<?php
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "findbrick-real-estate";

// Create connection
$con = mysqli_connect($host, $db_user, $db_pass, $db_name);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
