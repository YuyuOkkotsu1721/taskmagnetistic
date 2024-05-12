<?php
require_once('../config.php');

// Check if subtaskID is set in the POST data
if(isset($_POST['subtaskID'])) {
    // Sanitize the subtaskID
    $subtaskID = mysqli_real_escape_string($link, $_POST['subtaskID']);

    // Prepare a SQL query to delete the subtask
    $sql = "DELETE FROM subtasks WHERE SubtaskID = '$subtaskID'";

    // Execute the query
    if(mysqli_query($link, $sql)) {
        // Additional query to delete records in editlogs table with the same subtaskID
        $deleteEditLogsSQL = "DELETE FROM editlogs WHERE SubtaskID = '$subtaskID'";
        if(mysqli_query($link, $deleteEditLogsSQL)) {
            echo "Subtask and associated edit logs deleted successfully.";
        } else {
            echo "Error deleting edit logs: " . mysqli_error($link);
        }
    } else {
        echo "Error deleting subtask: " . mysqli_error($link);
    }
} else {
    echo "Subtask ID not provided.";
}

// Close the connection
mysqli_close($link);


