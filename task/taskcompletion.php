<?php
require_once('../config.php');

// Check if the ventureid parameter is set and not empty
if (isset($_GET['ventureid']) && !empty($_GET['ventureid'])) {
    $ventureID = mysqli_real_escape_string($link, $_GET['ventureid']);

    // SQL query to count total tasks under the venture
    $sql_total_tasks = "SELECT COUNT(*) AS total_tasks FROM tasks WHERE VentureID = '$ventureID'";

    // SQL query to count tasks with all subtasks completed
    $sql_completed_tasks = "SELECT COUNT(*) AS completed_tasks FROM tasks t 
                            WHERE t.VentureID = '$ventureID' AND (
                                SELECT COUNT(*) FROM subtasks s WHERE s.TaskID = t.TaskID AND s.SubtaskStatus = 'Done'
                            ) = (
                                SELECT COUNT(*) FROM subtasks s WHERE s.TaskID = t.TaskID
                            ) AND (
                                SELECT COUNT(*) FROM subtasks s WHERE s.TaskID = t.TaskID AND s.SubtaskStatus = 'Done'
                            ) > 0";

    // Execute query to count total tasks
    $result_total_tasks = mysqli_query($link, $sql_total_tasks);
    if ($result_total_tasks) {
        $row_total_tasks = mysqli_fetch_assoc($result_total_tasks);
        $total_tasks = $row_total_tasks['total_tasks'];
    } else {
        echo "Error: " . mysqli_error($link);
    }

    // Execute query to count completed tasks
    $result_completed_tasks = mysqli_query($link, $sql_completed_tasks);
    if ($result_completed_tasks) {
        $row_completed_tasks = mysqli_fetch_assoc($result_completed_tasks);
        $completed_tasks = $row_completed_tasks['completed_tasks'];
    } else {
        echo "Error: " . mysqli_error($link);
    }

    // Calculate completion indicator
    $completion_indicator = $completed_tasks . '/' . $total_tasks;

    // Output completion indicator
    echo $completion_indicator;
} else {
    echo "Invalid request.";
}

// Close database connection
mysqli_close($link);
?>
