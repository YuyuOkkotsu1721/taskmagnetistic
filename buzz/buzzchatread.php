<?php
require_once('../config.php');
session_start();

// Check if required POST data is available
if (isset($_GET['SubtaskID'])) {
    $subtaskID = $_GET['SubtaskID'];

    // Assuming you have a MySQL table named 'buzzchats'
    $sql = "SELECT * FROM buzzchats WHERE SubtaskID = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $subtaskID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Assuming your table has columns SubtaskID, UserID, BuzzTimeStamp, BuzzMessage
        while ($row = mysqli_fetch_assoc($result)) {
            // Format BuzzTimeStamp
            $buzzTimestamp = strtotime($row['BuzzTimeStamp']);
            $currentDate = strtotime(date('Y-m-d')); // Today's date
            $yesterdayDate = strtotime(date('Y-m-d', strtotime('-1 day'))); // Yesterday's date

            if ($buzzTimestamp >= $currentDate) {
                // If the buzz is made today
                $buzzTimestampFormatted = 'Today at ' . date('g:i A', $buzzTimestamp);
            } elseif ($buzzTimestamp >= $yesterdayDate) {
                // If the buzz is made yesterday
                $buzzTimestampFormatted = 'Yesterday at ' . date('g:i A', $buzzTimestamp);
            } else {
                // For other dates
                $buzzTimestampFormatted = date('M d, Y g:i A', $buzzTimestamp);
            }

            // Retrieve UserName based on UserID
            $userID = $row['UserID'];
            $userQuery = "SELECT UserFirstName, UserLastName FROM user WHERE UserID = '$userID'";
            $userResult = mysqli_query($link, $userQuery);
            $userRow = mysqli_fetch_assoc($userResult);
            $userName = $userRow['UserFirstName'] . ' ' . $userRow['UserLastName'];

            // Replace "147lnbrk" with line break in BuzzMessage
            $buzzMessageFormatted = stripslashes(str_replace('147lnbrk', '<br>', $row['BuzzMessage']));

            // Determine if the current user is the same as the UserID in the row
            $currentUserID = $_SESSION["userID"];
            $justifyClass = ($currentUserID == $userID) ? "justify-end" : "justify-start";
            $bgColorClass = ($currentUserID == $userID) ? "bg-green-200" : "bg-blue-200";

            ?>
            <div id="chatbubble" class="mb-2 flex <?php echo $justifyClass; ?> mr-2 ml-2">
                <div id="chatbg" class="rounded-lg p-2 max-w-xs <?php echo $bgColorClass; ?> overflow-hidden break-words ">
                    <div class="text-gray-600"><?php echo $userName; ?>:</div>
                    <div class="text-gray-800"><?php echo $buzzMessageFormatted; ?></div>
                    <div class="text-gray-600"><?php echo $buzzTimestampFormatted; ?></div>
                </div>
            </div>
            <?php
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>
