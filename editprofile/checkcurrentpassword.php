<?php
require_once('../config.php');
session_start();

$response = []; // Initialize an empty array to store response data

if (isset($_SESSION["userID"])) {
    // Retrieve userID from session
    $userID = $_SESSION["userID"];

    // Retrieve the current password entered by the user
    $currentPassword = $_POST['currentPassword'];

    // Prepare and execute a SELECT query to retrieve the hashed password from the user table
    $sql = "SELECT UserPassword FROM user WHERE UserID = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_userID);
        $param_userID = $userID;

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            // Check if a record exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $hashedPassword);

                // Fetch the result
                if (mysqli_stmt_fetch($stmt)) {
                    // Verify if the current password matches the hashed password
                    if (password_verify($currentPassword, $hashedPassword)) {
                        $response['match'] = true;
                    } else {
                        $response['match'] = false;
                    }
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
    $response['error'] = "Please log in to check the password.";
}

// Close connection
mysqli_close($link);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
