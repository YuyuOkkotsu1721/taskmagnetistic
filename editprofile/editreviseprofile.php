<?php
require_once('../config.php');
session_start();

$response = []; // Initialize an empty array to store response data

if (isset($_SESSION["userID"])) {
    $userID = $_SESSION["userID"];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if file is uploaded and move it to profilepics directory
        if(isset($_FILES['editImage']) && $_FILES['editImage']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['editImage']['tmp_name'];
            $fileName = $_FILES['editImage']['name'];
            $fileSize = $_FILES['editImage']['size'];
            $fileType = $_FILES['editImage']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadPath = '../profilepics/' . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $uploadPath)) {
                // Update UserProfileImage column in the database
                $sql = "UPDATE user SET UserProfileImage = ? WHERE UserID = ?";
                if($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ss", $uploadPath, $userID);
                    if(mysqli_stmt_execute($stmt)) {
                        $response['success'] = true;
                    } else {
                        $response['error'] = "Error updating profile picture.";
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $response['error'] = "Error uploading image.";
            }
        }

        // Update user data except password
        $userFirstName = $_POST['userFirstName'];
        $userLastName = $_POST['userLastName'];
        $userPhoneNumber = $_POST['userPhoneNumber'];
        $userEmail = $_POST['userEmail'];
        $userUsername = $_POST['userUsername'];
        $usernamecurrent = $_POST['usernamecurrent']; // Get the usernamecurrent

        $sql = "UPDATE user SET UserFirstName=?, UserLastName=?, UserPhoneNumber=?, UserEmail=?, UserUsername=? WHERE UserID=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssss", $userFirstName, $userLastName, $userPhoneNumber, $userEmail, $userUsername, $userID);
            if (mysqli_stmt_execute($stmt)) {
                // Update username in collaborators table
                $sql_update_collaborators = "UPDATE collaborators SET CollaboratorMaker = ? WHERE CollaboratorMaker = ? OR CollaboratorMember = ?";
                if ($stmt_update_collaborators = mysqli_prepare($link, $sql_update_collaborators)) {
                    mysqli_stmt_bind_param($stmt_update_collaborators, "sss", $userUsername, $usernamecurrent, $usernamecurrent);
                    mysqli_stmt_execute($stmt_update_collaborators);
                    mysqli_stmt_close($stmt_update_collaborators);
                } else {
                    $response['error'] = "Error updating collaborators table.";
                }
                
                // Update username in subtaskaccess table
                $sql_update_subtaskaccess = "UPDATE subtaskaccess SET AssignedTo = ? WHERE AssignedTo = ?";
                if ($stmt_update_subtaskaccess = mysqli_prepare($link, $sql_update_subtaskaccess)) {
                    mysqli_stmt_bind_param($stmt_update_subtaskaccess, "ss", $userUsername, $usernamecurrent);
                    mysqli_stmt_execute($stmt_update_subtaskaccess);
                    mysqli_stmt_close($stmt_update_subtaskaccess);
                } else {
                    $response['error'] = "Error updating subtaskaccess table.";
                }

                $response['success'] = true;
            } else {
                $response['error'] = "Error updating user data.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $response['error'] = "Invalid request method.";
    }
} else {
    $response['error'] = "Please log in to view ventures.";
}

// Close connection
mysqli_close($link);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
