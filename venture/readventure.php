<?php
require_once('../config.php');
session_start();

if (isset($_SESSION["userID"])) {
    $userID = $_SESSION["userID"];
    $query = "SELECT v.*, 
    (SELECT COUNT(*) FROM tasks t WHERE v.VentureID = t.VentureID) AS TotalTasks,
    (SELECT COUNT(*) FROM tasks t 
     WHERE v.VentureID = t.VentureID AND (
         SELECT COUNT(*) FROM subtasks s WHERE s.TaskID = t.TaskID AND s.SubtaskStatus = 'Done'
     ) = (
         SELECT COUNT(*) FROM subtasks s WHERE s.TaskID = t.TaskID
     ) AND (
         SELECT COUNT(*) FROM subtasks s WHERE s.TaskID = t.TaskID AND s.SubtaskStatus = 'Done'
     ) > 0
    ) AS CompletedTasks,
    (SELECT COUNT(*) FROM tasks t
     WHERE v.VentureID = t.VentureID AND (
         SELECT COUNT(*) FROM subtasks s WHERE s.TaskID = t.TaskID AND s.SubtaskStatus = 'Done'
     ) = 0
    ) AS IncompleteTasks,
    (SELECT COUNT(DISTINCT t.TaskID) FROM tasks t
     INNER JOIN subtasks s ON t.TaskID = s.TaskID
     WHERE t.VentureID = v.VentureID AND s.SubtaskStatus = 'Done' AND
     (SELECT COUNT(*) FROM subtasks WHERE TaskID = t.TaskID AND SubtaskStatus = 'Done') > 0 AND
     (SELECT COUNT(*) FROM subtasks WHERE TaskID = t.TaskID AND SubtaskStatus = 'Done') < (SELECT COUNT(*) FROM subtasks WHERE TaskID = t.TaskID)
    ) AS ActiveTasks
FROM venture v 
WHERE v.UserID = ?";

    if ($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $userID);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $ventureBackgroundColor = $row['VentureBackgroundColor'];
                    $ventureTextColor = $row['VentureTextColor'];
                    $ventureDescription = $row['VentureDescription'];
                    $ventureTitle = $row['VentureTitle'];
                    $totalTasks = $row['TotalTasks'];
                    $completedTasks = $row['CompletedTasks'];
                    $incompleteTasks = $row['IncompleteTasks'];
                    $activeTasks = $row['ActiveTasks'];

                    ?>
                    <div class='mx-auto p-8 rounded-tl-3xl rounded-br-3xl relative ventureshape mb-6'
                        style='background-color: <?php echo $ventureBackgroundColor; ?>; color: <?php echo $ventureTextColor; ?>; '>
                        <input type='hidden' class='ventureID' value='<?php echo $row['VentureID']; ?>'>
                        <div class="mb-2 md:mb-2">
                            <!-- Apply text color style directly to the input fields -->
                            <input type="text" class="text-2xl font-bold bg-transparent text-white outline-none border-none w-full mb-4"
                                id="TaskTitleInput" name="TaskTitleInput" style="color: <?php echo $ventureTextColor; ?>;"
                                value='<?php echo $ventureTitle; ?>'>
                            <input type="text" id="TaskDescriptionInput" name="TaskDescriptionInput"
                                class="text-lg bg-transparent md:text-xl text-white outline-none border-none resize-none w-full"
                                style="color: <?php echo $ventureTextColor; ?>;" value="<?php echo $ventureDescription; ?>">
                        </div>

                        <div class="absolute top-0 right-0 mt-2 md:mt-4 mr-2 md:mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 md:h-8 w-6 md:w-8 text-white cursor-pointer"
                                viewBox="0 0 20 20" fill="<?php echo $ventureTextColor; ?>"
                                onclick="openVentureModal('<?php echo $row['VentureID']; ?>', '<?php echo $ventureTitle; ?>', '<?php echo $ventureDescription; ?>', '<?php echo $ventureBackgroundColor; ?>', '<?php echo $ventureTextColor; ?>', '<?php echo $row['TotalTasks']; ?>')">
                                <path fill-rule="evenodd"
                                    d="M3 10a2 2 0 114 0 2 2 0 01-4 0zM9 10a2 2 0 114 0 2 2 0 01-4 0zm6 0a2 2 0 114 0 2 2 0 01-4 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <!-- Add the following code for the rectangular divs -->
                        <div class="absolute top-0 right-20 flex space-x-4">
                            <div class="relative w-24 h-8 bg-blue-600 rounded-bl-lg rounded-br-lg">
                                <div id="alltaskcount"
                                    class="absolute inset-0 flex justify-center items-center text-white text-sm font-bold">
                                    All: <?php echo $totalTasks; ?> <!-- Use the count from the query result -->
                                </div>
                            </div>
                            <div class="relative w-24 h-8 bg-yellow-500 rounded-bl-lg rounded-br-lg">
                                <div id="inactivetaskcount"
                                    class="absolute inset-0 flex justify-center items-center text-white text-sm font-bold">
                                    Inactive: <?php echo $incompleteTasks; ?>
                                </div>
                            </div>
                            <div class="relative w-24 h-8 bg-red-500 rounded-bl-lg rounded-br-lg">
                                <div id="activetaskcount"
                                    class="absolute inset-0 flex justify-center items-center text-white text-sm font-bold">
                                    Active: <?php echo $activeTasks; ?>
                                </div>
                            </div>
                            <div class="relative w-24 h-8 bg-green-500 rounded-bl-lg rounded-br-lg">
                                <div id="completedtaskcount"
                                    class="absolute inset-0 flex justify-center items-center text-white text-sm font-bold">
                                    Completed: <?php echo $completedTasks; ?> <!-- Use the count from the query result -->
                                </div>
                            </div>
                        </div>



                        <button class="absolute bottom-0 right-0 mb-4 mr-4 font-bold py-2 px-4 rounded"
                            style="background-color: <?php echo $ventureTextColor; ?>; color: <?php echo $ventureBackgroundColor; ?>;"
                            onclick='redirectToTask("<?php echo $ventureTitle; ?>", "<?php echo $row['VentureID']; ?>")'>See Tasks</button>
                    </div>



                    <!-- VentureModal -->
                    <div class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden text-white" id="VentureModal"
                        onclick="stopPropagation(event)">
                        <div         class="absolute top-1/2 left-1/2 w-11/12 transform -translate-x-1/2 -translate-y-1/2 bg-gray-900 p-8 rounded-lg max-h-screen overflow-y-auto"

                            onclick="stopPropagation(event)">
                            <h2 class="text-2xl font-bold mb-4">Edit Venture Settings</h2>
                            <div class="mb-4" onclick="stopPropagation(event)">
                                <label class="block mb-2">Edit Venture Title</label>
                                <input type="text" id="editVentureTitle"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                            </div>
                            <div class="mb-4" onclick="stopPropagation(event)">
                                <label class="block mb-2">Edit Venture Description</label>
                                <textarea id="editVentureDescription"
                                    class="bg-gray-800 text-white px-4 py-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                            </div>
                            <label id="totalTasks" class="block mb-2 hidden">Total Tasks Remaining:</label>


                            <div class="mb-4" onclick="stopPropagation(event)">
                                <label class="block mb-2">Edit Venture Background Color</label>
                                <input type="color" id="editbgColor" value="#0000FF"
                                    class="bg-gray-800 text-white w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                            </div>


                            <div class="mb-4" onclick="stopPropagation(event)">
                                <label class="block mb-2">Edit Venture Text Color</label>
                                <input type="color" id="edittextColor" value="#FFFFFF"
                                    class="bg-gray-800 text-white w-full rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
                            </div>

                            <input type="hidden" id="editVentureID" />
                            <div class="flex justify-between">
                                <button id="venturedeleteButton" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                                    onclick="deleteVenture()">Delete</button>

                                <div class="flex justify-end" onclick="stopPropagation(event)">
                                    <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 mr-2" id="closeVentureModal"
                                        onclick="closeVentureModal()">Cancel</button>
                                    <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600" id="saveChanges"
                                        onclick="logUpdatedVentureInputs()">Save Changes</button>

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
                            <p id="alertText" class="p-3 text-black"></p>
                            <div class="border-t border-gray-300 mt-1 w-full"></div>
                            <div class="flex justify-end p-3">
                                <button id="closealertModalButton"
                                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>


                    <script>

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



                        function openVentureModal(ventureID, ventureTitle, ventureDescription, ventureBgColor, ventureTextColor, totalTasks) {
                            console.log("Venture ID:", ventureID);
                            console.log("Venture Title:", ventureTitle);
                            console.log("Venture Description:", ventureDescription);
                            console.log("Venture Background Color:", ventureBgColor);
                            console.log("Venture Text Color:", ventureTextColor);
                            console.log("Total Tasks:", totalTasks);

                            // Set input values based on venture data
                            document.getElementById('editVentureTitle').value = ventureTitle;
                            document.getElementById('editVentureDescription').value = ventureDescription;
                            document.getElementById('editbgColor').value = ventureBgColor;
                            document.getElementById('edittextColor').value = ventureTextColor;
                            document.getElementById('editVentureID').value = ventureID;
                            // Set total tasks in the modal
                            document.getElementById('totalTasks').innerText = "Total Tasks: " + totalTasks;


                            var venturedeleteButton = document.getElementById('venturedeleteButton');

                            // Check if total subtasks is zero
                            if (totalTasks > 0) {
                                // Set background color to yellow
                                venturedeleteButton.style.backgroundColor = 'orange';
                                // Disable the button
                                // Display alert message

                            } else {
                                // Reset background color to default
                                venturedeleteButton.style.backgroundColor = '';
                                // Enable the button
                            }



                            document.getElementById("VentureModal").classList.remove('hidden');
                        }



                        function logUpdatedVentureInputs() {
                            var ventureID = document.getElementById('editVentureID').value; // Change .editVentureID to #editVentureID
                            var updatedTitle = document.getElementById("editVentureTitle").value;
                            var updatedDescription = document.getElementById("editVentureDescription").value;
                            var updatedBgColor = document.getElementById("editbgColor").value;
                            var updatedTextColor = document.getElementById("edittextColor").value;

                            console.log("Updated Venture ID:", ventureID);
                            console.log("Updated Venture Title:", updatedTitle);
                            console.log("Updated Venture Description:", updatedDescription);
                            console.log("Updated Venture Background Color:", updatedBgColor);
                            console.log("Updated Venture Text Color:", updatedTextColor);

                            // Send the updated data to server using AJAX
                            $.post("venture/editventure.php", {
                                ventureID: ventureID,
                                title: updatedTitle,
                                description: updatedDescription,
                                bgColor: updatedBgColor,
                                textColor: updatedTextColor
                            }, function (data, status) {
                                loadVentures();
                                console.log("Data: " + data + "\nStatus: " + status);
                                // Optionally, you can perform actions based on the response here, like refreshing the page or displaying a message.
                            });

                            // Close the venture modal
                            closeVentureModal();
                        }


                        function closeVentureModal() {
                            document.getElementById('VentureModal').classList.add('hidden');
                        };

                        function deleteVenture() {
                            var ventureID = document.getElementById('editVentureID').value;
                            var totalTasks = parseInt(document.getElementById('totalTasks').innerText.split(':')[1].trim());

                            // Check if total subtasks is greater than zero
                            if (totalTasks > 0) {
                                openAlertModal("Cannot delete venture with remaining tasks.");

                                return; // Exit the function without performing deletion
                            }

                            // If total subtasks is zero, proceed with deletion
                            // Send an AJAX request to deleteventure.php
                            $.post("venture/deleteventure.php", { ventureID: ventureID }, function (data, status) {
                                // Handle the response
                                console.log("Data: " + data + "\nStatus: " + status);

                                // Reload the ventures after deletion
                                loadVentures();
                            });

                            // Close the venture modal
                            closeVentureModal();
                        }



                        function stopPropagation(event) {
                            event.stopPropagation();
                        }


                    </script>
                    <style>
                        #VentureModal {
                            z-index: 2000;
                            /* Set a higher z-index value */
                        }

                        #myalertModal {
                            z-index: 3000;
                            /* Set a higher z-index value than the edit modal */
                        }

                        .inverted-trapezoid {
                            /* Changed from border-bottom to border-top */
                            border-left: 15px solid transparent;
                            border-right: 15px solid transparent;
                            height: 0;
                            width: 80px;
                            border-bottom-left-radius: 20px;
                            border-bottom-right-radius: 20px;
                            border-top-left-radius: 1px;
                            border-top-right-radius: 1px;
                        }
                    </style>
                    <?php
                }
                } else {
                echo '<div class="container mx-auto mt-8 p-6 md:p-12 text-center text-white">You have no ventures here.</div>';
            }
        } else {
            echo "ERROR: Could not execute query.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not prepare query.";
    }
} else {
    echo '<script>window.location.href = "login.html";</script>';

    echo "Please log in to view ventures.";

}

mysqli_close($link);
?>