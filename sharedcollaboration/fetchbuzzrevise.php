<?php
require_once('../config.php'); // Include the database configuration file
session_start(); // Start the session

// Ensure there is a valid session with a taskID
if (!isset($_SESSION['userID']) || !isset($_GET['taskID'])) {
    echo json_encode([]);
    exit;
}

$taskID = $_GET['taskID']; // Get the taskID from GET request

// SQL to fetch subtasks that are 'Pending (Revise)'
$pendingReviseSQL = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration
FROM subtasks 
WHERE TaskID = '$taskID' AND SubtaskStatus = 'Pending (Revise)'";

$result = mysqli_query($link, $pendingReviseSQL);
$data = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Process line breaks in subtask description
        $row['SubtaskDescription'] = str_replace("\r\n", '147linebreakbymatthew', $row['SubtaskDescription']);
        $row['SubtaskDescription'] = str_replace(["\r", "\n"], '147linebreakbymatthew', $row['SubtaskDescription']);
        $data[] = $row;
    }
}

// Return the data as JSON
echo json_encode($data);
?>
