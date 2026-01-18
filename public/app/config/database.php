<?php
// Path: public/app/config/database.php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "devblog_db"; // Ensure you create this DB in phpMyAdmin

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>