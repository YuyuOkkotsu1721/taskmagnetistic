<?php
define('DB_SERVER', 'y5s2h87f6ur56vae.cbetxkdyhwsb.us-east-1.rds.amazonaws.com');
define('DB_USERNAME', 'gkbyqw54eu40m34j');
define('DB_PASSWORD', 'rkcnzklzzniuu23m');
define('DB_NAME', 'wfsbuhww1badwteb');
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
