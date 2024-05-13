<?php
require_once('../config.php');
session_start();

if (isset($_SESSION["userID"])) {
    // Fetch buzz chats
    $taskID = $_GET['taskID'];

    $buzzNotifySQL = "SELECT b.UserID, s.SubtaskID, s.SubtaskTitle, s.SubtaskDescription, s.SubtaskDuration, s.SubtaskDifficulty, s.SubtaskCreationDate, s.SubtaskBackgroundColor, s.SubtaskTextColor, s.SubtaskStatus, s.SubtaskMarkNumber, s.SubtaskImage, s.SubtaskStartTime, s.SubtaskEndTime, s.SubtaskPausedDuration, bc.BuzzMessage
    FROM buzznotify AS b 
    INNER JOIN subtasks AS s ON b.SubtaskID = s.SubtaskID 
    LEFT JOIN (
        SELECT bc.UserID, bc.SubtaskID, bc.BuzzMessage
        FROM buzzchats AS bc
        INNER JOIN (
            SELECT UserID, SubtaskID, MAX(BuzzTimeStamp) AS LatestTimeStamp
            FROM buzzchats
            GROUP BY UserID, SubtaskID
        ) AS latest_msg ON bc.UserID = latest_msg.UserID AND bc.SubtaskID = latest_msg.SubtaskID AND bc.BuzzTimeStamp = latest_msg.LatestTimeStamp
    ) AS bc ON b.UserID = bc.UserID AND b.SubtaskID = bc.SubtaskID
    WHERE b.TaskID = '$taskID' 
    AND b.UserID != '" . $_SESSION['userID'] . "' 
    AND b.BuzzNotifiedStatus = 'false'";

    $buzzNotifyResult = mysqli_query($link, $buzzNotifySQL);

    $data = [];

    while ($row = mysqli_fetch_assoc($buzzNotifyResult)) {
        // Check if SubtaskPausedDuration is empty
        $pausedDuration = empty($row['SubtaskPausedDuration']) ? '00:00:00' : $row['SubtaskPausedDuration'];

        $data[] = [
            'SubtaskID' => $row['SubtaskID'],
            'SubtaskTitle' => $row['SubtaskTitle'],
            'BuzzMessage' => $row['BuzzMessage'],
            'SubtaskDescription' => $row['SubtaskDescription'],
            'SubtaskDuration' => $row['SubtaskDuration'],
            'SubtaskDifficulty' => $row['SubtaskDifficulty'],
            'SubtaskBackgroundColor' => $row['SubtaskBackgroundColor'],
            'SubtaskTextColor' => $row['SubtaskTextColor'],
            'SubtaskStatus' => $row['SubtaskStatus'],
            'SubtaskMarkNumber' => $row['SubtaskMarkNumber'],
            'SubtaskStartTime' => $row['SubtaskStartTime'],
            'SubtaskEndTime' => $row['SubtaskEndTime'],
            'SubtaskPausedDuration' => $pausedDuration,
            'SubtaskCreationDate' => $row['SubtaskCreationDate'],
            'SubtaskImage' => $row['SubtaskImage'],
            'UserID' => $row['UserID']

        ];
    }

    // Encode data as JSON
    echo json_encode($data);
}
?>
