<?php
require_once('../config.php');

// Check if required POST data is available
if (isset($_POST['SubtaskID'], $_POST['TaskID'], $_POST['UserID'])) {
    // Sanitize input
    $subtaskID = mysqli_real_escape_string($link, $_POST['SubtaskID']);
    $taskID = mysqli_real_escape_string($link, $_POST['TaskID']);
    $userID = mysqli_real_escape_string($link, $_POST['UserID']);

    // Update BuzzNotifiedStatus to 'true'
    $updateSQL = "UPDATE buzznotify SET BuzzNotifiedStatus = 'true' WHERE SubtaskID = '$subtaskID' AND TaskID = '$taskID' AND UserID = '$userID'";
    $updateResult = mysqli_query($link, $updateSQL);

    if ($updateResult) {
        echo "BuzzNotify status updated successfully.";
    } else {
        echo "Error updating BuzzNotify status: " . mysqli_error($link);
    }
} else {
    echo "Incomplete POST data.";
}
?>
