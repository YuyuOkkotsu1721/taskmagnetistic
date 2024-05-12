<?php
require_once('../config.php');

if(isset($_GET['id']) && isset($_GET['status'])) {
    $ventureID = mysqli_real_escape_string($link, $_GET['id']);
    $status = mysqli_real_escape_string($link, $_GET['status']);

    $sql = "";
    switch($status) {
        case 'All':
            $sql = "SELECT COUNT(*) FROM tasks WHERE VentureID = '$ventureID'";
            break;
        case 'Inactive':
            $sql = "SELECT COUNT(*) FROM tasks WHERE VentureID = '$ventureID' AND CompletedSubtasks = 0";
            break;
        case 'Active':
            $sql = "SELECT COUNT(*) FROM tasks WHERE VentureID = '$ventureID' AND CompletedSubtasks > 0 AND CompletedSubtasks < TotalSubtasks";
            break;
        case 'Completed':
            $sql = "SELECT COUNT(*) FROM tasks WHERE VentureID = '$ventureID' AND CompletedSubtasks = TotalSubtasks AND TotalSubtasks > 0";
            break;
        default:
            echo "Invalid status";
            exit;
    }

    $result = mysqli_query($link, $sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo $row['COUNT(*)']; // Return the count of tasks
    } else {
        echo "0"; // Return 0 if no tasks match the criteria
    }
} else {
    echo "Invalid request";
}
?>
