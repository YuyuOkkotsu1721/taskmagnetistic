<?php
require_once('../config.php');

if (isset($_POST['UserID'], $_POST['SubtaskID'], $_POST['BuzzMessage'], $_POST['TaskID'])) {
    $userID = mysqli_real_escape_string($link, $_POST['UserID']);
    $subtaskID = mysqli_real_escape_string($link, $_POST['SubtaskID']);
    $taskID = mysqli_real_escape_string($link, $_POST['TaskID']);
    $buzzMessage = mysqli_real_escape_string($link, str_replace(array("\r\n", "\r", "\n"), "147lnbrk", $_POST['BuzzMessage']));
    date_default_timezone_set('Asia/Manila');
    $buzzTimeStamp = date('Y-m-d H:i:s');

    $query = "SELECT UserID, COUNT(*) FROM buzznotify WHERE SubtaskID = '$subtaskID'";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_array($result);
    $count = $row[1];
    $existingUserID = $row[0];

    if ($count == 0) {
        $insertQuery = "INSERT INTO buzznotify (SubtaskID, UserID, TaskID, BuzzNotifiedStatus) VALUES ('$subtaskID', '$userID', '$taskID', 'false')";
        if (mysqli_query($link, $insertQuery)) {
            echo "New SubtaskID added successfully";
        } else {
            echo "Error adding SubtaskID: " . mysqli_error($link);
        }
    } else {
        $updateQuery = "UPDATE buzznotify SET BuzzNotifiedStatus = 'false'";
        if ($existingUserID !== $userID) {
            $updateQuery .= ", UserID = '$userID'";
        }
        $updateQuery .= " WHERE SubtaskID = '$subtaskID'";
        if (mysqli_query($link, $updateQuery)) {
            echo "BuzzNotifiedStatus updated successfully";
        } else {
            echo "Error updating BuzzNotifiedStatus: " . mysqli_error($link);
        }
    }

    $stmt = mysqli_prepare($link, "INSERT INTO buzzchats (SubtaskID, UserID, BuzzMessage, BuzzTimeStamp) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $subtaskID, $userID, $buzzMessage, $buzzTimeStamp);

        if (mysqli_stmt_execute($stmt)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($link);
    }
    mysqli_close($link);
} else {
    echo "Error: Missing data";
}
?>
