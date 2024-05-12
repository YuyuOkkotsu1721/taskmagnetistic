<?php
// Include your database configuration file
require_once('../config.php');

if(isset($_POST['subtaskID']) && isset($_POST['AssignedTo'])) {
    // Sanitize input
    $subtaskID = mysqli_real_escape_string($link, $_POST['subtaskID']);
    $AssignedTo = mysqli_real_escape_string($link, $_POST['AssignedTo']);

    // Prepare and execute SQL query to delete the collaborator record
    $sql = "DELETE FROM subtaskaccess WHERE SubtaskID = '$subtaskID' AND AssignedTo = '$AssignedTo'";
    if(mysqli_query($link, $sql)) {
        echo "Access record deleted successfully.";
    } else {
        echo "Error deleting access record: " . mysqli_error($link);
    }
} else {
    echo "Invalid request.";
}

// Close database connection
mysqli_close($link);

