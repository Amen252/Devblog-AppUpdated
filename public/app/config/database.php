<?php
// Path: public/app/config/database.php

$host = "localhost";
$username = "root";
$password = "";
$db_name = "devblog_db"; 

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to match modern database standards
$conn->set_charset("utf8mb4");
?>