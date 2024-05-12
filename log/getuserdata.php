<?php
session_start();
if(isset($_SESSION['user_id'])) {
    // Fetch and return UUID from the session
    echo $_SESSION['user_id'];
} else {
    echo "User UUID not found";
}
?>
