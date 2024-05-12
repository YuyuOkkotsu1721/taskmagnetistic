<?php
require_once('../config.php');
session_start();

// Check if the request is POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate venture title
    if(empty(trim($_POST["VentureTitle"]))){
        echo "Venture title cannot be blank.";
    } else{

        date_default_timezone_set('Asia/Manila');

        // Prepare an insert statement
        $sql = "INSERT INTO venture (VentureTitle, VentureDescription, VentureBackgroundColor, VentureTextColor, UserID, VentureID, VentureCreationDate) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssss", $param_VentureTitle, $param_VentureDescription, $param_VentureBackgroundColor, $param_VentureTextColor, $param_UserID, $param_VentureID, $param_VentureCreationDate);

            // Set parameters
            $param_VentureTitle = trim($_POST["VentureTitle"]);
            $param_VentureDescription = trim($_POST["VentureDescription"]);
            $param_VentureBackgroundColor = $_POST["bgColor"]; // Assuming you're passing this from the form
            $param_VentureTextColor = $_POST["textColor"]; // Assuming you're passing this from the form
            $param_UserID = $_SESSION['userID']; // Fetch user UUID from session

            // Generate VentureID
            $nextVentureID = getNextVentureID($link, $param_UserID); // Pass UserID to getNextVentureID function
            $param_VentureID = $param_UserID . 'v' . $nextVentureID; // Concatenate UUID with 'v' and auto-incremented number

            // Set VentureCreationDate to current date and time
            $param_VentureCreationDate = date("Y-m-d H:i:s");

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
} else {
    echo "No POST request received.";
}

// Function to get the next available VentureID for a particular user
function getNextVentureID($link, $userID) {
    $query = "SELECT MAX(CAST(SUBSTRING(VentureID, LOCATE('v', VentureID) + 1) AS UNSIGNED)) AS max_id FROM venture WHERE UserID = ?";
    if($stmt = mysqli_prepare($link, $query)){
        mysqli_stmt_bind_param($stmt, "s", $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $max_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        if ($max_id) {
            return $max_id + 1;
        } else {
            return 1;
        }
    } else {
        return false;
    }
}

?>
