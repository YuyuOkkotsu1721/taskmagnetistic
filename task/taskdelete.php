<?php
require_once('../config.php');

// Check if taskID parameter is set in the POST request
if(isset($_POST['taskID'])) {
    // Sanitize the taskID
    $taskID = mysqli_real_escape_string($link, $_POST['taskID']);

    // Prepare a SQL query to delete the task based on the taskID
    $sql = "DELETE FROM tasks WHERE TaskID = '$taskID'";

    // Execute the query
    if (mysqli_query($link, $sql)) {
        echo "Task deleted successfully";
    } else {
        echo "Error deleting task: " . mysqli_error($link);
    }
} else {
    echo 'Task ID not provided.';
}

// Close connection
mysqli_close($link);
?>
