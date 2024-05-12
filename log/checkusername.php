<?php
require_once('../config.php');

// Check if username is provided
if(isset($_POST['username'])) {
    // Sanitize username
    $username = mysqli_real_escape_string($link, $_POST['username']);

    // Check if the username exists in the users table
    $sql = "SELECT COUNT(*) AS count FROM user WHERE UserUsername = '$username'";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    if($row['count'] > 0) {
        echo "exists";
    } else {
        echo "notexists";
    }
} else {
    echo "Username not provided.";
}

// Close connection
mysqli_close($link);
