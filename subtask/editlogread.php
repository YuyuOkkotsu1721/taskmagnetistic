<?php
require_once('../config.php');

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Check if subtaskID is passed in GET request
if (isset($_GET['subtaskID'])) {
    $subtaskID = mysqli_real_escape_string($link, $_GET['subtaskID']);
    $sql = "SELECT EditLogID, UserID, OriginalData, UpdatedData, EditTimestamp, EditFieldType FROM editlogs WHERE SubtaskID = '$subtaskID' ORDER BY EditTimestamp DESC";
    $result = mysqli_query($link, $sql);

    $createdLogExists = false; // Flag to track if a created log exists

    while ($row = mysqli_fetch_assoc($result)) {
        if (substr($row['EditLogID'], -3) === 'ed0') {
            $createdLogExists = true;
            $userID = $row['UserID'];
            $userQuery = "SELECT UserFirstName, UserLastName FROM user WHERE UserID = '$userID'";
            $userResult = mysqli_query($link, $userQuery);
            $userRow = mysqli_fetch_assoc($userResult);
            $userName = $userRow['UserFirstName'] . ' ' . $userRow['UserLastName'];
            $timestamp = strtotime($row['EditTimestamp']);
            $currentDate = strtotime(date('Y-m-d')); // Today's date
            $yesterdayDate = strtotime(date('Y-m-d', strtotime('-1 day'))); // Yesterday's date

            if ($timestamp >= $currentDate) {
                // If the edit is made today
                $timestampFormatted = 'Today at ' . date('g:i A', $timestamp);
            } elseif ($timestamp >= $yesterdayDate) {
                // If the edit is made yesterday
                $timestampFormatted = 'Yesterday at ' . date('g:i A', $timestamp);
            } else {
                // For other dates
                $timestampFormatted = 'on ' . date('M d, Y g:i A', $timestamp);
            }
            ?>
            <div class="border border-gray-300 border-opacity-50 p-4 flex justify-center">
            <p id="createdlog" class="text-white">Created <?php echo $timestampFormatted; ?> by <?php echo $userName; ?></p>
            </div>
            <?php
            break; // Assuming you want to display only the first created log
        }
    }

    mysqli_data_seek($result, 0); // Reset pointer to the beginning of the result set

    if (!$createdLogExists) {
        echo '<div class="border border-gray-300 border-opacity-50 p-4 flex justify-between hidden"></div>'; // Display blank if no created log
    }

    // Display edit log contents
    ?>
    <div id="editlogcontents">
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            if (substr($row['EditLogID'], -3) === 'ed0') {
                continue; // Skip created logs
            }

            // Format the EditTimestamp
            $editTimestamp = strtotime($row['EditTimestamp']);
            $currentDate = strtotime(date('Y-m-d')); // Today's date
            $yesterdayDate = strtotime(date('Y-m-d', strtotime('-1 day'))); // Yesterday's date

            if ($editTimestamp >= $currentDate) {
                // If the edit is made today
                $editTimestampFormatted = 'Today at ' . date('g:i A', $editTimestamp);
            } elseif ($editTimestamp >= $yesterdayDate) {
                // If the edit is made yesterday
                $editTimestampFormatted = 'Yesterday at ' . date('g:i A', $editTimestamp);
            } else {
                // For other dates
                $editTimestampFormatted = date('M d, Y g:i A', $editTimestamp);
            }

            // Retrieve user's first and last names based on UserID
            $userID = $row['UserID'];
            $userQuery = "SELECT UserFirstName, UserLastName FROM user WHERE UserID = '$userID'";
            $userResult = mysqli_query($link, $userQuery);
            $userRow = mysqli_fetch_assoc($userResult);
            $userName = $userRow['UserFirstName'] . ' ' . $userRow['UserLastName'];

            $originalData = str_replace('\r\n', '<br>', $row['OriginalData']);
            $updatedData = str_replace('\r\n', '<br>', $row['UpdatedData']);

            // Add a line break between original and updated data
            $updatedDataWithBreak = "<br>{$updatedData}";

            $editDescription = "Edited the " . $row['EditFieldType'] . " from <span class='text-blue-500'>" . $originalData . "</span> to <span class='text-green-500'>" . $updatedDataWithBreak . "</span>";
            ?>

            <div class="border border-gray-300 border-opacity-50 p-4 flex justify-between">
                <div class="w-4/5">
                    <p class="text-white"><?php echo $editDescription; ?></p>
                </div>
                <div>
                    <p><?php echo $editTimestampFormatted . " by " . $userName; ?></p>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php

    // Display message if no edits
    if (mysqli_num_rows($result) == 0 && !$createdLogExists) {
        echo '<div class="container mx-auto mt-60 p-6 md:p-12 text-center text-2xl text-white">You have no edits here.</div>';
    }
} else {
    echo "Subtask ID not provided.";
}

mysqli_close($link);
?>
