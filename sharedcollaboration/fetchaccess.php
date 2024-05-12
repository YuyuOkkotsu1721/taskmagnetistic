<?php
require_once('../config.php');

if(isset($_POST['subtaskID'])) {
    $subtaskID = mysqli_real_escape_string($link, $_POST['subtaskID']);
    
    // Fetch all AssignedTo values for the given subtaskID
    $sql = "SELECT AssignedTo FROM subtaskaccess WHERE SubtaskID = '$subtaskID'";
    $result = mysqli_query($link, $sql);

    if(mysqli_num_rows($result) > 0) {
        $output = '<ul>';
        while($row = mysqli_fetch_assoc($result)) {
            $output .= '<li class="mb-2">' . $row['AssignedTo'] . '</li>';
        }
        $output .= '</ul>';
        echo $output; // Output the list of AssignedTo values
    } else {
        echo "No collaborators found for this subtask.";
    }
} else {
    echo "Invalid request.";
}

