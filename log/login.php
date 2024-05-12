<?php
// Include your database configuration file
require_once('../config.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($link, $_POST['username']);
    $password = mysqli_real_escape_string($link, $_POST['password']);

    // Fetch user from database based on username
    $query = "SELECT * FROM user WHERE UserUsername = '$username'";
    $result = mysqli_query($link, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        // Verify password
        if (password_verify($password, $row['UserPassword'])) {
            // Password is correct, start a new session
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['userID'] = $row['UserID'];
            // Redirect to index.html
            header("Location: ../index.html");
            exit();
        } else {
            // Incorrect password, redirect back to login page with a query parameter
            header("Location: ../login.html?error=password");
            exit();
        }
    } else {
        // Username not found, redirect back to login page with a query parameter
        header("Location: ../login.html?error=user");
        exit();
    }
} else {
    // Not a POST request, redirect to login form or show an error
    echo "Invalid request.";
}

// Close connection
mysqli_close($link);
?>
