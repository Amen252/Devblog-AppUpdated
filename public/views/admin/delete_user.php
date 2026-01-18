<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php";

// 1. SECURITY: Admin Only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $target_id = $_GET['id'];
    $admin_id = $_SESSION['user_id'];

    // 2. Prevent self-deletion
    if ($target_id == $admin_id) {
        header("Location: users.php?error=self_delete");
        exit();
    }

    // 3. Delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $target_id);

    if ($stmt->execute()) {
        header("Location: users.php?success=deleted");
    } else {
        header("Location: users.php?error=db_error");
    }
    $stmt->close();
}
$conn->close();
exit();