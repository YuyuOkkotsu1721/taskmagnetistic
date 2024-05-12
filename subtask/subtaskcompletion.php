<?php
require_once('../config.php');

// Check if the taskid parameter is set and not empty
if (isset($_GET['taskid']) && !empty($_GET['taskid'])) {
    $taskID = mysqli_real_escape_string($link, $_GET['taskid']);
    $sql_total = "SELECT COUNT(*) AS total_subtasks FROM subtasks WHERE TaskID = '$taskID'";
    $sql_done = "SELECT COUNT(*) AS done_subtasks FROM subtasks WHERE TaskID = '$taskID' AND SubtaskStatus IN ('Done', 'Done (For Review)')";

    // Execute query to count total subtasks
    $result_total = mysqli_query($link, $sql_total);
    if ($result_total) {
        $row_total = mysqli_fetch_assoc($result_total);
        $total_subtasks = $row_total['total_subtasks'];
    } else {
        echo "Error: " . mysqli_error($link);
    }

    // Execute query to count done subtasks
    $result_done = mysqli_query($link, $sql_done);
    if ($result_done) {
        $row_done = mysqli_fetch_assoc($result_done);
        $done_subtasks = $row_done['done_subtasks'];
    } else {
        echo "Error: " . mysqli_error($link);
    }

    // Calculate completion indicator
    $completion_indicator = $done_subtasks . '/' . $total_subtasks;

    // Output completion indicator
    echo $completion_indicator;
} else {
    echo "Invalid request.";
}

// Close database connection
mysqli_close($link);
?>
