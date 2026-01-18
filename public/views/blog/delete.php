<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_user = $_SESSION['user_name'] ?? null;

$stmt = $conn->prepare("SELECT author FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if ($post && ($post['author'] === $current_user || $_SESSION['user_role'] === 'admin')) {
    $delete = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $delete->bind_param("i", $id);
    $delete->execute();
}

// Redirect back to the public blog page
header("Location: ../../public/blog.php");
exit();