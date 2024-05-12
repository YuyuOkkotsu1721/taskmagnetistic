<?php
require_once('../config.php');

if(isset($_POST['taskID'], $_POST['title'], $_POST['description'], $_POST['dueDate'], $_POST['priority'], $_POST['bgColor'], $_POST['textColor'])) {
    $taskID = mysqli_real_escape_string($link, $_POST['taskID']);
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $dueDate = mysqli_real_escape_string($link, $_POST['dueDate']);
    $priority = mysqli_real_escape_string($link, $_POST['priority']);
    $bgColor = mysqli_real_escape_string($link, $_POST['bgColor']);
    $textColor = mysqli_real_escape_string($link, $_POST['textColor']); // Retrieve the updated text color

    $sql = "UPDATE tasks SET TaskTitle = '$title', TaskDescription = '$description', TaskDueDateTime = '$dueDate', TaskPriority = '$priority', TaskBackgroundColor = '$bgColor', TaskTextColor = '$textColor' WHERE TaskID = '$taskID'";

    if(mysqli_query($link, $sql)) {
        echo "Task updated successfully.";
    } else {
        echo "Error updating task: " . mysqli_error($link);
    }
} else {
    echo "Incomplete data received.";
}

mysqli_close($link);
