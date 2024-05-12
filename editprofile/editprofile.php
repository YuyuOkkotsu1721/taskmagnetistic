<?php
require_once('../config.php');
session_start();

$response = []; // Initialize an empty array to store response data

if (isset($_SESSION["userID"])) {
    $userID = $_SESSION["userID"];
    
    // Prepare and execute a SELECT query to retrieve user data
    $sql = "SELECT UserFirstName, UserLastName, UserPhoneNumber, UserEmail, UserUsername, UserProfileImage FROM user WHERE UserID = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_userID);
        $param_userID = $userID;
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            // Check if a record exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $userFirstName, $userLastName, $userPhoneNumber, $userEmail, $userUsername, $userProfileImage);
                
                // Fetch the result
                if (mysqli_stmt_fetch($stmt)) {
                    // Add user data to the response array
                    $response['userFirstName'] = $userFirstName;
                    $response['userLastName'] = $userLastName;
                    $response['userPhoneNumber'] = $userPhoneNumber;
                    $response['userEmail'] = $userEmail;
                    $response['userUsername'] = $userUsername;
                    $response['userProfileImage'] = $userProfileImage; // Add user profile image path to response
                }
            } else {
                $response['error'] = "User not found.";
            }
        } else {
            $response['error'] = "Oops! Something went wrong. Please try again later.";
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
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
