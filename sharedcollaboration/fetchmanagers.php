<?php
require_once('../config.php');

if(isset($_POST['taskID'])) {
    $taskID = mysqli_real_escape_string($link, $_POST['taskID']);
    $collaboratorID = $taskID . "clb";

    // Fetch collaborators for the given collaboratorID
    $sql = "SELECT * FROM collaborators WHERE CollaboratorID = '$collaboratorID' AND CollaboratorManager != ''";
    $result = mysqli_query($link, $sql);

    if(mysqli_num_rows($result) > 0) {
        // Display collaborators
        $output = '<ul>';
        while($row = mysqli_fetch_assoc($result)) {
            $output .= '<li class="mb-2">' . $row['CollaboratorManager'] . '</li>';
        }
        $output .= '</ul>';
        echo $output;
    } else {
        echo "No collaborators found.";
    }
} else {
    echo "Task ID not provided.";
}

mysqli_close($link);
