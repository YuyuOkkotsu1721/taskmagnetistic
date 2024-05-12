<?php
// logout.php

// Start session
session_start();

// Destroy session
session_destroy();

// Redirect to login page
header('Location: ../login.html');
exit();
