<?php
require_once('../config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle image upload
    $targetDir = "../profilepics/";
    $fileName = basename($_FILES["editImage"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
    
    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if(in_array($fileType, $allowTypes)){
        // Upload file to server
        if(move_uploaded_file($_FILES["editImage"]["tmp_name"], $targetFilePath)){
            // Insert user data into database
            $userFirstName = mysqli_real_escape_string($link, $_POST['UserFirstName']);
            $userLastName = mysqli_real_escape_string($link, $_POST['UserLastName']);
            $userPhoneNumber = mysqli_real_escape_string($link, $_POST['UserPhoneNumber']);
            $userEmail = mysqli_real_escape_string($link, $_POST['UserEmail']);
            $userUsername = mysqli_real_escape_string($link, $_POST['UserUsername']);
            $userPassword = mysqli_real_escape_string($link, $_POST['Userpassword']);
            $userID = uniqid();
            $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO user (UserID, UserFirstName, UserLastName, UserPhoneNumber, UserEmail, UserUsername, UserPassword, UserProfileImage) VALUES ('$userID', '$userFirstName', '$userLastName', '$userPhoneNumber', '$userEmail', '$userUsername', '$hashedPassword', '$targetFilePath')";
            
            if (mysqli_query($link, $insertQuery)) {
                // User registered successfully, start session and redirect to index.html
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $userUsername;
                $_SESSION['userID'] = $userID;
                header("Location: index.html");
                exit();
            } else {
                echo "ERROR: Could not execute $insertQuery. " . mysqli_error($link);
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo 'Invalid file format.';
    }
} else {
    echo "Invalid request.";
}
mysqli_close($link);
?>
