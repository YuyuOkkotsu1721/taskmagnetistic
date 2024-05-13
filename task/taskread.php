<?php
require_once('../config.php');
session_start();

if (isset($_SESSION["userID"])) {

    if (isset($_GET['id'])) {
        $ventureID = mysqli_real_escape_string($link, $_GET['id']);

        // Query to fetch user information based on userID
        $userquery = "SELECT UserUsername FROM user WHERE UserID = '{$_SESSION["userID"]}'";
        $userresult = mysqli_query($link, $userquery);
        $user = mysqli_fetch_assoc($userresult);
        $username = $user['UserUsername'];

        $order = 'asc'; // Default sorting order
        if (isset($_GET['order'])) {
            $validOrders = ['asc', 'desc', 'highToLow', 'lowToHigh'];
            $order = in_array($_GET['order'], $validOrders) ? $_GET['order'] : 'asc';
        }

        // Initial SQL query to select all tasks
        $sql = "SELECT TaskID, TaskTitle, TaskDescription, TaskDueDateTime, TaskPriority, TaskBackgroundColor, TaskTextColor, TaskCreationDate,
            (SELECT COUNT(*) FROM subtasks WHERE TaskID = tasks.TaskID) AS TotalSubtasks,
            (SELECT COUNT(*) FROM subtasks WHERE TaskID = tasks.TaskID AND SubtaskStatus = 'Done') AS CompletedSubtasks 
            FROM tasks WHERE VentureID = '$ventureID'";

        // Filter tasks based on status
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            switch ($status) {
                case 'Inactive':
                    $sql .= " HAVING CompletedSubtasks = 0";
                    break;
                case 'Active':
                    $sql .= " HAVING CompletedSubtasks > 0 AND CompletedSubtasks < TotalSubtasks";
                    break;
                case 'Completed':
                    $sql .= " HAVING CompletedSubtasks = TotalSubtasks AND TotalSubtasks > 0";
                    break;
                // For 'All' status, no additional filtering is needed
                default:
                    break;
            }
        }

        // Apply sorting order
        switch ($order) {
            case 'highToLow':
                $sql .= " ORDER BY FIELD(TaskPriority, 'High', 'Medium', 'Low'), TaskCreationDate DESC";
                break;
            case 'lowToHigh':
                $sql .= " ORDER BY FIELD(TaskPriority, 'Low', 'Medium', 'High'), TaskCreationDate DESC";
                break;
            default:
                $sql .= " ORDER BY TaskCreationDate $order";
                break;
        }

        $result = mysqli_query($link, $sql);

        ?>

        <?php

if (mysqli_num_rows($result) > 0) {
    ?>
    <div id="tgrid"class="grid grid-cols-2 gap-4 container mx-auto">
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
                // Determine the background color based on TaskBackgroundColor
                $bgColorStyle = $row['TaskBackgroundColor'] ? "style='background-color: {$row['TaskBackgroundColor']};'" : "style='background-color:;'"; // Default color for bg-blue-600
                $textColorStyle = $row['TaskTextColor'] ? "style='color: {$row['TaskTextColor']};'" : ""; // Apply text color
                ?>

                <!-- Main Content Container with curved corners -->
                <div id="container_<?php echo $row['TaskID']; ?>"
                    class="bg-blue-600 container mx-auto mt-8 p-6 md:p-12 rounded-xl relative max-w-3xl" 
                    
                    <?php echo $bgColorStyle; ?>>

                    <!-- Display Username -->
                    <div class="absolute top-0 left-0 mt-2 md:mt-4 ml-10 md:ml-16 hidden font-medium text-sm text-black "
                        id="currentUserUsername">
                        Username: <?php echo $username; ?>
                    </div>



                    <!-- Description -->
                    <div class="mb-6 md:mb-10">
                        <!-- Task Title Input -->
                        <input type="text"
                            class="text-2xl md:text-3xl font-bold mb-2 bg-transparent text-white outline-none border-none w-full mb-4"
                            id="TaskTitleInput" name="TaskTitleInput" value="<?php echo $row['TaskTitle']; ?>" <?php echo
                                   $textColorStyle; ?>><br>
                        <!-- Task Description Input -->
                        <textarea id="TaskDescriptionInput" name="TaskDescriptionInput" 
                        class="text-lg bg-transparent md:text-xl text-white outline-none border-none resize-none w-full overflow-hidden break-word" 
                        style="color: #ffffff;">The task of finishing the database project for PE- ADVANCED DATABASES LEC</textarea>

                    </div>
                    <!-- Creation Date -->
                    <div class="absolute top-0 right-0 mt-2 md:mt-4 mr-10 md:mr-16 text-white font-medium text-sm" <?php echo
                        $textColorStyle; ?>>
                        Created on
                        <?php echo date('m-d-Y h:i A', strtotime($row['TaskCreationDate'])); ?>
                    </div>
                    <!-- Menu Icon -->
                    <div class="absolute top-0 right-0 mt-2 md:mt-4 mr-2 md:mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 md:h-8 w-6 md:w-8 text-white cursor-pointer"
                            viewBox="0 0 20 20" fill="<?php echo $row['TaskTextColor']; ?>"
                            onclick="openEditModal('<?php echo $row['TaskID']; ?>', '<?php echo $row['TaskTitle']; ?>', '<?php echo $row['TaskDescription']; ?>', '<?php echo $row['TaskDueDateTime']; ?>', '<?php echo $row['TaskPriority']; ?>', '<?php echo $row['TaskBackgroundColor']; ?>', '<?php echo $row['TaskTextColor']; ?>', '<?php echo $row['TotalSubtasks']; ?>', '<?php echo $username; ?>')">
                            <path fill-rule="evenodd"
                                d="M3 10a2 2 0 114 0 2 2 0 01-4 0zM9 10a2 2 0 114 0 2 2 0 01-4 0zm6 0a2 2 0 114 0 2 2 0 01-4 0z"
                                clip-rule="evenodd" />
                        </svg>


                    </div>

                    <!-- Additional Elements -->
                    <div id="taskdetails"
                        class="absolute bottom-0 left-12 mb-4 flex flex-wrap justify-center space-x-1 ">
                        <!-- Completion Button -->
                        <button class="bg-gray-700 font-medium text-white text-sm responsive-curve1 rounded-l-full p-2 overflow-hidden break-words"
                            style="background-color: <?php echo $row['TaskTextColor']; ?>; color: <?php echo $row['TaskBackgroundColor']; ?>;">
                            Completion: <?php echo $row['CompletedSubtasks']; ?> / <?php echo $row['TotalSubtasks']; ?>
                        </button>
                        <!-- Due Date Button -->
                        <button class="bg-gray-700 font-medium text-sm responsive-curve2 p-2 overflow-hidden break-words" id="dueDateButton"
                            style="background-color: <?php echo $row['TaskTextColor']; ?>; color: <?php echo $row['TaskBackgroundColor']; ?>;">
                            Due Date: <?php echo date('m-d-Y h:i A', strtotime($row['TaskDueDateTime'])); ?>
                        </button>
                        <!-- Priority Button -->
                        <button class="bg-gray-700 font-medium text-white text-sm responsive-curve3 rounded-r-full p-2 overflow-hidden break-words" id="priorityButton"
                            style="background-color: <?php echo $row['TaskTextColor']; ?>; color: <?php echo $row['TaskBackgroundColor']; ?>;">
                            Priority: <?php echo $row['TaskPriority']; ?>
                        </button>
                    </div>


                    
                    <!-- "Next" button -->
                    <button class="absolute bottom-0  text-sm right-0 mb-4 mr-4 font-bold py-2 px-4 rounded"
                        style="background-color: <?php echo $row['TaskTextColor']; ?>; color: <?php echo $row['TaskBackgroundColor']; ?>;"
                        onclick="redirectToSubtask('<?php echo $row['TaskID']; ?>', '<?php echo $row['TaskTitle']; ?>')">See Subtasks</button>
                </div>




                <div class="fixed top-2 left-0 w-full h-full bg-black bg-opacity-50 hidden text-white " id="editmodal"
                    onclick="stopPropagation(event)">
                    <div class="absolute top-1/2 left-1/2 w-11/12 transform -translate-x-1/2 -translate-y-1/2 bg-gray-900 p-8 rounded-lg max-h-screen overflow-y-auto"
                        onclick="stopPropagation(event)">
                        <h2 class="text-2xl font-bold mb-4">Edit Task Settings</h2>
                        <p id="editcurrentUsername" class="text-white mb-4 hidden"></p>

                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Task Title</label>
                            <input type="text" id="editTaskTitle"
                                class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                        </div>
                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Task Description</label>
                            <textarea id="editTaskDescription"
                            class="bg-gray-800 text-white px-4 py-2 w-full h-48 rounded focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Due Date</label>
                            <input type="datetime-local" id="editDueDate"
                                class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                        </div>


                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Priority Level</label>
                            <select id="editPriorityLevel"
                                class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <label id="subtasktotals" class="block mb-2 hidden">Total Subtasks Remaining:</label>


                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Add Managers</label>
                            <div class="flex">
                                <input type="text" id="manageradd"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                                    placeholder="Add Managers" />
                                <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 ml-2"
                                    onclick="addManager()">Add</button>
                            </div>
                        </div>

                        <div class="mb-4" onclick="stopPropagation(event)">

                            <label class="block mb-2">Manager List:</label>
                            <div class="mb-4" onclick="stopPropagation(event)" id="managerlist">
                            </div>

                        </div>



                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Add Collaborators</label>
                            <div class="flex">
                                <input type="text" id="collaborationadd"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                                    placeholder="Add Collaborators" />
                                <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 ml-2"
                                    onclick="addCollaborator()">Add</button>
                            </div>
                        </div>

                        <div class="mb-4" onclick="stopPropagation(event)">

                            <label class="block mb-2">Collaborator List:</label>
                            <div class="mb-4" onclick="stopPropagation(event)" id="collaboratorlist">
                            </div>

                        </div>



                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Task Background Color</label>
                            <input type="color" id="editBgColor"
                                class="bg-gray-800 text-white w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                        </div>
                        <div class="mb-4" onclick="stopPropagation(event)">
                            <label class="block mb-2">Task Text Color</label>
                            <input type="color" id="editTextColor"
                                class="bg-gray-800 text-white  w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                        </div>
                        <input type="hidden" id="editTaskID" />
                        <div class="flex justify-between">
                            <button id="taskdeleteButton" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                                onclick="deleteTask()">Delete</button>
                            <div>
                                <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 mr-2" onclick="closeEditModal()"
                                    id="closeEditModal">Cancel</button>
                                <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600" id="editChanges"
                                    onclick="logUpdatedInputs()">Edit Changes</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="myalertModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
                    <div class="bg-white rounded shadow-md w-1/2">
                        <div class="flex items-center justify-between ">
                            <div class="font-bold text-xl text-purple-700 p-2 ml-1  ">Alert</div>
                            <button id="closealertModal"
                                class=" text-3xl text-purple-700 hover:text-purple-900 p-2 mr-2">&times;</button>
                        </div>
                        <div class="border-t border-gray-300 mb-1 w-full "></div>
                        <p id="alertText" class="p-3"></p>
                        <div class="border-t border-gray-300 mt-1 w-full"></div>
                        <div class="flex justify-end p-3">
                            <button id="closealertModalButton"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                OK
                            </button>
                        </div>
                    </div>
                </div>

                <?php
            }

            ?>
            <script>

                    // Get today's date in the format YYYY-MM-DDTHH:mm
                    var today = new Date();
                    var year = today.getFullYear();
                    var month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
                    var day = String(today.getDate()).padStart(2, '0');
                    var hours = String(today.getHours()).padStart(2, '0');
                    var minutes = String(today.getMinutes()).padStart(2, '0');
                  
                    // Format the date string correctly
                    var formattedToday = `${year}-${month}-${day}T${hours}:${minutes}`;
                  
                    // Set the min attribute of the input to today's date
                    document.getElementById('editDueDate').setAttribute('min', formattedToday);



function addCollaborator() {
    var taskID = currentTaskID;
    var collaboratorUsername = document.getElementById("collaborationadd").value;
    var updatedTaskID = document.getElementById("editTaskID").value; // Retrieve the value of editTaskID

    // Check if the entered collaborator username is the same as the current user's username
    var currentUserText = document.getElementById("editcurrentUsername").textContent.trim(); // Retrieve the current user text
    var currentUserUsername = currentUserText.split(":")[1].trim(); // Extract the username part
    if (collaboratorUsername.toLowerCase() === currentUserUsername.toLowerCase()) {
        openAlertModal("You cannot add your own username for collaboration.");
        return; // Exit function if the entered username is the same as the current user's username
    }

    // Check if a collaborator is selected
    var collaboratorID = "";
    if (collaboratorUsername !== "") {
        collaboratorID = updatedTaskID + "clb"; // Append "clb" to the TaskID for CollaboratorID
    }

    var collaboratorMaker = currentUserUsername;

    // AJAX request to check if the username exists in the users table
$.ajax({
    type: "POST",
    url: "log/checkusername.php",
    data: { 
        username: collaboratorUsername,
        taskID: updatedTaskID // Pass the TaskID
    },
    success: function (response) {
        if (response == "exists") {
            // If the username exists, proceed to add collaborator
            $.post("sharedcollaboration/addcollaborators.php", {
                collaboratorID: collaboratorID, // Pass the collaboratorID
                collaboratorUsername: collaboratorUsername,
                collaboratorMaker: collaboratorMaker, // Pass the collaboratorMaker
                taskID: updatedTaskID // Pass the TaskID
            }, function (data, status) {
                console.log("Data: " + data + "\nStatus: " + status);
                fetchCollaboratorsAndUpdateUI(taskID);
            });
        } else {
            openAlertModal("Username does not exist!");
        }
    }
});
}

function addManager() {
    var taskID = currentTaskID;
    var collaboratorUsername = document.getElementById("manageradd").value;
    var updatedTaskID = document.getElementById("editTaskID").value; // Retrieve the value of editTaskID

    // Check if the entered collaborator username is the same as the current user's username
    var currentUserText = document.getElementById("editcurrentUsername").textContent.trim(); // Retrieve the current user text
    var currentUserUsername = currentUserText.split(":")[1].trim(); // Extract the username part
    if (collaboratorUsername.toLowerCase() === currentUserUsername.toLowerCase()) {
        openAlertModal("You cannot add your own username for collaboration.");
        return; // Exit function if the entered username is the same as the current user's username
    }

    // Check if a collaborator is selected
    var collaboratorID = "";
    if (collaboratorUsername !== "") {
        collaboratorID = updatedTaskID + "clb"; // Append "clb" to the TaskID for CollaboratorID
    }

    var collaboratorMaker = currentUserUsername;

    // AJAX request to check if the username exists in the users table
$.ajax({
    type: "POST",
    url: "log/checkusername.php",
    data: { 
        username: collaboratorUsername,
        taskID: updatedTaskID // Pass the TaskID
    },
    success: function (response) {
        if (response == "exists") {
            // If the username exists, proceed to add collaborator
            $.post("sharedcollaboration/addmanagers.php", {
                collaboratorID: collaboratorID, // Pass the collaboratorID
                collaboratorUsername: collaboratorUsername,
                collaboratorMaker: collaboratorMaker, // Pass the collaboratorMaker
                taskID: updatedTaskID // Pass the TaskID
            }, function (data, status) {
                console.log("Data: " + data + "\nStatus: " + status);
                fetchManagersAndUpdateUI(taskID);
            });
        } else {
            openAlertModal("Username does not exist!");
        }
    }
});
}

                var currentTaskID;


                function openEditModal(taskID, taskTitle, taskDescription, dueDate, priorityLevel, bgColor, textColor, totalSubtasks, currentUserUsername) {
                    console.log("Task ID:", taskID);
                    console.log("Task Title:", taskTitle);
                    console.log("Task Description:", taskDescription);
                    console.log("Due Date:", dueDate);
                    console.log("Priority Level:", priorityLevel);
                    document.getElementById('editTaskTitle').value = taskTitle;
                    document.getElementById('editTaskDescription').value = taskDescription;
                    document.getElementById('editDueDate').value = dueDate;
                    document.getElementById('editPriorityLevel').value = priorityLevel;
                    document.getElementById('editTaskID').value = taskID;
                    document.getElementById('editBgColor').value = bgColor;
                    document.getElementById('editTextColor').value = textColor;
                    document.getElementById('subtasktotals').innerText = "Total Subtasks Remaining: " + totalSubtasks;
                    document.getElementById('editcurrentUsername').innerText = "Current User: " + currentUserUsername; // Set the currentUserUsername

                    currentTaskID = taskID;
                    // Select the delete button
                    var taskdeleteButton = document.getElementById('taskdeleteButton');
                    // Check if total subtasks is zero
                    if (totalSubtasks > 0) {
                        // Set background color to yellow
                        taskdeleteButton.style.backgroundColor = 'orange';
                    } else {
                        // Reset background color to default
                        taskdeleteButton.style.backgroundColor = '';
                        // Enable the button
                    }
                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration/fetchcollaborators.php",
                        data: { taskID: taskID },
                        success: function (response) {
                            // Display fetched collaborators on collaboratorlist
                            var collaboratorList = document.getElementById('collaboratorlist');
                            collaboratorList.innerHTML = response;collaboratorlist
                            // Add delete button next to each collaborator
                            var collaborators = collaboratorList.getElementsByTagName('li');
                            for (var i = 0; i < collaborators.length; i++) {
                                var deleteButton = document.createElement('button');
                                deleteButton.className = 'bg-red-500 text-white px-2 rounded hover:bg-red-600 ml-2';
                                deleteButton.innerHTML = '&#10005;';
                                deleteButton.onclick = function () {
                                    var collaboratorMember = this.parentElement.innerText.trim().slice(0, -1); // Remove the X symbol
                                    var collaboratorID = taskID + "clb";
                                    console.log("Collaborator Member:", collaboratorMember);
                                    console.log("Collaborator ID:", collaboratorID);
                                    deleteCollaborator(taskID, collaboratorMember);
                                };
                                collaborators[i].appendChild(deleteButton);
                            }
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration/fetchmanagers.php",
                        data: { taskID: taskID },
                        success: function (response) {
                            // Display fetched collaborators on collaboratorlist
                            var managerList = document.getElementById('managerlist');
                            managerList.innerHTML = response;

                            // Add delete button next to each collaborator
                            var managers = managerList.getElementsByTagName('li');
                            for (var i = 0; i < managers.length; i++) {
                                var deleteButton = document.createElement('button');
                                deleteButton.className = 'bg-red-500 text-white px-2 rounded hover:bg-red-600 ml-2';
                                deleteButton.innerHTML = '&#10005;';
                                deleteButton.onclick = function () {
                                    var collaboratorManager = this.parentElement.innerText.trim().slice(0, -1); // Remove the X symbol
                                    var collaboratorID = taskID + "clb";
                                    console.log("Collaborator Member:", collaboratorManager);
                                    console.log("Collaborator ID:", collaboratorID);
                                    deleteManager(taskID, collaboratorManager);
                                };
                                managers[i].appendChild(deleteButton);
                            }
                        }
                    });


                    document.getElementById("editmodal").style.display = "block";
                }


                function fetchCollaboratorsAndUpdateUI(taskID) {
                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration/fetchcollaborators.php",
                        data: { taskID: taskID },
                        success: function (response) {
                            // Display fetched collaborators on collaboratorlist
                            var collaboratorList = document.getElementById('collaboratorlist');
                            collaboratorList.innerHTML = response;

                            // Add delete button next to each collaborator
                            var collaborators = collaboratorList.getElementsByTagName('li');
                            for (var i = 0; i < collaborators.length; i++) {
                                var deleteButton = document.createElement('button');
                                deleteButton.className = 'bg-red-500 text-white px-2 rounded hover:bg-red-600 ml-2';
                                deleteButton.innerHTML = '&#10005;';
                                deleteButton.onclick = function () {
                                    var collaboratorMember = this.parentElement.innerText.trim().slice(0, -1); // Remove the X symbol
                                    var collaboratorID = taskID + "clb";
                                    console.log("Collaborator Member:", collaboratorMember);
                                    console.log("Collaborator ID:", collaboratorID);
                                    deleteCollaborator(taskID, collaboratorMember);

                                };

                                collaborators[i].appendChild(deleteButton);
                            }
                        }
                    });
                }

                function fetchManagersAndUpdateUI(taskID) {
                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration/fetchmanagers.php",
                        data: { taskID: taskID },
                        success: function (response) {
                            // Display fetched collaborators on collaboratorlist
                            var managerList = document.getElementById('managerlist');
                            managerList.innerHTML = response;

                            // Add delete button next to each collaborator
                            var managers = managerList.getElementsByTagName('li');
                            for (var i = 0; i < managers.length; i++) {
                                var deleteButton = document.createElement('button');
                                deleteButton.className = 'bg-red-500 text-white px-2 rounded hover:bg-red-600 ml-2';
                                deleteButton.innerHTML = '&#10005;';
                                deleteButton.onclick = function () {
                                    var collaboratorManager = this.parentElement.innerText.trim().slice(0, -1); // Remove the X symbol
                                    var collaboratorID = taskID + "clb";
                                    console.log("Collaborator Member:", collaboratorManager);
                                    console.log("Collaborator ID:", collaboratorID);
                                    deleteManager(taskID, collaboratorManager);
                                };
                                managers[i].appendChild(deleteButton);
                            }
                        }
                    });
                }


                function openAlertModal(message) {
                    document.getElementById("alertText").textContent = message;
                    document.getElementById("myalertModal").classList.remove("hidden");
                }

                document.getElementById("closealertModal").addEventListener("click", function () {
                    document.getElementById("myalertModal").classList.add("hidden");
                });

                document.getElementById("closealertModalButton").addEventListener("click", function () {
                    document.getElementById("myalertModal").classList.add("hidden");
                });







                function deleteTask() {
                    var taskID = document.getElementById("editTaskID").value;
                    var totalSubtasks = parseInt(document.getElementById('subtasktotals').innerText.split(':')[1].trim());

                    // Check if total subtasks is greater than zero
                    if (totalSubtasks > 0) {
                        openAlertModal("Cannot delete task with remaining subtasks.");

                        return; // Exit the function without performing deletion
                    }

                    // If total subtasks is zero, proceed with deletion
                    $.post("task/taskdelete.php", {
                        taskID: taskID
                    }, function (data, status) {
                        console.log("Data: " + data + "\nStatus: " + status);
                        closeEditModal(); // Close the modal after deletion
                        location.reload(); // Reload the page after deletion
                    });
                }


                function logUpdatedInputs() {
                    var updatedTitle = document.getElementById("editTaskTitle").value;
                    var updatedDescription = document.getElementById("editTaskDescription").value;
                    var updatedDueDate = new Date(document.getElementById("editDueDate").value);
                    var updatedPriority = document.getElementById("editPriorityLevel").value;
                    var updatedTaskID = document.getElementById("editTaskID").value;


                    // Retrieve the updated background color value
                    var updatedBgColor = document.getElementById("editBgColor").value;



                    // Retrieve the updated text color value
                    var updatedTextColor = document.getElementById("editTextColor").value;

                    console.log("Updated Task ID:", updatedTaskID); // Log the TaskID
                    console.log("Updated Task Title:", updatedTitle);
                    console.log("Updated Task Description:", updatedDescription);
                    var year = updatedDueDate.getFullYear();
                    var month = ("0" + (updatedDueDate.getMonth() + 1)).slice(-2);
                    var day = ("0" + updatedDueDate.getDate()).slice(-2);
                    var hour = ("0" + updatedDueDate.getHours()).slice(-2);
                    var minute = ("0" + updatedDueDate.getMinutes()).slice(-2);
                    var second = ("0" + updatedDueDate.getSeconds()).slice(-2);
                    var updatedDueDateFormatted = year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
                    console.log("Updated Due Date:", updatedDueDateFormatted);
                    console.log("Updated Priority Level:", updatedPriority);




                    // Send the updated data to server using AJAX
                    $.post("task/taskedit.php", {
                        taskID: updatedTaskID,
                        title: updatedTitle,
                        description: updatedDescription,
                        dueDate: updatedDueDateFormatted,
                        priority: updatedPriority,
                        bgColor: updatedBgColor, // Include updated background color
                        textColor: updatedTextColor // Include updated text color
                    }, function (data, status) {
                        refreshTaskList();
                        console.log("Data: " + data + "\nStatus: " + status);
                    });


                    closeEditModal();
                }



                function deleteCollaborator(taskID, collaboratorMember) {
                    var collaboratorID = taskID + "clb"; // Construct the collaborator ID

                    // AJAX request to delete collaborator record
                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration/deletecollaborator.php",
                        data: { taskID: taskID, collaboratorID: collaboratorID, collaboratorMember: collaboratorMember },
                        success: function (response) {
                            fetchCollaboratorsAndUpdateUI(taskID);
                            console.log(response); // Log the response from the server
                        }
                    });
                }


                
                function deleteManager(taskID, collaboratorManager) {
                    var collaboratorID = taskID + "clb"; // Construct the collaborator ID

                    // AJAX request to delete collaborator record
                    $.ajax({
                        type: "POST",
                        url: "sharedcollaboration/deletemanager.php",
                        data: { taskID: taskID, collaboratorID: collaboratorID, collaboratorManager: collaboratorManager },
                        success: function (response) {
                            fetchManagersAndUpdateUI(taskID);
                            console.log(response); // Log the response from the server
                        }
                    });
                }


                function closeEditModal() {
                    document.getElementById("editmodal").style.display = "none";
                }

                function stopPropagation(event) {
                    event.stopPropagation();
                }




            </script>

            <style>

                @media (max-width: 1030px) {
                    #tgrid {
                        grid-template-columns: repeat(1, minmax(0, 1fr));
                    }
                }


                #editmodal {
                    z-index: 2000;
                    /* Set a higher z-index value */
                }

                #myalertModal {
                    z-index: 3000;
                    /* Set a higher z-index value than the edit modal */
                }


                    

            </style>


            <?php
    } else {
        // SQL error handling (optional)
        echo '<div class="container mx-auto mt-8 p-6 md:p-12 text-center text-white">You have no tasks here.</div>';
    }

        mysqli_free_result($result);
    } else {
        echo 'Venture ID not provided.';
    }
} 


else {
    echo '<script>window.location.href = "../login.html";</script>';

    echo "Please log in to view tasks.";
}
mysqli_close($link);
?>