<?php
include 'config.php'; // Include your DB configuration

// Check if required POST data is available
if (isset($_POST['SubtaskID'], $_POST['TaskID'], $_POST['AssignedTo'], $_POST['AccessOption'])) {
    // Sanitize the POST data
    $subtaskID = mysqli_real_escape_string($link, $_POST['SubtaskID']);
    $taskID = mysqli_real_escape_string($link, $_POST['TaskID']);
    $assignedTo = mysqli_real_escape_string($link, $_POST['AssignedTo']);
    $accessOption = mysqli_real_escape_string($link, $_POST['AccessOption']);

    // Check if the combination already exists
    $checkQuery = "SELECT * FROM subtaskaccess WHERE SubtaskID = '$subtaskID' AND TaskID = '$taskID' AND AssignedTo = '$assignedTo' AND AccessOption = '$accessOption'";
    $checkResult = mysqli_query($link, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "Access already granted for this combination.";
    } else {
        // Insert data into the subtaskaccess table
        $insertQuery = "INSERT INTO subtaskaccess (SubtaskID, TaskID, AssignedTo, AccessOption) VALUES ('$subtaskID', '$taskID', '$assignedTo', '$accessOption')";
        
        if (mysqli_query($link, $insertQuery)) {
            echo "Access granted successfully.";
        } else {
            echo "Error: " . mysqli_error($link);
        }
    }
} else {
    echo "Required data is missing.";
}

mysqli_close($link);

