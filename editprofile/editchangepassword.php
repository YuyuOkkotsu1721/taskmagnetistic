<?php
require_once('../config.php');
session_start();

$response = []; // Initialize an empty array to store response data

if (isset($_SESSION["userID"])) {
    $userID = $_SESSION["userID"];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if new password is provided
        if (isset($_POST['newPassword'])) {
            // Retrieve new password from POST data
            $newPassword = $_POST['newPassword'];

            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update UserPassword column in the database
            $sql = "UPDATE user SET UserPassword = ? WHERE UserID = ?";
            if($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $userID);
                if(mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                } else {
                    $response['error'] = "Error updating password.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $response['error'] = "New password not provided.";
        }
    } else {
        $response['error'] = "Invalid request method.";
    }
} else {
    $response['error'] = "Please log in to change password.";
}

// Close connection
mysqli_close($link);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
