<?php
require_once('../config.php');

// Check if TaskID is provided
if(isset($_GET['taskid'])) {
    // Sanitize the input to prevent SQL injection
    $taskID = mysqli_real_escape_string($link, $_GET['taskid']);

    // Prepare SQL query to fetch background color, text color, and due date from tasks table
    $sql = "SELECT TaskBackgroundColor, TaskTextColor, TaskDueDateTime FROM tasks WHERE TaskID = '$taskID'";
    $result = mysqli_query($link, $sql);

    if($result) {
        // Check if any row is returned
        if(mysqli_num_rows($result) > 0) {
            // Fetch the background color, text color, and due date
            $row = mysqli_fetch_assoc($result);
            $backgroundColor = $row['TaskBackgroundColor'];
            $textColor = $row['TaskTextColor'];
            $dueDateTime = $row['TaskDueDateTime'];

            // Fetch total allotted time from subtasks
            $sqlSubtasks = "SELECT SUM(SubtaskDuration) AS TotalAllottedTime FROM subtasks WHERE TaskID = '$taskID'";
            $resultSubtasks = mysqli_query($link, $sqlSubtasks);
            $totalAllottedTime = 0;
            if($resultSubtasks) {
                $rowSubtasks = mysqli_fetch_assoc($resultSubtasks);
                $totalAllottedTime = $rowSubtasks['TotalAllottedTime'];
            }

            // Fetch total completed time from subtasks with status 'Done'
            $sqlCompletedSubtasks = "SELECT 
                                        SEC_TO_TIME(SUM(
                                            IF(SubtaskPausedDuration IS NULL, 
                                                TIME_TO_SEC(TIMEDIFF(SubtaskEndTime, SubtaskStartTime)), 
                                                TIME_TO_SEC(SubtaskPausedDuration)
                                            )
                                        )) AS TotalCompletedTime 
                                    FROM subtasks 
                                    WHERE TaskID = '$taskID' AND SubtaskStatus = 'Done'";
            $resultCompletedSubtasks = mysqli_query($link, $sqlCompletedSubtasks);
            $totalCompletedTime = "00:00:00";
            if($resultCompletedSubtasks) {
                $rowCompletedSubtasks = mysqli_fetch_assoc($resultCompletedSubtasks);
                $totalCompletedTime = $rowCompletedSubtasks['TotalCompletedTime'];
            }

            // Output the data as JSON
            echo json_encode(array(
                "TaskBackgroundColor" => $backgroundColor, 
                "TaskTextColor" => $textColor,
                "TaskDueDateTime" => $dueDateTime,
                "TotalAllottedTime" => $totalAllottedTime,
                "TotalCompletedTime" => $totalCompletedTime
            ));
        } else {
            // TaskID not found in database
            echo json_encode(array("error" => "TaskID not found"));
        }
    } else {
        // Error executing query
        echo json_encode(array("error" => "Query execution failed"));
    }
} else {
    // TaskID parameter not provided
    echo json_encode(array("error" => "TaskID parameter not provided"));
}
?>
