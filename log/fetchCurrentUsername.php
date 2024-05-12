<?php
require_once('../config.php');
session_start();

$response = array(); // Initialize an array to store response data

if (isset($_SESSION["userID"])) {
    // Query to fetch user information based on userID
    $userquery = "SELECT UserUsername, UserFirstName, UserLastName, UserProfileImage FROM user WHERE UserID = '{$_SESSION["userID"]}'";
    $userresult = mysqli_query($link, $userquery);
    $user = mysqli_fetch_assoc($userresult);

    // Check if user exists
    if ($user) {
        // Extracting user details
        $username = $user['UserUsername'];
        $firstName = $user['UserFirstName'];
        $lastName = $user['UserLastName'];
        $profileImage = $user['UserProfileImage'];

        // Adding data to the response array
        $response['username'] = $username;
        $response['firstName'] = $firstName;
        $response['lastName'] = $lastName;
        $response['profileImage'] = $profileImage;
    } else {
        // User not found
        $response['error'] = "User not found.";
    }
} else {
    // Session not set
    $response['error'] = "Session not set.";
}

// Sending the response as JSON
echo json_encode($response);
?>
