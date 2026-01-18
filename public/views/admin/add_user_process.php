<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php";

// 1. SECURITY: Admin Only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Basic Validation
    if (empty($name) || empty($email) || empty($password)) {
        header("Location: users.php?error=empty_fields");
        exit();
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        header("Location: users.php?error=email_taken");
        exit();
    }
    $check_stmt->close();

    // 2. Hash Password & Insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        header("Location: users.php?success=user_added");
    } else {
        header("Location: users.php?error=db_error");
    }
    
    $stmt->close();
    $conn->close();
    exit();
}