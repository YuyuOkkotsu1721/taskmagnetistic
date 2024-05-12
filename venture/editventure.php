<?php
require_once('../config.php');

if(isset($_POST['ventureID'], $_POST['title'], $_POST['description'], $_POST['bgColor'], $_POST['textColor'])) {
    $ventureID = mysqli_real_escape_string($link, $_POST['ventureID']);
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $bgColor = mysqli_real_escape_string($link, $_POST['bgColor']);
    $textColor = mysqli_real_escape_string($link, $_POST['textColor']);

    $sql = "UPDATE venture SET VentureTitle = '$title', VentureDescription = '$description', VentureBackgroundColor = '$bgColor', VentureTextColor = '$textColor' WHERE VentureID = '$ventureID'";

    if(mysqli_query($link, $sql)) {
        echo "Venture updated successfully.";
    } else {
        echo "Error updating venture: " . mysqli_error($link);
    }
} else {
    echo "Incomplete data received.";
}

mysqli_close($link);
?>
