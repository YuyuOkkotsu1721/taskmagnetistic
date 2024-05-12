<?php
// Include your database configuration file
require_once('../config.php');

// Check if collaboratorID, collaboratorManager, and taskID are provided via POST
if(isset($_POST['collaboratorID']) && isset($_POST['collaboratorManager']) && isset($_POST['taskID'])) {
    // Sanitize input
    $collaboratorID = mysqli_real_escape_string($link, $_POST['collaboratorID']);
    $collaboratorManager = mysqli_real_escape_string($link, $_POST['collaboratorManager']);
    $taskID = mysqli_real_escape_string($link, $_POST['taskID']);

    // Start a transaction to ensure atomicity
    mysqli_begin_transaction($link);
    
    // Prepare and execute SQL query to delete the collaborator record
    $sqlDeleteCollaborator = "DELETE FROM collaborators WHERE CollaboratorID = '$collaboratorID' AND CollaboratorManager = '$collaboratorManager'";
    
    // Prepare and execute SQL query to delete records from subtaskaccess table
    $sqlDeleteSubtaskAccess = "DELETE FROM subtaskaccess WHERE TaskID = '$taskID' AND AssignedTo = '$collaboratorManager'";
    
    $error = false;
    if(mysqli_query($link, $sqlDeleteCollaborator) && mysqli_query($link, $sqlDeleteSubtaskAccess)) {
        mysqli_commit($link); // Commit transaction if both queries succeed
        echo "Collaborator record and related subtaskaccess records deleted successfully.";
    } else {
        mysqli_rollback($link); // Rollback transaction if any query fails
        echo "Error deleting collaborator record or related subtaskaccess records: " . mysqli_error($link);
    }
} else {
    echo "Invalid request.";
}

// Close database connection
mysqli_close($link);
?>
