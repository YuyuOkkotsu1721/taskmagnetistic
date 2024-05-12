<?php
require_once('../config.php');

// Check if UserID is set in session
if (isset($_SESSION["userID"])) {
    // Get UserID from session
    $userID = $_SESSION["userID"];

    // Prepare SQL query to fetch username from users table
    $sql = "SELECT UserUsername FROM users WHERE UserID = '$userID'";
    $result = mysqli_query($link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the username
        $row = mysqli_fetch_assoc($result);
        $username = $row['UserUsername'];
        // Output the username
        echo $username;
    } else {
        echo "Unknown"; // Output if username not found
    }
} else {
    echo "Unknown"; // Output if UserID not set in session
}
?>
