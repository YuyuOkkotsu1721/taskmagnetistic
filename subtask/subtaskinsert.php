<?php
require_once('../config.php');
session_start();

if (isset($_SESSION["userID"])) {

    // Function to get the next available SubtaskID for a particular task
    function getNextSubtaskID($link, $taskID)
    {
        $query = "SELECT MAX(CAST(SUBSTRING(SubtaskID, LOCATE('s', SubtaskID) + 1) AS UNSIGNED)) AS max_id FROM subtasks WHERE TaskID =?";
        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $taskID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $max_id);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            if ($max_id) {
                return $max_id + 1;
            } else {
                return 1;
            }
        } else {
            return false;
        }
    }

    // Check if the request is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate subtask title, subtask description, duration, difficulty level, and task ID
        if (empty(trim($_POST["subtaskTitle"])) || empty(trim($_POST["subtaskDescription"])) || empty(trim($_POST["subtaskDuration"])) || empty(trim($_POST["subtaskDifficulty"])) || empty(trim($_POST["taskid"]))) {
            echo "Subtask title, subtask description, duration, difficulty level, and task ID cannot be blank.";
        } else {
            $subtaskTitle = trim($_POST["subtaskTitle"]);
            $subtaskDescription = trim($_POST["subtaskDescription"]);
            $subtaskDuration = trim($_POST["subtaskDuration"]);
            $subtaskDifficulty = trim($_POST["subtaskDifficulty"]);
            $bgColor = trim($_POST["bgColor"]);
            $textColor = trim($_POST["textColor"]);
            $taskID = trim($_POST["taskid"]);

            // Get the next available SubtaskID
            $nextSubtaskID = getNextSubtaskID($link, $taskID);
            if ($nextSubtaskID === false) {
                echo "Error occurred while fetching next SubtaskID.";
                exit;
            }

            // Generate SubtaskID by concatenating taskID and nextSubtaskID
            $subtaskID = $taskID. "s". $nextSubtaskID;

            // Set default values for SubtaskStatus and SubtaskCreationDate
            $subtaskStatus = "Pending";
            // Set Manila time zone
            date_default_timezone_set('Asia/Manila');

            // Get current time in Manila time zone
            $subtaskCreationDate = date('Y-m-d H:i:s');

            // Prepare an insert statement for subtasks
            $sql = "INSERT INTO subtasks (SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, TaskID, SubtaskStatus, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor) VALUES (?,?,?,?,?,?,?,?,?,?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssssssss", $param_SubtaskID, $param_SubtaskTitle, $param_SubtaskDescription, $param_SubtaskDuration, $param_SubtaskDifficulty, $param_TaskID, $param_SubtaskStatus, $param_SubtaskCreationDate, $param_BgColor, $param_TextColor);

                // Set parameters
                $param_SubtaskID = $subtaskID;
                $param_SubtaskTitle = $subtaskTitle;
                $param_SubtaskDescription = $subtaskDescription;
                $param_SubtaskDuration = $subtaskDuration;
                $param_SubtaskDifficulty = $subtaskDifficulty;
                $param_TaskID = $taskID;
                $param_SubtaskStatus = $subtaskStatus;
                $param_SubtaskCreationDate = $subtaskCreationDate;
                $param_BgColor = $bgColor;
                $param_TextColor = $textColor;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Prepare an insert statement for editlogs
                    $sqlEditLogs = "INSERT INTO editlogs (EditLogID, SubtaskID, UserID, EditTimestamp) VALUES (?,?,?,?)";
                    if ($stmtEditLogs = mysqli_prepare($link, $sqlEditLogs)) {
                        // Generate EditLogID by appending "ed0" to SubtaskID
                        $editLogID = $subtaskID. "ed0";

                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmtEditLogs, "ssss", $editLogID, $subtaskID, $_SESSION["userID"], $subtaskCreationDate);

                        // Attempt to execute the prepared statement for editlogs
                        if (mysqli_stmt_execute($stmtEditLogs)) {
                            echo "success";
                            echo "edo";
                        } else {
                            echo "error";
                        }

                        // Close statement for editlogs
                        mysqli_stmt_close($stmtEditLogs);
                    }
                }

                // Close statement for subtasks
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        echo '<script>window.location.href = "login.html";</script>';
        echo "Please log in to view subtasks.";
    }
    // Close connection
    mysqli_close($link);
} else {
    echo "No POST request received.";
}
