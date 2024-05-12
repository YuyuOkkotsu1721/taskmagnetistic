<?php
require_once('../config.php');
session_start();

if (isset($_SESSION["userID"])) {
    if (isset($_GET['taskid']) && !empty($_GET['taskid'])) {
        $taskID = mysqli_real_escape_string($link, $_GET['taskid']);
        $statusFilter = isset($_GET['status']) ? mysqli_real_escape_string($link, $_GET['status']) : '';
        $sortBy = isset($_GET['sortBy']) ? mysqli_real_escape_string($link, $_GET['sortBy']) : '';

        // Check if the taskid exists in the collaborators table
        $collaboratorCheckSQL = "SELECT EXISTS(SELECT 1 FROM collaborators WHERE TaskID = '$taskID') AS IsCollaborator";
        $collaboratorResult = mysqli_query($link, $collaboratorCheckSQL);
        if ($collaboratorResult && $row = mysqli_fetch_assoc($collaboratorResult)) {
            $clbstatus = ($row['IsCollaborator'] == 1) ? true : false;
        } else {
            $clbstatus = false;
            $sql = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration FROM subtasks WHERE TaskID = '$taskID'";

        }


        // Prepare SQL query to fetch background color and text color from tasks table
        $colorQuery = "SELECT TaskBackgroundColor, TaskTextColor FROM tasks WHERE TaskID = '$taskID'";
        $colorResult = mysqli_query($link, $colorQuery);

        // Fetch the colors from the result
        $colorRow = mysqli_fetch_assoc($colorResult);
        $taskBackgroundColor = $colorRow['TaskBackgroundColor'];
        $taskTextColor = $colorRow['TaskTextColor'];



        // Initialize $currentuserclb
        $currentuserclb = '';

        // Display $currentuserclb only if $clbstatus is true
        if ($clbstatus) {
            // Search collaborators table using taskid and username
            $collaboratorSearchSQLMaker = "SELECT * FROM collaborators WHERE TaskID = '$taskID' AND CollaboratorMaker = '{$_SESSION['username']}'";
            $collaboratorSearchResultMaker = mysqli_query($link, $collaboratorSearchSQLMaker);

            $collaboratorSearchSQLMember = "SELECT * FROM collaborators WHERE TaskID = '$taskID' AND CollaboratorMember = '{$_SESSION['username']}'";
            $collaboratorSearchResultMember = mysqli_query($link, $collaboratorSearchSQLMember);

            $collaboratorSearchSQLManager = "SELECT * FROM collaborators WHERE TaskID = '$taskID' AND CollaboratorManager = '{$_SESSION['username']}'";
            $collaboratorSearchResultManager = mysqli_query($link, $collaboratorSearchSQLManager);


            if (mysqli_num_rows($collaboratorSearchResultMaker) > 0) {
                // If user is a CollaboratorMaker, output username with role
                $currentuserclb = "Access: CollaboratorMaker";
                // Use the default query for CollaboratorMaker
                $sql = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration FROM subtasks WHERE TaskID = '$taskID'";
            } elseif (mysqli_num_rows($collaboratorSearchResultManager) > 0) {
                // If user is a CollaboratorMaker, output username with role
                $currentuserclb = "Access: CollaboratorManager";
                // Use the default query for CollaboratorMaker
                $sql = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration FROM subtasks WHERE TaskID = '$taskID'";
            } elseif (mysqli_num_rows($collaboratorSearchResultMember) > 0) {
                // If user is a CollaboratorMember, output username with role
                $currentuserclb = "Access: CollaboratorMember";
                // Use the query for CollaboratorMember
                $sql = "
                    SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration 
                    FROM subtasks
                    WHERE TaskID = '$taskID'
                    AND (
                        CONCAT(SubtaskID, 'ed0') IN (SELECT EditLogID FROM editlogs WHERE UserID = '" . $_SESSION["userID"] . "')
                        OR EXISTS (
                            SELECT 1 
                            FROM subtaskaccess 
                            WHERE subtasks.SubtaskID = subtaskaccess.SubtaskID 
                            AND subtasks.TaskID = subtaskaccess.TaskID 
                            AND subtaskaccess.AssignedTo = '{$_SESSION['username']}'
                        )
                    )

                ";

            } else {
                // If user is not found in collaborators table, output username only
                $currentuserclb = $_SESSION["username"];
                // Use the default query
                $sql = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration FROM subtasks WHERE TaskID = '$taskID'";
            }
        } else {
            // If $clbstatus is false, set $currentuserclb to 'false'
            $currentuserclb = 'false';
            // Use the default query
            $sql = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration FROM subtasks WHERE TaskID = '$taskID'";
        }

        // Execute a query to determine the presence of SubtaskMarkNumber
        $checkMarkNumberSQL = "SELECT EXISTS(SELECT 1 FROM subtasks WHERE TaskID = '$taskID' AND SubtaskMarkNumber IS NOT NULL AND SubtaskMarkNumber != '') AS HasMarkNumber";
        $markCheckResult = mysqli_query($link, $checkMarkNumberSQL);
        if ($markCheckResult && $row = mysqli_fetch_assoc($markCheckResult)) {
            if ((int) $row['HasMarkNumber'] === 1) {
                if (empty($sortBy)) {
                    $sortBy = 'markedfirst'; // Default to 'markedfirst' if any SubtaskMarkNumber is not empty
                }
            } else {
                if (empty($sortBy)) {
                    $sortBy = 'statusasc'; // Default to 'shortest' if all SubtaskMarkNumber are empty
                }
            }
        }


        if (!empty($statusFilter)) {
            if ($statusFilter === 'Pending') {
                $sql .= " AND SubtaskStatus IN ('Pending', 'Pending (Revise)', 'Pending (Short-Term)', 'Pending (To-Follow)', 'Pending (Long-Term)')";
            } else if ($statusFilter === 'Done') {
                $sql .= " AND SubtaskStatus IN ('Done', 'Done (For Review)')";
            } else {
                $sql .= " AND SubtaskStatus = '$statusFilter'";
            }
        }

        // Apply sorting based on the sortBy parameter
        switch ($sortBy) {
            case 'shortest':
                $sql .= " ORDER BY SubtaskDuration ASC, SubtaskEndTime DESC, SubtaskCreationDate DESC";
                break;
            case 'longest':
                $sql .= " ORDER BY SubtaskDuration DESC, SubtaskEndTime DESC, SubtaskCreationDate DESC";
                break;
            case 'easy':
                $sql .= " ORDER BY FIELD(SubtaskDifficulty, 'Easy', 'Average', 'Hard'), SubtaskEndTime DESC, SubtaskCreationDate DESC";
                break;
            case 'hard':
                $sql .= " ORDER BY FIELD(SubtaskDifficulty, 'Hard', 'Average', 'Easy'), SubtaskEndTime DESC, SubtaskCreationDate DESC";
                break;
            case 'markedfirst':
                $sql .= " ORDER BY CASE WHEN SubtaskMarkNumber IS NOT NULL AND SubtaskMarkNumber != '' THEN CAST(SubtaskMarkNumber AS UNSIGNED) ELSE 99999 END ASC, FIELD(SubtaskStatus, 'Ongoing', 'Pending (Revise)','Pending (Short-Term)', 'Pending', 'Pending (To-Follow)',  'Pending (Long-Term)','Done (For Review)', 'Done'), SubtaskEndTime DESC, SubtaskCreationDate DESC";
                break;

            case 'statusasc':
                $sql .= " ORDER BY FIELD(SubtaskStatus, 'Ongoing', 'Pending (Revise)','Pending (Short-Term)', 'Pending', 'Pending (To-Follow)',   'Pending (Long-Term)', 'Done (For Review)', 'Done'), SubtaskEndTime DESC,SubtaskCreationDate DESC";
                break;
            case 'statusdesc':
                $sql .= " ORDER BY FIELD(SubtaskStatus, 'Pending (Long-Term)', 'Done (For Review)', 'Done', 'Ongoing', 'Pending (Revise)','Pending (To-Follow)', 'Pending', 'Pending (Short-Term)'), SubtaskEndTime DESC, SubtaskCreationDate DESC";
                break;
            default:

                break;
        }

        $result = mysqli_query($link, $sql);


        // Display $currentuserclb here
        ?>
        <div class="m-auto text-center flex rounded-3xl px-3 py-2 mb-2 items-center"
            style="background-color: <?php echo $taskTextColor; ?>; color: <?php echo $taskBackgroundColor; ?>;   ">
            <p id="currentuserclb" class="font-bold text-xl  <?php echo ($clbstatus) ? '' : 'hidden'; ?>">
                <?php echo $currentuserclb; ?>
            </p>
        </div>
        <?php

        if (mysqli_num_rows($result) > 0) {
            ?>

            <?php if ($clbstatus) { ?>
                <button id="buzznotificationButton"
                    style="background-color: <?php echo $taskBackgroundColor; ?>; color: <?php echo $taskTextColor; ?>;   filter: brightness(135%);"
                    class="fixed bottom-0 left-5 bg-yellow-500 text-white px-4 py-2 rounded shadow w-96 text-3xl font-bold flex items-center justify-between"
                    onclick="openBuzzModal()">
                    Notification
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-0 transition duration-300" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                </button>

                <div id="buzznotificationmodal"
                    class="fixed bottom-0 left-5 bg-white text-black rounded-t-lg  shadow-lg hidden w-96 bg-transparent ">
                    <!-- Modal content -->
                    <!-- Header -->
                    <div style="background-color: <?php echo $taskBackgroundColor; ?>; color: <?php echo $taskTextColor; ?>;   filter: brightness(135%);"
                        id="buzznotificationmodalHeader"
                        class="flex justify-between items-center mb-4 bg-yellow-500 text-white px-4 py-2  rounded-t-lg cursor-pointer "
                        onclick="closeBuzzModal()">
                        <span class="text-3xl font-bold text-left">Notification</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-180 transition duration-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </div>
                    <div id="buzzlist" style="max-height:600px;" class="text-black overflow-y-auto">
                        <?php
                        // Fetch and display subtasks with SubtaskStatus of 'Pending (Revise)'
                        $pendingReviseSQL = "SELECT SubtaskID, SubtaskTitle, SubtaskDescription, SubtaskDuration, SubtaskDifficulty, SubtaskCreationDate, SubtaskBackgroundColor, SubtaskTextColor, SubtaskStatus, SubtaskMarkNumber, SubtaskImage, SubtaskStartTime, SubtaskEndTime, SubtaskPausedDuration FROM subtasks WHERE TaskID = '$taskID' AND SubtaskStatus = 'Pending (Revise)'";
                        $pendingReviseResult = mysqli_query($link, $pendingReviseSQL);

                        if (mysqli_num_rows($pendingReviseResult) > 0) {
                            while ($row = mysqli_fetch_assoc($pendingReviseResult)) {
                                // Replace line breaks in subtask description
                                $subtaskDescription = str_replace("\r\n", '147linebreakbymatthew', $row['SubtaskDescription']);
                                $subtaskDescription = str_replace(array("\r", "\n"), '147linebreakbymatthew', $subtaskDescription);

                                echo "<p class='px-3 mb-2 font-bold'>Subtask Title: <span class='font-medium'>{$row['SubtaskTitle']}</span></p>";

                                echo "<div class='flex justify-end'>";
                                // Updated button with onclick attribute for "Pending (Revise)"
                                echo "<button onclick=\"openVisualModal('{$row['SubtaskID']}', '" . addslashes($row['SubtaskTitle']) . "', '" . addslashes($subtaskDescription) . "', '{$row['SubtaskDuration']}', '{$row['SubtaskDifficulty']}', '{$row['SubtaskBackgroundColor']}', '{$row['SubtaskTextColor']}', '{$row['SubtaskStatus']}', '{$row['SubtaskMarkNumber']}', '{$row['SubtaskStartTime']}', '{$row['SubtaskEndTime']}', '{$row['SubtaskPausedDuration']}', '{$row['SubtaskCreationDate']}', '{$row['SubtaskImage']}')\"  
                                class='px-4 py-2 mr-4 rounded-md bg-yellow-600 hover:bg-opacity-75 transition-colors duration-300 text-white font-bold text-center max-w-xs'>Pending (Revise)</button>";
                                echo "</div>";

                                echo "<hr class='border-gray-400 mt-4 mx-0'>";
                            }
                        } else {
                            echo "<p></p>";
                        }
                        ?>
                    </div>

                    <div id="chatlist" class="text-black ">
                        <?php
                        // Fetch Subtask details from subtasks table based on SubtaskID from buzznotify table
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

                        // Check if there are any results
                        if (mysqli_num_rows($buzzNotifyResult) > 0) {
                            // Fetch and display Subtask details
                            while ($row = mysqli_fetch_assoc($buzzNotifyResult)) {
                                // Check if SubtaskPausedDuration is empty
                                $pausedDuration = empty($row['SubtaskPausedDuration']) ? '00:00:00' : $row['SubtaskPausedDuration'];

                                echo "<p class='px-3 mb-2 font-bold'>Subtask Title: <span class='font-medium'>{$row['SubtaskTitle']}</span></p>";
                                echo "<p class='px-3 mb-2 font-bold'>Latest Message: <span class='font-medium'>{$row['BuzzMessage']}</span></p>";

                                echo "<div class='flex justify-end'>";
                                // Updated button with onclick attribute
                                echo "<button onclick=\"updateBuzzNotify('{$row['SubtaskID']}', '{$taskID}', '{$row['UserID']}'); openVisualModal('{$row['SubtaskID']}', '" . addslashes($row['SubtaskTitle']) . "', '" . addslashes($row['SubtaskDescription']) . "',
                                    '{$row['SubtaskDuration']}', '{$row['SubtaskDifficulty']}', '{$row['SubtaskBackgroundColor']}', '{$row['SubtaskTextColor']}', '{$row['SubtaskStatus']}',
                                    '{$row['SubtaskMarkNumber']}', '{$row['SubtaskStartTime']}', '{$row['SubtaskEndTime']}', '{$pausedDuration}', '{$row['SubtaskCreationDate']}',
                                    '{$row['SubtaskImage']}'); toggleChatModal(true);\"  
                                    class='px-4 py-2 mr-4 rounded-md bg-red-600 hover:bg-opacity-75 transition-colors duration-300 text-white font-bold text-center max-w-xs'>
                                    Buzz Raised </button>";

                                echo "</div>";
                            }
                        } else {
                            // If no Subtask found for the specified taskID
                            echo "<p></p>";
                        }
                        ?>
                        <hr class='border-gray-400 mt-4 mx-0'>
                    </div>




                </div>




            <?php } ?>


            <div id="tgrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 ">
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    // Determine the background color based on SubtaskBackgroundColor
                    $bgColorStyle = $row['SubtaskBackgroundColor'] ? "style='background-color: {$row['SubtaskBackgroundColor']};'" : ""; // Default color if not provided
                    // Apply text color if provided
                    $textColorStyle = $row['SubtaskTextColor'] ? "style='color: {$row['SubtaskTextColor']};'" : "";

                    $hasImage = !empty($row['SubtaskImage']);
                    $subtaskDescription = str_replace("\r\n", '147linebreakbymatthew', $row['SubtaskDescription']);
                    // Replace any remaining single \n or \r (from Unix/Linux or Mac)
                    $subtaskDescription = str_replace(array("\r", "\n"), '147linebreakbymatthew', $subtaskDescription);

                    date_default_timezone_set('Asia/Manila');

                    $startTimeFormatted = date('m-d-y H:i:s', strtotime($row['SubtaskStartTime']));

                    // Fetch AssignedTo from subtaskaccess table based on SubtaskID
                    $assignedToQuery = "SELECT AssignedTo FROM subtaskaccess WHERE SubtaskID = '{$row['SubtaskID']}'";
                    $assignedToResult = mysqli_query($link, $assignedToQuery);
                    if ($assignedToResult && mysqli_num_rows($assignedToResult) > 0) {
                        $assignedToRow = mysqli_fetch_assoc($assignedToResult);
                        if ($assignedToRow['AssignedTo'] == $_SESSION['username']) {
                            $assignedTo = "Assigned to You";
                        } else {
                            $assignedTo = "Assigned To: " . $assignedToRow['AssignedTo'];
                        }
                    } else {
                        // If subtask is not assigned, get creator information from editlogs
                        $editLogQuery = "SELECT UserID FROM editlogs WHERE EditLogID = '{$row['SubtaskID']}ed0'";
                        $editLogResult = mysqli_query($link, $editLogQuery);
                        if ($editLogResult && mysqli_num_rows($editLogResult) > 0) {
                            // If user's edit log is found, display "Created by"
                            $editLogRow = mysqli_fetch_assoc($editLogResult);
                            $userID = $editLogRow['UserID'];

                            // Fetch UserUsername from user table based on UserID
                            $userQuery = "SELECT UserUsername FROM user WHERE UserID = '{$userID}'";
                            $userResult = mysqli_query($link, $userQuery);
                            if ($userResult && mysqli_num_rows($userResult) > 0) {
                                $userRow = mysqli_fetch_assoc($userResult);
                                if ($userRow['UserUsername'] == $_SESSION['username']) {
                                    $assignedTo = "Created by You";
                                } else {
                                    $assignedTo = "Created by: " . $userRow['UserUsername'];
                                }
                            } else {
                                $assignedTo = "Created by: Unknown";
                            }
                        } else {
                            // If no assignment or edit log found, display "Unassigned"
                            $assignedTo = "";
                        }
                    }



                    // Determine the background color for SubtaskDiv based on SubtaskStatus
                    $subtaskDivBackground = '';
                    switch ($row['SubtaskStatus']) {
                        case 'Pending':
                            $subtaskDivBackground = 'bg-yellow-600';
                            break;
                        case 'Pending (Short-Term)':
                            $subtaskDivBackground = 'bg-yellow-600';
                            break;
                        case 'Pending (Revise)':
                            $subtaskDivBackground = 'bg-yellow-600';
                            break;
                        case 'Pending (Long-Term)':
                            $subtaskDivBackground = 'bg-yellow-600';
                            break;
                        case 'Pending (To-Follow)':
                            $subtaskDivBackground = 'bg-yellow-600';
                            break;
                        case 'Ongoing':
                            $subtaskDivBackground = 'bg-red-600';
                            break;
                        case 'Done':
                            $subtaskDivBackground = 'bg-green-600';
                            break;
                        case 'Done (For Review)':
                            $subtaskDivBackground = 'bg-green-600';
                            break;
                        default:
                            // Default background color here if needed
                            break;
                    }

                    ?>
                    <div id="subtaskcontainer" class='relative rounded-lg '>

                        <div id="subtaskcontent"
                            class="flex flex-col justify-between relative p-6 rounded-lg shadow-lg <?php echo $subtaskDivBackground; ?>"
                            <?php echo $bgColorStyle; ?>>




                            <div id="subtaskheader" class="flex justify-between items-center mb-4">

                                <div id="SubtaskDiv"
                                    onclick="openVisualModal('<?php echo $row['SubtaskID']; ?>', '<?php echo addslashes($row['SubtaskTitle']); ?>', '<?php echo addslashes($subtaskDescription); ?>', '<?php echo $row['SubtaskDuration']; ?>', '<?php echo $row['SubtaskDifficulty']; ?>', '<?php echo $row['SubtaskBackgroundColor']; ?>', '<?php echo $row['SubtaskTextColor']; ?>', '<?php echo $row['SubtaskStatus']; ?>', '<?php echo $row['SubtaskMarkNumber']; ?>', '<?php echo $row['SubtaskStartTime']; ?>', '<?php echo $row['SubtaskEndTime']; ?>','<?php echo $row['SubtaskPausedDuration']; ?>', '<?php echo $row['SubtaskCreationDate']; ?>', '<?php echo $row['SubtaskImage']; ?>')"
                                    class="px-3 py-1 rounded-md self-start <?php echo $subtaskDivBackground; ?> hover:bg-opacity-75 transition-colors duration-300"
                                    style="color: white; cursor: grab;">
                                    <p class="text-lg font-bold"><?php echo $row['SubtaskStatus']; ?></p>
                                </div>




                                <div class="flex justify-start">
                                    <div class="bg-black rounded-full text-center text-lg font-medium whitespace-nowrap w-24 mr-20">
                                        <?php
                                        $textColorStyle = 'text-yellow-500'; // Set text color to orange-500
                                        if (in_array($row['SubtaskStatus'], ["Pending", "Pending (Revise)", "Pending (Short-Term)", "Pending (Long-Term)", "Pending (To-Follow)"])) {
                                            // If SubtaskPausedDuration is not null, get its value
                                            $pausedDuration = $row['SubtaskPausedDuration'] !== null ? $row['SubtaskPausedDuration'] : '';
                                            echo '<span class="' . $textColorStyle . '">' . $pausedDuration . '</span>';

                                        } elseif ($row['SubtaskStatus'] == 'Ongoing') {
                                            // Display timer with red color if SubtaskStatus is Ongoing
                                            echo '<span class="text-red-600 font-bold mr-2" id="timer_' . $row['SubtaskID'] . '"></span>';
                                        } elseif ($row['SubtaskStatus'] == 'Done' || $row['SubtaskStatus'] == 'Done (For Review)') {
                                            // Display "-" for Done tasks with green color
                                            echo '<span class="text-green-600 font-bold mr-2" id="timer_' . $row['SubtaskID'] . '">-</span>';
                                        }
                                        ?>
                                    </div>
                                </div>




                                <div class="flex justify-between items-center ">
                                    <?php if (($sortBy === 'markedfirst') && !empty($row['SubtaskMarkNumber'])) { ?>
                                        <div id="markernumber"
                                            class="absolute right-10 mr-6 flex justify-center items-center font-medium rounded-full h-8 w-8 font-bold"
                                            style="background-color: <?php echo $row['SubtaskTextColor']; ?>; color: <?php echo $row['SubtaskBackgroundColor']; ?>;">
                                            <?php echo $row['SubtaskMarkNumber']; ?>
                                        </div>
                                    <?php } ?>


                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="absolute right-6  h-6 md:h-8 w-6 md:w-8 text-white cursor-pointer"
                                            viewBox="0 0 20 20" fill="currentColor"
                                            onclick="openEditModal('<?php echo $row['SubtaskID']; ?>', '<?php echo addslashes($row['SubtaskTitle']); ?>', '<?php echo addslashes($subtaskDescription); ?>', '<?php echo $row['SubtaskDuration']; ?>', '<?php echo $row['SubtaskDifficulty']; ?>', '<?php echo $row['SubtaskBackgroundColor']; ?>', '<?php echo $row['SubtaskTextColor']; ?>', '<?php echo $row['SubtaskStatus']; ?>', '<?php echo $row['SubtaskMarkNumber']; ?>')">
                                            <path fill-rule="evenodd"
                                                d="M3 10a2 2 0 114 0 2 2 0 01-4 0zM9 10a2 2 0 114 0 2 2 0 01-4 0zm6 0a2 2 0 114 0 2 2 0 01-4 0z"
                                                clip-rule="evenodd" style="fill: <?php echo $row['SubtaskTextColor']; ?>;" />
                                        </svg>
                                    </div>
                                </div>



                            </div>


                            <div class="flex items-center mb-4">


                                <h3 style="color: <?php echo $row['SubtaskTextColor']; ?>;" class='text-xl font-semibold w-full '>
                                    <?php echo $row['SubtaskTitle']; ?>
                                </h3>

                                <?php if ($row['SubtaskStatus'] == 'Done' || $row['SubtaskStatus'] == 'Done (For Review)') { ?>
                                    <div class="flex items-center justify-center mt-1">
                                        <div id="SubtaskDiv" class="px-2 py-1 rounded-md self-start <?= $subtaskDivBackground ?>"
                                            style="color: white;">
                                            <p id="efficiency_rate_<?= $row['SubtaskID'] ?>" class="text-sm font-medium text-white">-</p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="flex items-center mb-4">

                                <p style="color: <?php echo $row['SubtaskTextColor']; ?>;"
                                    class='text-sm font-medium overflow-y-auto max-h-64 mt-2 w-full'>
                                    <?php echo nl2br($row['SubtaskDescription']); ?>
                            </div>

                            <p <?php echo $textColorStyle; ?> class='hidden text-sm font-medium overflow-y-auto max-h-20 mt-2'>
                                Task ID: <?php echo $taskID; ?> <!-- Display taskID -->
                            </p>

                            <?php if ($hasImage) { ?>
                                <div id="imageplacer" class="bg-transparent w-full mb-4 h-20 relative">
                                    <img src="<?php echo $row['SubtaskImage']; ?>" alt="Subtask Image"
                                        class="absolute inset-0 h-full w-full object-contain object-center cursor-pointer"
                                        onclick="openImageModal('<?php echo $row['SubtaskImage']; ?>')">
                                </div>
                            <?php } ?>

                            <div class='flex justify-between items-center mt-2'>
                                <p style="color: <?php echo $row['SubtaskTextColor']; ?>;" class='text-sm font-medium'>Duration:
                                    <?php
                                    $durationInMinutes = $row['SubtaskDuration'];
                                    $days = floor($durationInMinutes / (24 * 60));
                                    $hours = floor(($durationInMinutes % (24 * 60)) / 60);
                                    $minutes = $durationInMinutes % 60;

                                    $formattedDuration = "";
                                    if ($days > 0) {
                                        $formattedDuration .= $days . " day" . ($days > 1 ? "s " : " ");
                                    }
                                    if ($hours > 0) {
                                        $formattedDuration .= $hours . " hour" . ($hours > 1 ? "s " : " ");
                                    }
                                    if ($minutes > 0) {
                                        $formattedDuration .= $minutes . " min" . ($minutes > 1 ? "s" : "");
                                    }

                                    echo $formattedDuration;
                                    ?>
                                </p>

                                <p style="color: <?php echo $row['SubtaskTextColor']; ?>;" class='text-sm font-medium'>Difficulty:
                                    <?php echo $row['SubtaskDifficulty']; ?>
                                </p>
                            </div>


                            <p id="assignedName" class="text-xs font-bold absolute bottom-7 right-6 ">
                                <?php echo $assignedTo; ?>
                            </p>


                            <div class="flex justify-center items-center mt-4">
                                <div class="px-3 py-1 rounded-md"
                                    style="cursor: grab; background-color: <?php echo $row['SubtaskTextColor']; ?>; color: <?php echo $row['SubtaskBackgroundColor']; ?>;">
                                    <?php
                                    // Get Today's date and Yesterday's date
                                    $Today = date('Y-m-d');
                                    $Yesterday = date('Y-m-d', strtotime('-1 day'));

                                    if ($row['SubtaskStatus'] == 'Pending' || $row['SubtaskStatus'] == 'Pending (Revise)' || $row['SubtaskStatus'] == 'Pending (Short-Term)' || $row['SubtaskStatus'] == 'Pending (Long-Term)' || $row['SubtaskStatus'] == 'Pending (To-Follow)' || $row['SubtaskStatus'] == 'Ongoing') {
                                        $creation_date = date('Y-m-d', strtotime($row['SubtaskCreationDate']));
                                        if ($creation_date == $Today) {
                                            $time_display = "Today";
                                        } elseif ($creation_date == $Yesterday) {
                                            $time_display = "Yesterday";
                                        } else {
                                            $time_display = "on " . date('M-d-Y', strtotime($row['SubtaskCreationDate']));
                                        }
                                        ?>
                                        <p class="text-xs font-bold" onclick="toggleDateDetails(this)">
                                            Made <?php echo $time_display; ?>
                                            <span class="hidden"> at
                                                <?php echo date('g:i A', strtotime($row['SubtaskCreationDate'])); ?></span>
                                        </p>
                                    <?php } elseif ($row['SubtaskStatus'] == 'Done' || $row['SubtaskStatus'] == 'Done (For Review)') {
                                        $end_time = date('Y-m-d', strtotime($row['SubtaskEndTime']));
                                        if ($end_time == $Today) {
                                            $time_display = "Today";
                                        } elseif ($end_time == $Yesterday) {
                                            $time_display = "Yesterday";
                                        } else {
                                            $time_display = "on " . date('M-d-Y', strtotime($row['SubtaskEndTime']));
                                        }
                                        ?>
                                        <p class="text-xs font-bold" onclick="toggleDateDetails(this)">
                                            Done <?php echo $time_display; ?>
                                            <span class="hidden"> at
                                                <?php echo date('g:i A', strtotime($row['SubtaskEndTime'])); ?></span>
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>






                        </div>
                    </div>

                    <!-- Modal -->
                    <div id="imageModal"
                        class="fixed top-0 left-0 z-50 w-full h-full flex justify-center items-center bg-black bg-opacity-50 hidden text-white"
                        onclick="closeImageModal(event)">
                        <div class="bg-white rounded-lg overflow-hidden relative max-w-screen-md max-h-screen-md "
                            style="max-height: 95vh;">
                            <!-- Image -->
                            <img id="modalImage" src="" alt="Modal Image" class="w-full h-full object-contain" />
                        </div>

                        <button onclick="closeImageModal()" class="absolute top-0 right-0 p-3 rounded-full white">&#10005;</button>
                    </div>
                    <?php
                }
                ?>
            </div>
            <input type="hidden" id="editsubtaskID" />
            <style>
                @media (max-width: 1030px) {
                    #tgrid {
                        grid-template-columns: repeat(1, minmax(0, 1fr));
                    }
                }

                #editmodal {
                    z-index: 3000;
                }

                #visualModal {
                    z-index: 2000;
                }

                #imageModal {
                    z-index: 4000;
                }

                #chatModal {
                    z-index: 2500;
                }

                #buzznotificationButton {
                    z-index: 1000;
                }

                #buzznotificationmodal {
                    z-index: 1000;
                }
            </style>

            <div class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden text-white overflow-y-auto" id="editmodal"
                onclick="stopPropagation(event)">
                <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-gray-900 p-4 rounded-lg w-11/12 "
                    onclick="stopPropagation(event)">
                    <div class="flex justify-between items-center mb-4">

                        <div class="rounded-lg py-2 cursor-pointer hover:bg-gray-500 border border-transparent ">
                            <h2 id="editsubtasklabel" class="text-2xl font-bold">Edit Subtask Settings</h2>
                        </div>
                        <div id="editlogsdiv" onclick="toggleEditSettingsContent()"
                            class="group rounded-lg mr-6 px-2 py-2 cursor-pointer hover:bg-gray-500 border border-transparent">
                            <h2 id="editlogslabel"
                                class="text-2xl text-gray-600 font-bold group-hover:text-white transition-colors">History Logs</h2>
                        </div>


                        <div id="editlogsdiv2"
                            class="group rounded-lg mr-6 px-2 py-2 cursor-pointer hover:bg-gray-500 border border-transparent hidden">
                        </div>



                        <button onclick="closeEditModal()" class="absolute top-0 right-0 p-3 rounded-full white">&#10005;</button>
                    </div>

                    <div id="editsettingscontent" class="transition-all duration-500 ease-in-out">
                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Subtask Title</label>
                            <input type="text" id="editsubtaskTitle"
                                class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                        </div>
                        <div class="mb-4" onclick="stopPropagation(event)">

                            <div class="flex items-center mb-4" onclick="stopPropagation(event)">
                                <label class="inline-block mb">Subtask Description</label>
                                <div class="inline-flex items-center">
                                    <button class="ml-4 text-white text-2xl">☐</button>
                                    <button class="ml-2 text-white text-2xl">&#9745;</button>
                                </div>
                            </div>
                            <textarea id="editsubtaskDescription"
                                class="bg-gray-800 text-white px-4 py-2 w-full h-48 rounded focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>

                        <div class="mb-4 flex" onclick="stopPropagation(event)">
                            <div class="w-1/6 mr-2">
                                <label class="block mb-2">Duration (days)</label>
                                <input type="number" id="editdurationdays"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                            </div>
                            <div class="w-1/6 mr-2">
                                <label class="block mb-2">Duration (hours)</label>
                                <input type="number" id="editdurationhours"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                            </div>

                            <div class="w-1/6 mr-2">
                                <label class="block mb-2">Duration (mins)</label>
                                <input type="number" id="editduration"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                            </div>
                            <div class="w-1/2 ml-2">
                                <label class="block mb-2">Status</label>
                                <select id="editStatus"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="Pending">Pending</option>
                                    <option value="Pending (Revise)">Pending (Revise)</option>

                                    <option value="Pending (Short-Term)">Pending (Short-Term)</option>
                                    <option value="Pending (Long-Term)">Pending (Long-Term)</option>
                                    <option value="Pending (To-Follow)">Pending (To-Follow)</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Done">Done</option>
                                    <option value="Done (For Review)">Done (For Review)</option>

                                </select>
                            </div>
                        </div>

                        <div class="mb-4 flex" onclick="stopPropagation(event)">
                            <div class="w-1/2 mr-2">
                                <label class="block mb-2">Mark Number</label>
                                <select id="editMarkNumber"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="">Select Mark Number</option> <!-- Blank option -->
                                    <?php
                                    // Fetch the count of subtasks with the same task ID
                                    $subtaskCountQuery = "SELECT COUNT(*) AS count FROM subtasks WHERE TaskID = '$taskID'";
                                    $subtaskCountResult = mysqli_query($link, $subtaskCountQuery);
                                    $subtaskCountRow = mysqli_fetch_assoc($subtaskCountResult);
                                    $subtaskCount = $subtaskCountRow['count'];
                                    // Generate options starting from 1 up to the number of subtasks + 1
                                    for ($i = 1; $i <= $subtaskCount; $i++) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="w-1/2 ml-2">
                                <label class="block mb-2">Difficulty Level</label>
                                <select id="editdifficultyLevel"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="Easy">Easy</option>
                                    <option value="Average">Average</option>
                                    <option value="Hard">Hard</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 " onclick="stopPropagation(event)">
                            <label class="mb-2">Collaboration Accessibility Options</label>
                            <div class="flex items-center ">
                                <div class="w-11/12 mr-2">
                                    <select id="editcollaboration"
                                        class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <option value="">Select Collaborator</option> <!-- Blank option -->
                                        <?php
                                        // Fetch CollaboratorMembers from collaborators table based on TaskID
                                        $collaboratorQuery = "SELECT CollaboratorMember FROM collaborators WHERE TaskID = '$taskID'";
                                        $collaboratorResult = mysqli_query($link, $collaboratorQuery);
                                        // Generate options for each CollaboratorMember
                                        while ($row = mysqli_fetch_assoc($collaboratorResult)) {
                                            $collaboratorMember = $row['CollaboratorMember'];
                                            echo "<option value='$collaboratorMember'>$collaboratorMember</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 ml-2"
                                    onclick="accessadd()">Add</button>
                            </div>

                            <div class="mb-4" onclick="stopPropagation(event)">

                                <label class="block mb-2">Collaborator List:</label>
                                <div class="mb-4" onclick="stopPropagation(event)" id="collaboratorlist">
                                </div>

                            </div>

                        </div>


                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Upload Image</label>
                            <input type="file" id="editImage" name="editImage" accept="image/*"
                                class="bg-gray-800 text-white w-1/2 rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                        </div>
                        <div class="mb-4 flex" onclick="stopPropagation(event)">
                            <div class="w-1/2 mr-2">
                                <label class="block mb-2">Task Background Color</label>
                                <input type="color" id="editBgColor"
                                    class="bg-gray-800 text-white w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                                    onchange="updateColorCode('editBgColor', 'bgColorCode', true)" />
                                <input type="text" id="bgColorCode" class="bg-gray-800 text-white w-full rounded mt-2 px-2 py-1"
                                    onchange="updateColorCode('editBgColor', 'bgColorCode', false)" />
                            </div>
                            <div class="w-1/2 ml-2">
                                <label class="block mb-2">Task Text Color</label>
                                <input type="color" id="editTextColor"
                                    class="bg-gray-800 text-white w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                                    onchange="updateColorCode('editTextColor', 'textColorCode', true)" />
                                <input type="text" id="textColorCode" class="bg-gray-800 text-white w-full rounded mt-2 px-2 py-1"
                                    onchange="updateColorCode('editTextColor', 'textColorCode', false)" />
                            </div>
                        </div>
                        <input type="hidden" id="editsubtaskID" />
                        <div class="flex justify-between">
                            <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                                onclick="deleteTask()">Delete</button>
                            <div>
                                <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 mr-2"
                                    onclick="closeEditModal()" id="closeEditModal">Cancel</button>
                                <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600" id="editChanges"
                                    onclick="logUpdatedInputs()">Edit Changes</button>
                            </div>
                        </div>
                    </div>


                    <div id="editlogcontent" class="h-11/12 hidden text-white overflow-y-auto"
                        style="min-height: 80vh; max-height: 80vh;">
                        <div id="editloglist"></div>
                    </div>

                </div>
            </div>


            <div id="chatModal"
                class="fixed bottom-0 right-5 hidden flex flex-col items-center justify-center w-full max-w-lg mx-auto w-96">
                <div class="bg-white text-black rounded-lg shadow-lg w-full">
                    <!-- Chat header -->
                    <div id="chatheader"
                        class="flex justify-end items-center mb-4 bg-blue-500 px-4 py-2 rounded-t-lg cursor-pointer"
                        onclick="toggleChatModal(false)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-180 transition duration-300"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </div>
                    <span id="chatheaderlabel" class="absolute top-0 left-2 text-3xl font-bold text-white text-left">Buzz
                        Help</span>


                    <p id="chatuserid" class=" hidden break-words"></p>
                    <p id="chatsubtaskid" class="hidden break-words"></p>

                    <!-- Chat content area -->
                    <div id="chatmessagecontent" class="mb-4 overflow-y-auto " style="min-height:500px; max-height:500px;">

                    </div>

                    <!-- Input for message -->
                    <div class="flex">
                        <textarea id="chatinput" rows="1"
                            class="form-textarea flex-1 rounded-l-lg p-2 border-2 border-r-0 resize-none"
                            placeholder="Type your message..."></textarea>
                        <button id="enterchatbutton" class="bg-blue-500 text-white font-medium px-4 py-2 rounded-r-lg"
                            onclick="sendbuzz()">Send
                        </button>
                    </div>
                </div>
            </div>



            <div id="visualModal" class="modal fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
                <div id="visualModalcontent" class="bg-gray-800  mx-auto overflow-hidden relative rounded-2xl"
                    style="height: 98vh; width: 98vw;">
                    <button id="closeModal" onclick="closeVisualModal()" class="absolute top-0 right-0 mr-4  text-2xl">×</button>


                    <!-- Task Number and Status -->
                    <div id="visualmarknumber" class="absolute top-1 left-2 flex items-center hidden">
                        <div id="visualmarknumbercircle"
                            class="rounded-full h-8 w-8 bg-white flex items-center justify-center mr-2">
                            <p>1</p>
                        </div>
                    </div>
                    <div id="visualSubtaskID" class="hidden">Subtask ID</div>




                    <div id="ringcircle"
                        class="absolute bottom-16 right-6 p-2 font-medium rounded-full bg-yellow-500 cursor-pointer"
                        onclick="toggleChatModal(true)">
                        <img id="ringbell" width="50" height="50" src="https://img.icons8.com/ios-filled/50/FFFFF/alarm.png"
                            alt="alarm" />
                    </div>


                    <svg id="visualmenu" xmlns="http://www.w3.org/2000/svg"
                        class="absolute right-14 h-8 md:h-10 w-8 md:w-10 text-white cursor-pointer" viewBox="0 0 20 20"
                        fill="currentColor" onclick="openEditModal(
                        document.getElementById('visualSubtaskID').innerText,
                        document.getElementById('visualtitle').innerText,
                        document.getElementById('visualdescription').innerText,
                        convertDurationToMinutes(document.getElementById('visualduration').innerText), // Call the function here
                        document.getElementById('visualdifficulty').innerText.replace('Difficulty: ', ''), // Remove prefix
                        rgbToHex(document.getElementById('visualModalcontent').style.backgroundColor),
                        rgbToHex(document.getElementById('visualModal').style.color),
                        document.getElementById('visualstatus').innerText.replace('Status: ', ''), // Remove prefix
                        document.getElementById('visualmarknumber').querySelector('p').innerText
                    )">
                        <path fill-rule="evenodd"
                            d="M3 10a2 2 0 114 0 2 2 0 01-4 0zM9 10a2 2 0 114 0 2 2 0 01-4 0zm6 0a2 2 0 114 0 2 2 0 01-4 0z"
                            clip-rule="evenodd" style="fill: <?php echo $row['SubtaskTextColor']; ?>;" />
                    </svg>


                    <path fill-rule="evenodd"
                        d="M3 10a2 2 0 114 0 2 2 0 01-4 0zM9 10a2 2 0 114 0 2 2 0 01-4 0zm6 0a2 2 0 114 0 2 2 0 01-4 0z"
                        clip-rule="evenodd" style="fill: <?php echo $row['SubtaskTextColor']; ?>;" />
                    </svg>


                    <path fill-rule="evenodd"
                        d="M3 10a2 2 0 114 0 2 2 0 01-4 0zM9 10a2 2 0 114 0 2 2 0 01-4 0zm6 0a2 2 0 114 0 2 2 0 01-4 0z"
                        clip-rule="evenodd" style="fill: <?php echo $row['SubtaskTextColor']; ?>;" />
                    </svg>


                    <div class="flex flex-col items-center mt-20 w-5/6 mx-auto  h-full px-8">


                        <div
                            class="flex justify-center items-center bg-black rounded-full text-center text-3xl font-medium whitespace-nowrap w-40 px-2 py-2 mb-8">
                            <span id="visualtimer" class="font-bold mr-2 text-black">-</span>
                        </div>



                        <div class="flex flex-col items-center">
                            <h2 id="visualtitle" class="text-3xl font-bold mb-4 text-center">Subtask Title</h2>
                            <p id="visualdescription" class="text-xl mb-8 mr-auto overflow-y-auto" style="max-height: 40vh;">Lore?
                            </p>
                        </div>

                        <div id="visualimageplacer"
                            class="absolute bottom-32 flex justify-center items-center bg-transparent w-11/12 h-60">
                        </div>

                        <div class="fixed bottom-32 left-0 w-full h-60 bg-transparent flex justify-between items-center">
                            <div class="w-72 h-48">
                                <img class="w-full h-full object-contain" src="https://i.gifer.com/Z6W2.gif" alt="GIF Animation">
                            </div>

                        </div>



                        <div class="absolute bottom-24 flex flex-col items-center">
                            <p class="font-medium px-3 py-1 text-xl text-white rounded-md self-start hover:bg-opacity-75 transition-colors duration-300"
                                id="visualstatus">Status: In Progress</p>
                        </div>

                        <div class="absolute bottom-0 left-0 p-4 font-medium">
                            <p id="visualduration">Duration: 3 hours</p>
                        </div>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 p-4 font-medium">
                            <p id="visualdate">Date: 2024-04-26</p>
                        </div>
                        <div class="absolute bottom-0 right-0 p-4 font-medium">
                            <p id="visualdifficulty">Difficulty: Hard</p>
                        </div>


                    </div>
                </div>
            </div>







            <script>


                function updateBuzzNotify(subtaskID, taskID, userID) {
                    // Create FormData object
                    var formData = new FormData();
                    formData.append('SubtaskID', subtaskID);
                    formData.append('TaskID', taskID);
                    formData.append('UserID', userID);

                    // Send AJAX request
                    $.ajax({
                        type: "POST",
                        url: "buzz/updatebuzznotify.php",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            // Handle success response if needed
                            console.log(response);
                        },
                        error: function (xhr, status, error) {
                            // Handle error response if needed
                            console.error(xhr.responseText);
                        }
                    });
                }

                // Add event listener for Enter key press on textarea
                document.getElementById("chatinput").addEventListener("keypress", function (event) {
                    if (event.key === "Enter") {
                        sendbuzz();
                    }
                });

                // Add event listener for click on ringcircle
                document.getElementById("ringcircle").addEventListener("click", function () {
                    toggleChatModal(true);
                    sendbuzz();
                });

                // Modified sendbuzz function
                function sendbuzz() {
                    var userID = document.getElementById("chatuserid").textContent;
                    var subtaskID = document.getElementById("chatsubtaskid").textContent;
                    var buzzMessage = document.getElementById("chatinput").value;

                    var taskID = getQueryParam('taskid');

                    console.log("buzztaskid" + taskID)
                    // Check if buzzMessage is not empty
                    if (buzzMessage.trim() !== "") {
                        var formData = new FormData();
                        formData.append('UserID', userID);
                        formData.append('SubtaskID', subtaskID);
                        formData.append('BuzzMessage', buzzMessage);
                        formData.append('TaskID', taskID); // Add TaskID parameter


                        $.ajax({
                            url: "buzz/buzzchats.php",
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                console.log("Message Sent: " + response);
                                fetchAndDisplayMessages(subtaskID)
                                document.getElementById("chatinput").value = "";
                            },
                            error: function (xhr, status, error) {
                                console.error("An error occurred: " + error);
                            }
                        });

                    }
                }


                function toggleChatModal(show) {
                    const modal = document.getElementById('chatModal');
                    modal.style.display = show ? 'flex' : 'none';

                    if (show) {
                        // Get the subtaskID
                        var subtaskID = document.getElementById("chatsubtaskid").textContent;
                        // Fetch and display messages
                        fetchAndDisplayMessages(subtaskID);
                    }
                }


                function fetchAndDisplayMessages(subtaskID) {
                    $.get("buzz/buzzchatread.php?SubtaskID=" + subtaskID, function (data) {
                        // Assuming the response from buzzchats.php is in HTML format
                        $("#chatmessagecontent").html(data);
                        // After updating the content, scroll to the bottom
                        $("#chatmessagecontent").scrollTop($("#chatmessagecontent")[0].scrollHeight);
                    });
                }




                function convertDurationToMinutes(durationString) {
                    // Remove "Duration: " from the string
                    durationString = durationString.replace("Duration: ", "");

                    // Split the string into individual components
                    let components = durationString.split(" ");

                    let totalMinutes = 0;

                    for (let i = 0; i < components.length; i += 2) {
                        let value = parseInt(components[i]);
                        let unit = components[i + 1].toLowerCase();

                        // Convert each component to minutes and add to total
                        switch (unit) {
                            case 'day':
                            case 'days':
                                totalMinutes += value * 24 * 60;
                                break;
                            case 'hour':
                            case 'hours':
                                totalMinutes += value * 60;
                                break;
                            case 'min':
                            case 'mins':
                            case 'minute':
                            case 'minutes':
                                totalMinutes += value;
                                break;
                            default:
                                console.log("Invalid unit:", unit);
                                break;
                        }
                    }

                    return totalMinutes.toString(); // Convert total minutes to a string
                }
                function rgbToHex(rgb) {
                    // Parse the RGB string to extract the individual color values
                    const [r, g, b] = rgb.match(/\d+/g);

                    // Convert each color value to hex and concatenate them
                    const hexR = parseInt(r).toString(16).padStart(2, '0');
                    const hexG = parseInt(g).toString(16).padStart(2, '0');
                    const hexB = parseInt(b).toString(16).padStart(2, '0');

                    // Return the hex code
                    return `#${hexR}${hexG}${hexB}`;
                }



                function openBuzzModal() {
                    const buzznotificationmodal = document.getElementById('buzznotificationmodal');
                    buzznotificationmodal.classList.remove('hidden');
                }

                function closeBuzzModal() {
                    const buzznotificationmodal = document.getElementById('buzznotificationmodal');
                    buzznotificationmodal.classList.add('hidden');
                }

                function accessadd() {
                    var collaborator = document.getElementById("editcollaboration").value;
                    var subtaskID = document.getElementById("editsubtaskID").value; // Assumed to exist in your HTML

                    // Ensure a collaborator is selected
                    if (!collaborator) {
                        alert("Please select a collaborator.");
                        return;
                    }

                    var formData = {
                        'AssignedTo': collaborator,
                        'SubtaskID': subtaskID,
                        'TaskID': '<?php echo $taskID; ?>',
                        'AccessOption': 'Editor' // Given as a constant based on your description
                    };

                    $.ajax({
                        url: "subtask/subtaskaccess.php",
                        type: "POST",
                        data: formData,
                        success: function (response) {
                            console.log(response); // Adjusted to output the server response
                            updateaccess(subtaskID);



                        },
                        error: function (xhr, status, error) {
                            console.error("An error occurred: " + error);
                        }
                    });
                }





                function toggleEditSettingsContent() {
                    var editSettingsContent = document.getElementById("editsettingscontent");
                    var editLogContent = document.getElementById("editlogcontent");
                    var editlogsdiv = document.getElementById("editlogsdiv");

                    var editSubtaskID = document.getElementById("editsubtaskID").value;
                    console.log(editSubtaskID);

                    editSettingsContent.classList.toggle("hidden");
                    editLogContent.classList.toggle("hidden");


                    editlogsdiv2.classList.toggle("hidden");

                    document.getElementById("editloglist").innerHTML = "";

                    // Update label colors based on edit log content visibility
                    var isEditLogShown = !editLogContent.classList.contains("hidden");
                    var editLogsLabel = document.getElementById("editlogslabel");
                    editLogsLabel.classList.remove(isEditLogShown ? "text-gray-600" : "text-white");
                    editLogsLabel.classList.add(isEditLogShown ? "text-white" : "text-gray-600");

                    var editsubtasklabel = document.getElementById("editsubtasklabel");

                    // Hide editsubtasklabel when editlogcontent is displayed, and show it again when hidden
                    if (isEditLogShown) {
                        editsubtasklabel.classList.add("hidden");
                        editlogsdiv.classList.add("bg-gray-500");
                    } else {
                        editsubtasklabel.classList.remove("hidden");
                        editlogsdiv.classList.remove("bg-gray-500");
                    }

                    // Using the locally defined editSubtaskID
                    $.get("subtask/editlogread.php?subtaskID=" + editSubtaskID, function (data) {
                        $("#editloglist").html(data);
                    });
                }







                function updateColorCode(colorInputId, codeInputId, isColorToText) {

                    const colorInput = document.getElementById(colorInputId);
                    const codeInput = document.getElementById(codeInputId);
                    if (isColorToText) {
                        // Update text input from color input
                        codeInput.value = colorInput.value;
                    } else {
                        // Update color input from text input
                        colorInput.value = codeInput.value;
                    }
                }

                function updateVisualStatus(status) {
                    let statusElement = document.getElementById('visualstatus');

                    // Clear existing classes
                    statusElement.classList.remove('bg-yellow-600', 'bg-red-600', 'bg-green-600');

                    // Set classes based on status
                    switch (status) {
                        case 'Pending':
                        case 'Pending (Revise)':

                        case 'Pending (Short-Term)':
                        case 'Pending (Long-Term)':
                        case 'Pending (To-Follow)':
                            statusElement.classList.add('bg-yellow-600');
                            break;
                        case 'Ongoing':
                            statusElement.classList.add('bg-red-600');
                            break;
                        case 'Done':
                        case 'Done (For Review)':

                            statusElement.classList.add('bg-green-600');
                            break;
                        default:
                            // Handle other statuses if needed
                            break;
                    }
                }
                let timerInterval; // Declare timerInterval outside the functions

                function openVisualModal(subtaskID, subtaskTitle, subtaskDescription, subtaskDuration, subtaskDifficulty, backgroundColor, textColor, subtaskStatus, markNumber, subtaskStartTime, subtaskEndTime, subtaskPausedDuration, subtaskCreationDate, subtaskImage) {
                    // Clear any ongoing timer interval when opening the modal
                    clearInterval(timerInterval);

                    console.log('Subtask ID:', subtaskID);
                    console.log('Subtask Title:', subtaskTitle);
                    console.log('Subtask Description:', subtaskDescription);
                    console.log('Subtask Duration:', subtaskDuration);
                    console.log('Subtask Difficulty:', subtaskDifficulty);
                    console.log('Background Color:', backgroundColor);
                    console.log('Text Color:', textColor);
                    console.log('Subtask Status:', subtaskStatus);
                    console.log('Mark Number:', markNumber);
                    console.log('Subtask Start Time:', subtaskStartTime);
                    console.log('Subtask End Time:', subtaskEndTime);
                    console.log('Subtask Paused Duration:', subtaskPausedDuration);

                    var userID = '<?php echo $_SESSION["userID"]; ?>';

                    document.getElementById('chatuserid').innerText = userID;
                    document.getElementById('chatsubtaskid').innerText = subtaskID;

                    document.getElementById('visualSubtaskID').innerText = subtaskID; // Setting subtaskID

                    document.getElementById('visualModal').classList.remove('hidden');
                    subtaskDescription = subtaskDescription.replace(/147linebreakbymatthew/g, '\n');

                    // Set content based on passed values
                    document.getElementById('visualtitle').innerText = subtaskTitle;
                    document.getElementById('visualstatus').innerText = "Status: " + subtaskStatus;

                    document.getElementById('visualdescription').innerText = subtaskDescription;


                    // Get the ringbell element
                    var ringbell = document.getElementById('ringbell');

                    // Remove "#" from backgroundColor
                    var specialbg = backgroundColor.replace('#', '');

                    // Replace the color code in the src attribute
                    var newSrc = ringbell.src.replace(/FFFFF/g, specialbg);

                    // Set the new src attribute
                    ringbell.src = newSrc;

                    var visualmenu = document.getElementById('visualmenu');
                    visualmenu.style.fill = textColor;

                    // Format the duration in days, hours, and minutes
                    let durationInMinutes = parseInt(subtaskDuration, 10);
                    let days = Math.floor(durationInMinutes / (24 * 60));
                    let hours = Math.floor((durationInMinutes % (24 * 60)) / 60);
                    let minutes = durationInMinutes % 60;

                    let formattedDuration = "";
                    if (days > 0) {
                        formattedDuration += days + " day" + (days > 1 ? "s " : " ");
                    }
                    if (hours > 0) {
                        formattedDuration += hours + " hour" + (hours > 1 ? "s " : " ");
                    }
                    if (minutes > 0) {
                        formattedDuration += minutes + " min" + (minutes > 1 ? "s" : "");
                    }

                    document.getElementById('visualduration').innerText = "Duration: " + formattedDuration;

                    // Check if subtaskImage is not empty, then display it
                    if (subtaskImage !== '') {
                        document.getElementById('visualimageplacer').innerHTML = `<img src="${subtaskImage}" alt="Subtask Image" class="object-contain max-h-full cursor-pointer" onclick="openImageModal('${subtaskImage}')">`;
                        document.getElementById('visualimageplacer').classList.remove('hidden');
                    } else {
                        // Hide visualimageplacer if subtaskImage is empty
                        document.getElementById('visualimageplacer').innerHTML = '';
                        document.getElementById('visualimageplacer').classList.add('hidden');
                    }

                    // Format the creation date including hours and minutes
                    var formattedCreationDate = new Date(subtaskCreationDate);
                    formattedCreationDate = formattedCreationDate.toLocaleDateString('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // Set visual date based on status
                    if (subtaskStatus !== 'Done' && subtaskStatus !== 'Done (For Review)') {
                        document.getElementById('visualdate').innerText = "Made on " + formattedCreationDate;
                    } else {
                        // Format the end time including hours and minutes
                        var formattedEndTime = new Date(subtaskEndTime);
                        formattedEndTime = formattedEndTime.toLocaleDateString('en-US', {
                            month: 'short',
                            day: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        document.getElementById('visualdate').innerText = "Done on " + formattedEndTime;
                    }

                    document.getElementById('ringcircle').style.backgroundColor = textColor;
                    document.getElementById('chatheader').style.backgroundColor = backgroundColor;
                    document.getElementById('chatheader').style.color = textColor;

                    document.getElementById('enterchatbutton').style.backgroundColor = backgroundColor;
                    document.getElementById('enterchatbutton').style.color = textColor;



                    document.getElementById('chatheaderlabel').style.color = textColor;

                    document.getElementById('chatheader').style.filter = 'brightness(85%)';


                    document.getElementById('visualdifficulty').innerText = "Difficulty: " + subtaskDifficulty;
                    document.getElementById('visualmarknumber').querySelector('p').innerText = markNumber;

                    // Set background color and text color
                    document.getElementById('visualModalcontent').style.backgroundColor = backgroundColor;
                    document.getElementById('visualModal').style.color = textColor;

                    document.getElementById('visualmarknumbercircle').style.backgroundColor = textColor;
                    document.getElementById('visualmarknumber').style.color = backgroundColor;

                    // Update visual status
                    updateVisualStatus(subtaskStatus);

                    // Update visual timer based on status
                    updateVisualTimer(subtaskStatus, subtaskStartTime, subtaskEndTime, subtaskPausedDuration);

                    // Hide visualmarknumber if markNumber is empty
                    if (markNumber === '') {
                        document.getElementById('visualmarknumber').classList.add('hidden');
                    } else {
                        document.getElementById('visualmarknumber').classList.remove('hidden');
                    }
                }

                function updateVisualTimer(subtaskStatus, subtaskStartTime, subtaskEndTime, subtaskPausedDuration) {
                    const visualTimerContainer = document.getElementById('visualtimer').parentNode;

                    // Clear any ongoing timer interval before starting a new one
                    clearInterval(timerInterval);

                    if (subtaskStatus === "Pending" || subtaskStatus === "Pending (Revise)" || subtaskStatus === "Pending (To-Follow)" || subtaskStatus === "Pending (Short-Term)" || subtaskStatus === "Pending (Long-Term)") {
                        document.getElementById('visualtimer').classList.add('text-yellow-500');
                        document.getElementById('visualtimer').innerText = "-- : --";
                        visualTimerContainer.style.display = "flex";
                        // If subtaskPausedDuration is not null, display it
                        if (subtaskPausedDuration !== null) {
                            const [hours, minutes, seconds] = subtaskPausedDuration.split(':').map(Number);
                            const formattedHours = hours.toString().padStart(2, '0');
                            const formattedMinutes = minutes.toString().padStart(2, '0');
                            const formattedSeconds = seconds.toString().padStart(2, '0');
                            let timerText = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                            // Remove hours if they are zero
                            if (hours === 0) {
                                timerText = `${formattedMinutes}:${formattedSeconds}`;
                            }
                            document.getElementById('visualtimer').innerText = timerText;
                        }
                    } else if (subtaskStatus === "Ongoing") {
                        // Show the timer
                        visualTimerContainer.style.display = "flex";

                        // Calculate difference excluding paused duration
                        const startTime = new Date(subtaskStartTime);
                        const endTime = new Date();
                        let diff = endTime - startTime;

                        // If subtaskPausedDuration is not null or empty, add it to the difference
                        if (subtaskPausedDuration && subtaskPausedDuration.trim() !== "") {
                            const [pausedHours, pausedMinutes, pausedSeconds] = subtaskPausedDuration.split(':').map(Number);
                            const pausedDurationMillis = (pausedHours * 3600 + pausedMinutes * 60 + pausedSeconds) * 1000;
                            diff += pausedDurationMillis;
                        }

                        // Update the timer every second
                        timerInterval = setInterval(() => {
                            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                            // Add leading zeros to minutes and seconds if less than 10
                            const formattedMinutes = minutes.toString().padStart(2, '0');
                            const formattedSeconds = seconds.toString().padStart(2, '0');

                            let timerText = "";
                            // Format timerText based on the presence of hours
                            if (hours > 0) {
                                const formattedHours = hours.toString().padStart(2, '0');
                                timerText = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                            } else {
                                timerText = `${formattedMinutes}:${formattedSeconds}`;
                            }

                            document.getElementById('visualtimer').innerText = timerText;
                            document.getElementById('visualtimer').classList.remove('text-green-500');
                            document.getElementById('visualtimer').classList.remove('text-yellow-500');
                            document.getElementById('visualtimer').classList.add('text-red-500');

                            // Decrease the remaining time every second
                            diff += 1000;

                            // Stop the timer when it reaches zero
                            if (diff < 0) {
                                clearInterval(timerInterval);
                                document.getElementById('visualtimer').innerText = "00:00";
                                // Optionally, you can add any additional logic when the timer reaches zero
                            }
                        }, 1000);
                    } else if (subtaskStatus === "Done" || subtaskStatus === "Done (For Review)") {
                        // Show the timer
                        visualTimerContainer.style.display = "flex";

                        // Update the timer
                        const startTime = new Date(subtaskStartTime);
                        const endTime = new Date(subtaskEndTime);

                        // Calculate difference excluding paused duration
                        let diff = endTime - startTime;

                        // If subtaskPausedDuration is not null or empty, add it to the difference
                        if (subtaskPausedDuration && subtaskPausedDuration.trim() !== "") {
                            const [pausedHours, pausedMinutes, pausedSeconds] = subtaskPausedDuration.split(':').map(Number);
                            const pausedDurationMillis = (pausedHours * 3600 + pausedMinutes * 60 + pausedSeconds) * 1000;
                            diff += pausedDurationMillis;
                        }

                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff / (1000 * 60)) % 60);
                        const seconds = Math.floor((diff / 1000) % 60);

                        let timerText = "";
                        // Format timerText based on the presence of hours
                        if (hours > 0) {
                            timerText = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        } else {
                            timerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        }

                        document.getElementById('visualtimer').innerText = timerText;

                        // Set text color using Tailwind CSS classes based on subtask status
                        document.getElementById('visualtimer').classList.remove('text-red-500');
                        document.getElementById('visualtimer').classList.add('text-green-500');
                    }
                }



                function closeVisualModal() {
                    toggleChatModal(false);

                    clearInterval(timerInterval);
                    resetVisualTimer();
                    document.getElementById('visualModal').classList.add('hidden');
                }

                function resetVisualTimer() {
                    document.getElementById('visualtimer').innerText = "-";
                    document.getElementById('visualtimer').classList.remove('text-red-500', 'text-green-500', 'text-yellow-500');
                    document.getElementById('visualtimer').classList.add('text-black');
                }



                // Inside the openEditModal function
                function openEditModal(subtaskID, subtaskTitle, subtaskDescription, subtaskDuration, subtaskDifficulty, bgColor, textColor, status, markNumber) {
                    console.log("Subtask ID:", subtaskID);
                    console.log("Subtask Title:", subtaskTitle);
                    console.log("Subtask Description:", subtaskDescription);
                    console.log("Subtask Duration:", subtaskDuration);
                    console.log("Subtask Difficulty:", subtaskDifficulty);
                    console.log("Subtask Status:", status);
                    console.log("Subtask Mark Number:", markNumber); // Log Mark Number

                    var userID = '<?php echo $_SESSION["userID"]; ?>';


                    console.log("User ID:", userID);

                    // Replace placeholder string with line breaks
                    // Inside the openEditModal function
                    subtaskDescription = subtaskDescription.replace(/147linebreakbymatthew/g, '\n');

                    // Set input values based on subtask data
                    document.getElementById('editsubtaskTitle').value = subtaskTitle;
                    document.getElementById('editsubtaskDescription').value = subtaskDescription;

                    // Distribute subtaskDuration to editdurationdays, editdurationhours, editduration
                    let totalMinutes = parseInt(subtaskDuration); // Convert duration to minutes
                    let days = Math.floor(totalMinutes / (24 * 60)); // Calculate days
                    let hours = Math.floor((totalMinutes % (24 * 60)) / 60); // Calculate hours
                    let minutes = totalMinutes % 60; // Calculate remaining minutes

                    document.getElementById('editdurationdays').value = days;
                    document.getElementById('editdurationhours').value = hours;
                    document.getElementById('editduration').value = minutes;

                    document.getElementById('editdifficultyLevel').value = subtaskDifficulty;
                    document.getElementById('editStatus').value = status; // Set status

                    document.getElementById('bgColorCode').value = bgColor;
                    document.getElementById('textColorCode').value = textColor;

                    // Set the hidden input value with the SubtaskID
                    document.getElementById('editsubtaskID').value = subtaskID;

                    // Set background color and text color
                    document.getElementById('editBgColor').value = bgColor;
                    document.getElementById('editTextColor').value = textColor;

                    // Set Mark Number
                    document.getElementById('editMarkNumber').value = markNumber;


                    document.getElementById('editStatus').querySelector("option[value='Done']").style.display = 'none';


                    // Show or hide the "Done" option based on SubtaskStatus
                    if (status === 'Ongoing') {

                        document.getElementById('editStatus').querySelector("option[value='Pending (Revise)']").style.display = 'none';


                        document.getElementById('editStatus').querySelector("option[value='Pending']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Long-Term)']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Short-Term)']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (To-Follow)']").style.display = 'block';


                        document.getElementById('editStatus').querySelector("option[value='Done (For Review)']").style.display = 'block';

                    }
                    else if (status === 'Pending (Revise)') {
                        document.getElementById('editStatus').querySelector("option[value='Pending']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Long-Term)']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Short-Term)']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Pending (To-Follow)']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Done']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Done (For Review)']").style.display = 'none';
                    }
                    else if (status === 'Pending (Short-Term)' || status === 'Pending (Long-Term)' || status === 'Pending' || status === 'Pending (To-Follow)') {
                        document.getElementById('editStatus').querySelector("option[value='Pending (Revise)']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Done']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Done (For Review)']").style.display = 'none';
                        document.getElementById('editStatus').querySelector("option[value='Pending']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Long-Term)']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Short-Term)']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (To-Follow)']").style.display = 'block';
                    }


                    else if (status === 'Done (For Review)') {
                        document.getElementById('editStatus').querySelector("option[value='Pending']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Long-Term)']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (Short-Term)']").style.display = 'block';
                        document.getElementById('editStatus').querySelector("option[value='Pending (To-Follow)']").style.display = 'block';
                    }







                    $.ajax({
                        type: "POST",
                        url: "subtask/fetchaccess.php",
                        data: { subtaskID: subtaskID },
                        success: function (response) {
                            var collaboratorList = document.getElementById('collaboratorlist');
                            collaboratorList.innerHTML = response;

                            // Add delete button next to each collaborator
                            var collaborators = collaboratorList.getElementsByTagName('li');
                            for (var i = 0; i < collaborators.length; i++) {
                                var deleteButton = document.createElement('button');
                                deleteButton.className = 'bg-red-500 text-white px-2 rounded hover:bg-red-600 ml-2';
                                deleteButton.innerHTML = '&#10005;';
                                deleteButton.onclick = function () {
                                    var AssignedTo = this.parentElement.innerText.trim().slice(0, -1); // Remove the X symbol
                                    console.log("AssignedTo:", AssignedTo);
                                    console.log("subtaskID:", subtaskID);
                                    deleteCollaborator(subtaskID, AssignedTo);

                                };

                                collaborators[i].appendChild(deleteButton);
                            }

                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX error:", error); // Log AJAX error
                        }
                    });


                    document.getElementById("editmodal").style.display = "block";
                }


                function deleteCollaborator(subtaskID, AssignedTo) {

                    // AJAX request to delete collaborator record
                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration//deleteaccess.php",
                        data: { subtaskID: subtaskID, AssignedTo: AssignedTo },
                        success: function (response) {
                            updateaccess(subtaskID);

                            console.log(response); // Log the response from the server
                        }
                    });
                }

                function updateaccess(subtaskID) {
                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration//fetchaccess.php",
                        data: { subtaskID: subtaskID },
                        success: function (response) {
                            var collaboratorList = document.getElementById('collaboratorlist');
                            collaboratorList.innerHTML = response;

                            // Add delete button next to each collaborator
                            var collaborators = collaboratorList.getElementsByTagName('li');
                            for (var i = 0; i < collaborators.length; i++) {
                                var deleteButton = document.createElement('button');
                                deleteButton.className = 'bg-red-500 text-white px-2 rounded hover:bg-red-600 ml-2';
                                deleteButton.innerHTML = '&#10005;';
                                deleteButton.onclick = function () {
                                    var AssignedTo = this.parentElement.innerText.trim().slice(0, -1); // Remove the X symbol
                                    console.log("AssignedTo:", AssignedTo);
                                    console.log("subtaskID:", subtaskID);
                                    deleteCollaborator(subtaskID, AssignedTo);

                                };

                                collaborators[i].appendChild(deleteButton);
                            }

                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX error:", error); // Log AJAX error
                        }
                    });
                }

                function closeEditModal() {
                    document.getElementById("editmodal").style.display = "none";
                    document.getElementById("editsettingscontent").classList.remove("hidden");
                    document.getElementById("editlogcontent").classList.add("hidden");
                    document.getElementById("editlogsdiv2").classList.add("hidden");

                    document.getElementById("editcollaboration").value = "";


                    // Update label colors based on edit log content visibility
                    var editLogsLabel = document.getElementById("editlogslabel");
                    editLogsLabel.classList.remove("text-white");
                    editLogsLabel.classList.add("text-gray-600");

                    var editsubtasklabel = document.getElementById("editsubtasklabel");
                    editsubtasklabel.classList.remove("hidden");
                    document.getElementById("editlogsdiv").classList.remove("bg-gray-500");
                }


                function stopPropagation(event) {
                    event.stopPropagation();
                }





                function toggleDateDetails(element) {
                    var detailsSpan = element.querySelector('span');
                    if (detailsSpan.classList.contains('hidden')) {
                        detailsSpan.classList.remove('hidden');
                    } else {
                        detailsSpan.classList.add('hidden');
                    }
                }

                function addCheckmark() {
                    const textarea = document.getElementById('editsubtaskDescription');
                    textarea.value += '☐ '; // Appends the checkmark at a new line
                    textarea.focus(); // Optionally set focus back to the textarea
                }

                function checkForBox(event) {
                    const textarea = event.target;
                    const cursorPosition = textarea.selectionStart;
                    const text = textarea.value;
                    const boxIndex = text.indexOf("☐", cursorPosition);

                    if (boxIndex !== -1) {
                        textarea.style.cursor = "pointer";
                    } else {
                        textarea.style.cursor = "auto";
                    }
                }

                function checkForBoxClick(event) {
                    const textarea = event.target;
                    const cursorPosition = textarea.selectionStart;
                    const text = textarea.value;
                    const boxIndex = text.indexOf("☐", cursorPosition);

                    if (boxIndex !== -1) {
                        const newText = text.substring(0, boxIndex) + "☑" + text.substring(boxIndex + 1);
                        textarea.value = newText;
                    } else {
                        const checkIndex = text.indexOf("☑", cursorPosition);
                        if (checkIndex !== -1) {
                            const newText = text.substring(0, checkIndex) + "☐" + text.substring(checkIndex + 1);
                            textarea.value = newText;
                        }
                    }
                }

                // Add event listener for double-click to toggle checkmark
                document.getElementById('editsubtaskDescription').addEventListener('dblclick', checkForBoxClick);

                function updateTimer() {
                    <?php
                    // Loop through each ongoing or done subtask to calculate and update the timer
                    $result = mysqli_query($link, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($row['SubtaskStatus'] == 'Ongoing') {
                            if ($row['SubtaskPausedDuration'] !== null) {
                                // If SubtaskPausedDuration is not null, use it as the timer value
                                $pausedDuration = $row['SubtaskPausedDuration'];
                                ?>
                                var startTime_<?php echo $row['SubtaskID']; ?> = new Date("<?php echo $row['SubtaskStartTime']; ?>").getTime();
                                var now_<?php echo $row['SubtaskID']; ?> = new Date().getTime();
                                var distance_<?php echo $row['SubtaskID']; ?> = now_<?php echo $row['SubtaskID']; ?> - startTime_<?php echo $row['SubtaskID']; ?>;

                                // Parse SubtaskPausedDuration into milliseconds
                                var pausedDurationParts = "<?php echo $pausedDuration; ?>".split(':');
                                var pausedDurationMillis = (parseInt(pausedDurationParts[0]) * 60 * 60 * 1000) + (parseInt(pausedDurationParts[1]) * 60 * 1000) + (parseInt(pausedDurationParts[2]) * 1000);

                                // Add pausedDurationMillis to the current distance
                                var totalDistance_<?php echo $row['SubtaskID']; ?> = distance_<?php echo $row['SubtaskID']; ?> + pausedDurationMillis;

                                var hours_<?php echo $row['SubtaskID']; ?> = Math.floor((totalDistance_<?php echo $row['SubtaskID']; ?> % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes_<?php echo $row['SubtaskID']; ?> = Math.floor((totalDistance_<?php echo $row['SubtaskID']; ?> % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds_<?php echo $row['SubtaskID']; ?> = Math.floor((totalDistance_<?php echo $row['SubtaskID']; ?> % (1000 * 60)) / 1000);

                                // Add leading zeros to minutes and seconds if less than 10
                                minutes_<?php echo $row['SubtaskID']; ?> = minutes_<?php echo $row['SubtaskID']; ?>.toString().padStart(2, '0');
                                seconds_<?php echo $row['SubtaskID']; ?> = seconds_<?php echo $row['SubtaskID']; ?>.toString().padStart(2, '0');

                                // Display the timer, include hours only if they are non-zero
                                var timeString_<?php echo $row['SubtaskID']; ?> = (hours_<?php echo $row['SubtaskID']; ?> > 0 ? hours_<?php echo $row['SubtaskID']; ?>.toString().padStart(2, '0') + ":" : "") + minutes_<?php echo $row['SubtaskID']; ?> + ":" + seconds_<?php echo $row['SubtaskID']; ?>;
                                document.getElementById("timer_<?php echo $row['SubtaskID']; ?>").innerHTML = timeString_<?php echo $row['SubtaskID']; ?>;
                                <?php
                            } else {
                                // Otherwise, calculate the timer normally
                                ?>
                                var startTime_<?php echo $row['SubtaskID']; ?> = new Date("<?php echo $row['SubtaskStartTime']; ?>").getTime();
                                var now_<?php echo $row['SubtaskID']; ?> = new Date().getTime();
                                var distance_<?php echo $row['SubtaskID']; ?> = now_<?php echo $row['SubtaskID']; ?> - startTime_<?php echo $row['SubtaskID']; ?>;
                                var hours_<?php echo $row['SubtaskID']; ?> = Math.floor((distance_<?php echo $row['SubtaskID']; ?> % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes_<?php echo $row['SubtaskID']; ?> = Math.floor((distance_<?php echo $row['SubtaskID']; ?> % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds_<?php echo $row['SubtaskID']; ?> = Math.floor((distance_<?php echo $row['SubtaskID']; ?> % (1000 * 60)) / 1000);

                                // Add leading zeros to minutes and seconds if less than 10
                                minutes_<?php echo $row['SubtaskID']; ?> = minutes_<?php echo $row['SubtaskID']; ?>.toString().padStart(2, '0');
                                seconds_<?php echo $row['SubtaskID']; ?> = seconds_<?php echo $row['SubtaskID']; ?>.toString().padStart(2, '0');

                                // Display the timer, include hours only if they are non-zero
                                var timeString_<?php echo $row['SubtaskID']; ?> = (hours_<?php echo $row['SubtaskID']; ?> > 0 ? hours_<?php echo $row['SubtaskID']; ?>.toString().padStart(2, '0') + ":" : "") + minutes_<?php echo $row['SubtaskID']; ?> + ":" + seconds_<?php echo $row['SubtaskID']; ?>;
                                document.getElementById("timer_<?php echo $row['SubtaskID']; ?>").innerHTML = timeString_<?php echo $row['SubtaskID']; ?>;
                                <?php
                            }
                        } elseif ($row['SubtaskStatus'] == 'Done' || $row['SubtaskStatus'] == 'Done (For Review)') {
                            $startTime = new DateTime($row['SubtaskStartTime']);
                            $endTime = new DateTime($row['SubtaskEndTime']);

                            // If SubtaskPausedDuration is not null, add it to the total duration
                            if ($row['SubtaskPausedDuration'] !== null) {
                                // Parse SubtaskPausedDuration into seconds
                                $pausedDurationParts = explode(':', $row['SubtaskPausedDuration']);
                                $pausedDurationSeconds = $pausedDurationParts[0] * 3600 + $pausedDurationParts[1] * 60 + $pausedDurationParts[2];

                                // Add paused duration to the total duration
                                $totalDurationSeconds = $startTime->diff($endTime)->format('%s');
                                $totalDurationSeconds += $pausedDurationSeconds;

                                // Convert total duration back to hours, minutes, seconds
                                $hours = floor($totalDurationSeconds / 3600);
                                $minutes = floor(($totalDurationSeconds % 3600) / 60);
                                $seconds = $totalDurationSeconds % 60;

                                // Calculate efficiency rate
                                $subtaskDuration = $row['SubtaskDuration'];
                                $efficiencyRate = round(($subtaskDuration / ($totalDurationSeconds / 60)) * 100);

                                // Generate time string conditionally displaying hours
                                $timeString = ($hours > 0 ? str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' : '') . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
                            } else {
                                // Calculate duration normally
                                $interval = $endTime->diff($startTime);
                                $hours = $interval->format('%h');
                                $minutes = $interval->format('%i');
                                $seconds = $interval->format('%s');

                                // Calculate efficiency rate
                                $subtaskDuration = $row['SubtaskDuration'];
                                $totalSeconds = $hours * 3600 + $minutes * 60 + $seconds;
                                $efficiencyRate = round(($subtaskDuration / ($totalSeconds / 60)) * 100);

                                // Generate time string conditionally displaying hours
                                $timeString = ($hours > 0 ? str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' : '') . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
                            }

                            ?>
                            document.getElementById("timer_<?php echo $row['SubtaskID']; ?>").innerHTML = "<?php echo $timeString ?>";
                            // Display efficiency rate
                            document.getElementById("efficiency_rate_<?php echo $row['SubtaskID']; ?>").innerHTML = "<?php echo $efficiencyRate ?>%";
                            <?php
                        }
                    }
                    ?>
                }

                setInterval(updateTimer, 1000);


                function openImageModal(imagePath) {
                    // Show modal
                    document.getElementById('imageModal').classList.remove('hidden');
                    // Set image source
                    document.getElementById('modalImage').src = imagePath;
                }


                function closeImageModal(event) {
                    // Check if the click happened on the modal itself or its children
                    if (event.target.id === 'imageModal' || event.target.tagName === 'BUTTON') {
                        document.getElementById('imageModal').classList.add('hidden');
                    }
                }
                function reloadTaskList() {
                    // Retrieve task ID dynamically from URL parameters
                    var taskID = getQueryParam('taskid');
                    console.log("reloaded");
                    // AJAX request to load subtasks
                    $.get("sharedcollaboration/sharedsubtaskread.php?taskid=" + taskID, function (data) {
                        $("#subtaskList").html(data);
                    });
                }


                function logUpdatedInputs() {
                    var updatedTitle = document.getElementById("editsubtaskTitle").value;
                    var updatedDescription = document.getElementById("editsubtaskDescription").value;
                    var updatedDurationDays = parseInt(document.getElementById("editdurationdays").value) || 0; // Get days or default to 0
                    var updatedDurationHours = parseInt(document.getElementById("editdurationhours").value) || 0; // Get hours or default to 0
                    var updatedDurationMinutes = parseInt(document.getElementById("editduration").value) || 0; // Get minutes or default to 0
                    var updatedDurationInMinutes = (updatedDurationDays * 24 * 60) + (updatedDurationHours * 60) + updatedDurationMinutes; // Convert to minutes
                    var updatedDifficulty = document.getElementById("editdifficultyLevel").value;
                    var updatedStatus = document.getElementById("editStatus").value;
                    var updatedSubtaskID = document.getElementById("editsubtaskID").value;
                    var updatedBgColor = document.getElementById("editBgColor").value;
                    var updatedTextColor = document.getElementById("editTextColor").value;
                    var updatedMarkNumber = document.getElementById("editMarkNumber").value;

                    var userID = '<?php echo $_SESSION["userID"]; ?>';


                    // Handle uploaded image
                    var updatedImage = document.getElementById("editImage").files[0];

                    // Get the taskID
                    var taskID = '<?php echo $taskID; ?>';

                    // Use FormData to send all the data
                    var formData = new FormData();
                    formData.append('subtaskID', updatedSubtaskID);
                    formData.append('title', updatedTitle);
                    formData.append('description', updatedDescription);
                    formData.append('duration', updatedDurationInMinutes); // Send duration in minutes
                    formData.append('difficulty', updatedDifficulty);
                    formData.append('status', updatedStatus);
                    formData.append('bgColor', updatedBgColor);
                    formData.append('textColor', updatedTextColor);
                    formData.append('markNumber', updatedMarkNumber);
                    formData.append('image', updatedImage);
                    formData.append('taskID', taskID); // Append the taskID
                    formData.append('userID', userID);

                    // Send the updated data to the server using AJAX
                    $.ajax({
                        url: "subtask/subtaskedit.php",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data, status) {
                            console.log("Data: " + data + "\nStatus: " + status);
                            closeEditModal(); // Close the modal after updating
                            reloadTaskList();

                            // Reload the page if status is being updated to "Ongoing" or "Done"
                            if (formData.get('status') === "Ongoing" || formData.get('status') === "Done" || formData.get('status') === "Done (For Review)") {
                                location.reload(); // Reload the page
                            }
                        }
                    });

                }



                function deleteTask() {
                    var subtaskID = document.getElementById("editsubtaskID").value;
                    console.log("Subtask ID to delete:", subtaskID);

                    // Send the subtask ID to server using AJAX
                    $.post("subtask/subtaskdelete.php", {
                        subtaskID: subtaskID
                    }, function (data, status) {
                        console.log("Data: " + data + "\nStatus: " + status);
                        closeEditModal(); // Close the modal after deletion
                        reloadTaskList();

                    });
                }
            </script>
            <?php
        } else {
            // SQL error handling (optional)
            echo '<div class="container mx-auto mt-8 p-6 md:p-12 text-center text-white">You have no subtasks here.</div>';
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo '<script>window.location.href = "../login.html";</script>';

    echo "Please log in to view subtasks.";
}
mysqli_close($link);
?>