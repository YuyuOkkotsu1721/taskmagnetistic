<?php
require_once('../config.php');
session_start();

if (isset($_SESSION["userID"]) && isset($_POST["ventureID"])) {
    $userID = $_SESSION["userID"];
    $ventureID = $_POST["ventureID"];

    // Prepare a delete statement
    $query = "DELETE FROM venture WHERE UserID = ? AND VentureID = ?";
    if ($stmt = mysqli_prepare($link, $query)) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ss", $userID, $ventureID);

        // Attempt to execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "Venture deleted successfully.";
        } else {
            echo "ERROR: Could not execute query.";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not prepare query.";
    }
} else {
    echo "ERROR: Missing parameters.";
}

mysqli_close($link);

