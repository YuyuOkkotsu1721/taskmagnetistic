<?php
require_once('../config.php');

// Check if the taskid parameter is set and not empty
if (isset($_GET['taskid']) && !empty($_GET['taskid'])) {
    $taskID = mysqli_real_escape_string($link, $_GET['taskid']);
    $sql_total = "SELECT COUNT(*) AS total_subtasks FROM subtasks WHERE TaskID = '$taskID'";
    $sql_done = "SELECT COUNT(*) AS done_subtasks FROM subtasks WHERE TaskID = '$taskID' AND SubtaskStatus = 'Done'";

    // Execute query to count total subtasks
    $result_total = mysqli_query($link, $sql_total);
    if ($result_total) {
        $row_total = mysqli_fetch_assoc($result_total);
        $total_subtasks = $row_total['total_subtasks'];
    } else {
        echo "Error: " . mysqli_error($link);
        exit; // Exit if there's an error
    }

    // Execute query to count done subtasks
    $result_done = mysqli_query($link, $sql_done);
    if ($result_done) {
        $row_done = mysqli_fetch_assoc($result_done);
        $done_subtasks = $row_done['done_subtasks'];
    } else {
        echo "Error: " . mysqli_error($link);
        exit; // Exit if there's an error
    }

    // Calculate completion indicator
    $completion_indicator = $done_subtasks . '/' . $total_subtasks;

    // If all subtasks are completed and there are subtasks
    if ($total_subtasks > 0 && $done_subtasks == $total_subtasks) {
        // Fetch the subtask with the latest SubtaskEndTime
        $sql_latest_subtask = "SELECT SubtaskEndTime FROM subtasks WHERE TaskID = '$taskID' ORDER BY SubtaskEndTime DESC LIMIT 1";
        $result_latest_subtask = mysqli_query($link, $sql_latest_subtask);
        if ($result_latest_subtask) {
            $row_latest_subtask = mysqli_fetch_assoc($result_latest_subtask);
            $latest_end_time = $row_latest_subtask['SubtaskEndTime'];

            // Format the latest end time
            $formatted_end_time = date("M j, Y h:i A", strtotime($latest_end_time));
            echo $formatted_end_time;
        } else {
            echo "Error: " . mysqli_error($link);
        }
    }
} else {
    echo "Invalid request.";
}

// Close database connection
mysqli_close($link);
?>
