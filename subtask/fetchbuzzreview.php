<?php
require_once('../config.php');
session_start();

if (isset($_SESSION["userID"]) && isset($_GET['taskID'])) {
    $taskID = $_GET['taskID'];

    $doneForReviewSQL = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration  
    FROM subtasks 
    WHERE TaskID = '$taskID' AND SubtaskStatus = 'Done (For Review)'";

    $doneForReviewResult = mysqli_query($link, $doneForReviewSQL);
    $data = [];
    if (mysqli_num_rows($doneForReviewResult) > 0) {
        while ($row = mysqli_fetch_assoc($doneForReviewResult)) {
            // Encode each line break and other necessary data
            $row['SubtaskDescription'] = addslashes(str_replace(["\r\n", "\r", "\n"], '147linebreakbymatthew', $row['SubtaskDescription']));
            $data[] = $row;
        }
    }
    // Return the fetched data as JSON
    echo json_encode($data);
} else {
    echo json_encode([]); // Return an empty array if not properly accessed
}
?>
