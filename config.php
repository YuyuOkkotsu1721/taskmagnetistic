<?php
define('DB_SERVER', 'qf5dic2wzyjf1x5x.cbetxkdyhwsb.us-east-1.rds.amazonaws.com');
define('DB_USERNAME', 'dbybgpvlrr7i6r9d');
define('DB_PASSWORD', 'dayfqto1alj7iy5h');
define('DB_NAME', 'ik0l60t2ju4idgvw');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
