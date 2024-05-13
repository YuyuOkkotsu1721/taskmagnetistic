<?php
require_once('../config.php');

// Check if subtaskID is set in the POST data
if (isset($_POST['subtaskID'])) {
    // Sanitize the subtaskID
    $subtaskID = mysqli_real_escape_string($link, $_POST['subtaskID']);

    // Retrieve userID from POST data and sanitize it
    $userID = mysqli_real_escape_string($link, $_POST['userID']);

    $taskID = mysqli_real_escape_string($link, $_POST['taskID']);


    // Sanitize other inputs
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $duration = mysqli_real_escape_string($link, $_POST['duration']);
    $difficulty = mysqli_real_escape_string($link, $_POST['difficulty']);
    $status = mysqli_real_escape_string($link, $_POST['status']);

    // Sanitize and retrieve bgColor and textColor
    $bgColor = mysqli_real_escape_string($link, $_POST['bgColor']);
    $textColor = mysqli_real_escape_string($link, $_POST['textColor']);

    // Sanitize and retrieve markNumber
    $markNumber = mysqli_real_escape_string($link, $_POST['markNumber']);

    // Prepare a SQL query to update the subtask, excluding the image
    $sql = "UPDATE subtasks SET SubtaskTitle = '$title', SubtaskDescription = '$description', SubtaskDuration = '$duration', SubtaskDifficulty = '$difficulty', SubtaskStatus = '$status', SubtaskBackgroundColor = '$bgColor', SubtaskTextColor = '$textColor', SubtaskMarkNumber = '$markNumber'";

    // Add condition to update SubtaskStartTime if status is being updated to "Ongoing"
    if ($status === "Ongoing") {
        // Check if SubtaskStartTime is empty
        $sqlStartTimeCheck = "SELECT SubtaskStartTime FROM subtasks WHERE SubtaskID = '$subtaskID'";
        $resultStartTimeCheck = mysqli_query($link, $sqlStartTimeCheck);
        $rowStartTimeCheck = mysqli_fetch_assoc($resultStartTimeCheck);
        if (empty($rowStartTimeCheck['SubtaskStartTime']) || $rowStartTimeCheck['SubtaskStartTime'] === '0000-00-00 00:00:00') {
            // Set Manila time zone
            date_default_timezone_set('Asia/Manila');
            // Get current time in Manila time zone
            $subtaskStartTime = date('Y-m-d H:i:s');
            $sql .= ", SubtaskStartTime = '$subtaskStartTime'";
        }
    }

    // Add condition to update SubtaskEndTime if status is being updated to "Done"
    if ($status === "Done") {
        // Check if SubtaskEndTime is empty
        $sqlEndTimeCheck = "SELECT SubtaskEndTime FROM subtasks WHERE SubtaskID = '$subtaskID'";
        $resultEndTimeCheck = mysqli_query($link, $sqlEndTimeCheck);
        $rowEndTimeCheck = mysqli_fetch_assoc($resultEndTimeCheck);
        if (empty($rowEndTimeCheck['SubtaskEndTime']) || $rowEndTimeCheck['SubtaskEndTime'] === '0000-00-00 00:00:00') {
            // Set Manila time zone
            date_default_timezone_set('Asia/Manila');
            // Get current time in Manila time zone
            $subtaskEndTime = date('Y-m-d H:i:s');
            $sql .= ", SubtaskEndTime = '$subtaskEndTime'";

            // Add code to clear or remove SubtaskMarkNumber
            $sql .= ", SubtaskMarkNumber = NULL"; // or 0 depending on the column type

            echo "<script>window.location.reload(true);</script>";
        }
    }

// Query to select subtasks with the same taskID and non-empty SubtaskMarkNumber
$sqlSubtasksWithMarkNumber = "SELECT * FROM subtasks WHERE TaskID = '$taskID' AND SubtaskMarkNumber IS NOT NULL AND SubtaskMarkNumber <> '' ORDER BY CAST(SubtaskMarkNumber AS UNSIGNED) ASC";
$resultSubtasksWithMarkNumber = mysqli_query($link, $sqlSubtasksWithMarkNumber);

// Check if there are subtasks with non-empty SubtaskMarkNumber
if (mysqli_num_rows($resultSubtasksWithMarkNumber) > 0) {
// Iterate through the results and echo relevant information
while ($row = mysqli_fetch_assoc($resultSubtasksWithMarkNumber)) {
    echo "Subtask ID: ". $row['SubtaskID']. ", Mark Number: ". $row['SubtaskMarkNumber']. "\n";
}
echo "Updated SubtaskID: ". $subtaskID. " Updated Mark Number: ". $markNumber. "\n";

// Query to select the old mark number
$sqlOldMarkNumber = "SELECT SubtaskMarkNumber FROM subtasks WHERE SubtaskID = '$subtaskID'";
$resultOldMarkNumber = mysqli_query($link, $sqlOldMarkNumber);
$rowOld = mysqli_fetch_assoc($resultOldMarkNumber);
$oldMarkNumber = $rowOld['SubtaskMarkNumber'];

echo "Old Mark Number: ". $oldMarkNumber. "\n";

// Now, update the SubtaskMarkNumber for subtasks that meet the criteria
mysqli_data_seek($resultSubtasksWithMarkNumber, 0); // Reset pointer to start of result set
// Start the loop over fetched rows
while ($row = mysqli_fetch_assoc($resultSubtasksWithMarkNumber)) {
    // Check if both $markNumber and $row['SubtaskMarkNumber'] are set and not null
    if (isset($markNumber, $row['SubtaskMarkNumber'])) {
        // Now proceed with specific conditions when both values are set
        if ($row['SubtaskMarkNumber'] == $markNumber || ($row['SubtaskMarkNumber'] > $markNumber && $row['SubtaskMarkNumber'] < $oldMarkNumber)) {
            $newMarkNumber = $row['SubtaskMarkNumber'] + 1;
            $subtaskIDToUpdate = $row['SubtaskID'];
            
            // Update the SubtaskMarkNumber in the database
            $updateQuery = "UPDATE subtasks SET SubtaskMarkNumber = '$newMarkNumber' WHERE SubtaskID = '$subtaskIDToUpdate'";
            if (mysqli_query($link, $updateQuery)) {
                echo "Subtask ID: " . $subtaskIDToUpdate . " updated to Mark Number: " . $newMarkNumber . "\n";
            } else {
                echo "Failed to update Subtask ID: " . $subtaskIDToUpdate . " - Error: " . mysqli_error($link) . "\n";
            }
        }
    }
}


} else {
    echo "No subtasks found with non-empty SubtaskMarkNumber for this task.\n";
}



    if (in_array($status, ["Pending (Short-Term)", "Pending (Long-Term)", "Pending (To-Follow)", "Pending"])) {
        $sqlStartTimeCheck = "SELECT SubtaskStartTime, SubtaskStatus FROM subtasks WHERE SubtaskID = '$subtaskID'";
        $resultStartTimeCheck = mysqli_query($link, $sqlStartTimeCheck);
        $rowStartTimeCheck = mysqli_fetch_assoc($resultStartTimeCheck);
        $currentStatus = $rowStartTimeCheck['SubtaskStatus'];

        if (!empty($rowStartTimeCheck['SubtaskStartTime']) && $currentStatus === "Ongoing") {
            $sqlPausedDuration = "SELECT TIMEDIFF(NOW(), SubtaskStartTime) AS PausedDuration FROM subtasks WHERE SubtaskID = '$subtaskID'";
            $resultPausedDuration = mysqli_query($link, $sqlPausedDuration);
            $rowPausedDuration = mysqli_fetch_assoc($resultPausedDuration);
            $pausedDurationFormatted = $rowPausedDuration['PausedDuration'];

            $sqlPausedDurationCheck = "SELECT SubtaskPausedDuration FROM subtasks WHERE SubtaskID = '$subtaskID'";
            $resultPausedDurationCheck = mysqli_query($link, $sqlPausedDurationCheck);
            $rowPausedDurationCheck = mysqli_fetch_assoc($resultPausedDurationCheck);

            if (!is_null($rowPausedDurationCheck['SubtaskPausedDuration'])) {
                // Convert both durations to seconds
                $pausedDurationSeconds = strtotime($pausedDurationFormatted) - strtotime('00:00:00');
                $subtaskPausedDurationSeconds = strtotime($rowPausedDurationCheck['SubtaskPausedDuration']) - strtotime('00:00:00');

                // Add the durations together
                $totalDurationSeconds = $pausedDurationSeconds + $subtaskPausedDurationSeconds;

                // Convert the total duration back to a time format
                $totalDuration = gmdate("H:i:s", $totalDurationSeconds);

                echo "Paused Duration: " . $pausedDurationFormatted . "<br>";
                echo "Subtask Paused Duration: " . $rowPausedDurationCheck['SubtaskPausedDuration'] . "<br>";
                echo "Total Duration: " . $totalDuration;
                $sql .= ", SubtaskPausedDuration = '$totalDuration'";
                $sql .= ", SubtaskStartTime = '0000-00-00 00:00:00'";
            } else {
                echo "Paused Duration: " . $pausedDurationFormatted;
                $sql .= ", SubtaskPausedDuration = '$pausedDurationFormatted'";
                $sql .= ", SubtaskStartTime = '0000-00-00 00:00:00'";
            }
        }
    }

    function getNextEditLogID($link, $subtaskID)
    {
        $query = "SELECT MAX(CAST(SUBSTRING(EditLogID, LOCATE('ed', EditLogID) + 2) AS UNSIGNED)) AS max_id FROM editlogs WHERE SubtaskID = ?";
        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $subtaskID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $max_id);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            if ($max_id) {
                return $subtaskID . 'ed' . ($max_id + 1);
            } else {
                return $subtaskID . 'ed1';
            }
        } else {
            return false;
        }
    }

    
    

    // Retrieve the current status of the subtask
    $sqlCurrentStatusCheck = "SELECT SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskStatus, SubtaskBackgroundColor, SubtaskTextColor, SubtaskMarkNumber FROM subtasks WHERE SubtaskID = '$subtaskID'";
    $resultCurrentStatus = mysqli_query($link, $sqlCurrentStatusCheck);
    $currentStatusData = mysqli_fetch_assoc($resultCurrentStatus);

    // Store original data
    $originalTitle = $currentStatusData['SubtaskTitle'];
    $originalDescription = $currentStatusData['SubtaskDescription'];
    $originalDuration = $currentStatusData['SubtaskDuration'];
    $originalDifficulty = $currentStatusData['SubtaskDifficulty'];
    $originalStatus = $currentStatusData['SubtaskStatus'];
    $originalBgColor = $currentStatusData['SubtaskBackgroundColor'];
    $originalTextColor = $currentStatusData['SubtaskTextColor'];
    $originalMarkNumber = $currentStatusData['SubtaskMarkNumber'];

    // After updating, compare and log differences
    date_default_timezone_set('Asia/Manila');

    $editTimestamp = date('Y-m-d H:i:s');
    $logData = array(
        "title" => array("original" => $originalTitle, "updated" => $title, "field" => "SubtaskTitle"),
        "description" => array("original" => $originalDescription, "updated" => $description, "field" => "SubtaskDescription"),
        "duration" => array("original" => $originalDuration, "updated" => $duration, "field" => "SubtaskDuration"),
        "difficulty" => array("original" => $originalDifficulty, "updated" => $difficulty, "field" => "SubtaskDifficulty"),
        "status" => array("original" => $originalStatus, "updated" => $status, "field" => "SubtaskStatus"),
        "bgColor" => array("original" => $originalBgColor, "updated" => $bgColor, "field" => "SubtaskBackgroundColor"),
        "textColor" => array("original" => $originalTextColor, "updated" => $textColor, "field" => "SubtaskTextColor"),
        "markNumber" => array("original" => $originalMarkNumber, "updated" => $markNumber, "field" => "SubtaskMarkNumber")
    );

    foreach ($logData as $key => $value) {
        date_default_timezone_set('Asia/Manila');

        if ($value['original'] !== $value['updated']) {
            $originalData = mysqli_real_escape_string($link, $value['original']);
            $updatedData = mysqli_real_escape_string($link, $value['updated']);
            $fieldType = $value['field'];
            
            // Generate EditLogID
            $editLogID = getNextEditLogID($link, $subtaskID);
            
            $sqlEditLogs = "INSERT INTO editlogs (EditLogID, SubtaskID, UserID, OriginalData, UpdatedData, EditTimestamp, EditFieldType) VALUES ('$editLogID', '$subtaskID', '$userID', '$originalData', '$updatedData', '$editTimestamp', '$fieldType')";
            mysqli_query($link, $sqlEditLogs);
        }
    }
    


    // Retrieve the current status of the subtask
    $sqlCurrentStatusCheck = "SELECT SubtaskStatus, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration FROM subtasks WHERE SubtaskID = '$subtaskID'";
    $resultCurrentStatus = mysqli_query($link, $sqlCurrentStatusCheck);
    $currentStatusData = mysqli_fetch_assoc($resultCurrentStatus);

    // Handling changes from "Done" to "Ongoing" or "Pending"
    if ($currentStatusData['SubtaskStatus'] === "Done" && in_array($status, ["Ongoing", "Pending (Short-Term)", "Pending (Long-Term)", "Pending (To-Follow)", "Pending"])) {
        $startTime = $currentStatusData['SubtaskStartTime'];
        $endTime = $currentStatusData['SubtaskEndTime'];
        $pausedDuration = $currentStatusData['SubtaskPausedDuration'];

        // Calculate the duration between start and end times
        $activeDuration = strtotime($endTime) - strtotime($startTime);

        // Convert the active duration to hours, minutes, and seconds format
        $activeDurationFormatted = gmdate("H:i:s", $activeDuration);

        if (!empty($pausedDuration)) {
            // Add the active duration to the existing paused duration
            $pausedDurationSeconds = strtotime($pausedDuration) - strtotime("00:00:00");
            $activeDurationSeconds = strtotime($activeDurationFormatted) - strtotime("00:00:00");
            $totalPausedDurationSeconds = $pausedDurationSeconds + $activeDurationSeconds;
            $totalPausedDurationFormatted = gmdate("H:i:s", $totalPausedDurationSeconds);
        } else {
            $totalPausedDurationFormatted = $activeDurationFormatted;
        }

        // Set Manila time zone
        date_default_timezone_set('Asia/Manila');
        // Get current time in Manila time zone
        $currentDateTime = date('Y-m-d H:i:s');

        $sql .= ", SubtaskPausedDuration = '$totalPausedDurationFormatted', SubtaskStartTime = '$currentDateTime', SubtaskEndTime = '0000-00-00 00:00:00'";
    }


    $sql .= " WHERE SubtaskID = '$subtaskID'";

    // Execute the query to update non-image fields
    if (mysqli_query($link, $sql)) {
        // Handle uploaded image only if 'image' key exists in the $_FILES array
        if (isset($_FILES['image'])) {
            // Retrieve image data
            $image = $_FILES['image'];
            $imageName = $image['name'];
            $imageTmpName = $image['tmp_name'];
            $imageError = $image['error'];


            // Check if image upload was successful
            if ($imageError === UPLOAD_ERR_OK) {
                // Move the uploaded image to the target directory
                $uploadPath =  $imageName;
                

                if (move_uploaded_file($imageTmpName, $uploadPath)) {
                    // Prepare a SQL query to update the subtask image
                    $sqlImageUpdate = "UPDATE subtasks SET SubtaskImage = '$uploadPath' WHERE SubtaskID = '$subtaskID'";

                    // Execute the query to update the image
                    if (mysqli_query($link, $sqlImageUpdate)) {
                        echo "Subtask updated successfully.";
                    } else {
                        echo "Error updating subtask image: " . mysqli_error($link);
                    }
                } else {
                    echo "Error moving uploaded image to destination directory.";
                }
            } else {
                echo "Error uploading image: " . $imageError;
            }
        } else {
            echo "No image uploaded."; // Inform the user that no image was uploaded
        }
    } else {
        echo "Error updating subtask: " . mysqli_error($link);
    }
} else {
    echo "Subtask ID not provided.";
}

// Close the connection
mysqli_close($link);
