<?php
require_once('../config.php');

// Check if collaboratorID, collaboratorUsername, collaboratorMaker, and TaskID are provided
if(isset($_POST['collaboratorID']) && isset($_POST['collaboratorUsername']) && isset($_POST['collaboratorMaker']) && isset($_POST['taskID'])) {
    // Sanitize inputs
    $collaboratorID = mysqli_real_escape_string($link, $_POST['collaboratorID']);
    $collaboratorUsername = mysqli_real_escape_string($link, $_POST['collaboratorUsername']);
    $collaboratorMaker = mysqli_real_escape_string($link, $_POST['collaboratorMaker']);
    $taskID = mysqli_real_escape_string($link, $_POST['taskID']); // Retrieve TaskID
    
    // Convert current date and time to Philippine local time (UTC+8)
    $currentDateTime = new DateTime();
    $currentDateTime->setTimezone(new DateTimeZone('Asia/Manila'));
    $currentDateTimeFormatted = $currentDateTime->format('Y-m-d H:i:s');

    // Check if the record already exists
    $checkSql = "SELECT * FROM collaborators WHERE CollaboratorID = '$collaboratorID' AND CollaboratorMember = '$collaboratorUsername'";
    $checkResult = mysqli_query($link, $checkSql);
    if(mysqli_num_rows($checkResult) == 0) {
        // Insert into collaborators table
        $insertSql = "INSERT INTO collaborators (CollaboratorID, CollaboratorMember, CollaboratorMaker, SharedDateTime, TaskID) VALUES ('$collaboratorID', '$collaboratorUsername', '$collaboratorMaker', '$currentDateTimeFormatted', '$taskID')"; // Include TaskID
        if(mysqli_query($link, $insertSql)) {
            echo "Collaborator added successfully.";
        } else {
            echo "Error: " . mysqli_error($link);
        }
    } else {
        echo "Collaborator already exists.";
    }
} else {
    echo "Collaborator ID, Collaborator Username, Collaborator Maker, or TaskID not provided.";
}

// Close connection
mysqli_close($link);

