<?php
require_once('../config.php');
session_start();

// Function to get the next available TaskID for a particular venture
function getNextTaskID($link, $ventureID) {
    $query = "SELECT MAX(CAST(SUBSTRING(TaskID, LOCATE('t', TaskID) + 1) AS UNSIGNED)) AS max_id FROM tasks WHERE VentureID = ?";
    if($stmt = mysqli_prepare($link, $query)){
        mysqli_stmt_bind_param($stmt, "s", $ventureID);
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
    // Validate task title, task description, priority level, due date, venture ID, and colors
    if (empty(trim($_POST["taskTitle"])) || empty(trim($_POST["taskDescription"])) || empty(trim($_POST["priorityLevel"])) || empty(trim($_POST["ventureID"])) || empty(trim($_POST["dueDate"])) || empty(trim($_POST["bgColor"])) || empty(trim($_POST["textColor"]))) {
        echo "All fields including title, description, priority level, due date, background color, and text color are required.";
    } else {
        $taskTitle = trim($_POST["taskTitle"]);
        $taskDescription = trim($_POST["taskDescription"]);
        $priorityLevel = trim($_POST["priorityLevel"]);
        $ventureID = trim($_POST["ventureID"]);
        $dueDate = date('Y-m-d H:i:s', strtotime($_POST['dueDate'])); // Format due date for MySQL
        $bgColor = trim($_POST["bgColor"]);
        $textColor = trim($_POST["textColor"]);

        // Get the next available TaskID
        $nextTaskID = getNextTaskID($link, $ventureID);
        if ($nextTaskID === false) {
            echo "Error occurred while fetching next TaskID.";
            exit;
        }
        
        // Generate TaskID by concatenating ventureID and nextTaskID
        $taskID = $ventureID . "t" . $nextTaskID;

        // Prepare an insert statement
        $sql = "INSERT INTO tasks (TaskID, TaskTitle, TaskDescription, TaskPriority, VentureID, TaskDueDateTime, TaskCreationDate, TaskBackgroundColor, TaskTextColor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssssss", $param_TaskID, $param_TaskTitle, $param_TaskDescription, $param_TaskPriority, $param_VentureID, $param_TaskDueDateTime, $param_TaskCreationDate, $param_TaskBackgroundColor, $param_TaskTextColor);

            // Set parameters
            $param_TaskID = $taskID;
            $param_TaskTitle = $taskTitle;
            $param_TaskDescription = $taskDescription;
            $param_TaskPriority = $priorityLevel;
            $param_VentureID = $ventureID;
            $param_TaskDueDateTime = $dueDate;
            $param_TaskBackgroundColor = $bgColor;
            $param_TaskTextColor = $textColor;
            
            // Convert current date and time to Philippine local time (UTC+8)
            $currentDateTime = new DateTime();
            $currentDateTime->setTimezone(new DateTimeZone('Asia/Manila'));
            $param_TaskCreationDate = $currentDateTime->format('Y-m-d H:i:s'); // Philippine local time

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                echo "Task saved successfully.";
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
} else {
    echo "No POST request received.";
}
