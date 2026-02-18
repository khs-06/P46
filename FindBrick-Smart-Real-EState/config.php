<?php
// Database configuration
$host = "localhost";           // Change if your DB is hosted elsewhere
$db_user = "root";             // Your DB username
$db_pass = "";                 // Your DB password
$db_name = "findbrick-real-estate";       // Your database name

// Create connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8mb4 for better Unicode support
$conn->set_charset("utf8mb4");
?>