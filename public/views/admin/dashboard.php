<?php
// 1. DATABASE & SESSION SETUP
session_start();

// Using absolute path for database config based on your folder structure
require_once __DIR__ . "/../../app/config/database.php"; 

// 2. SECURITY CHECK
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); 
    exit();
}

// 3. FETCH DATA FOR CARDS
$user_res = $conn->query("SELECT COUNT(id) as total FROM users");
$user_count = ($user_res) ? $user_res->fetch_assoc()['total'] : 0;

$post_res = $conn->query("SELECT COUNT(id) as total FROM posts");
$post_count = ($post_res) ? $post_res->fetch_assoc()['total'] : 0;

$msg_res = $conn->query("SELECT COUNT(id) as total FROM messages");
$msg_count = ($msg_res) ? $msg_res->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEVBLOG | Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

    <?php include __DIR__ . "/../layouts/admin_sidebar.php"; ?>

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <a href="../../index.php" class="back-home-link">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Site</span>
                </a>
            </div>

            <div class="user-profile">
                <div class="profile-info">
                    <p class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></p>
                    <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <h1 class="page-title">Dashboard Overview</h1>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="icon-box blue"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="stat-body">
                        <h3>Total Registered</h3>
                        <p><?= number_format($user_count) ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="icon-box purple"><i class="fas fa-pencil-alt"></i></div>
                    </div>
                    <div class="stat-body">
                        <h3>Articles Published</h3>
                        <p><?= number_format($post_count) ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="icon-box green"><i class="fas fa-comments"></i></div>
                    </div>
                    <div class="stat-body">
                        <h3>Pending Messages</h3>
                        <p><?= number_format($msg_count) ?></p>
                    </div>
                </div>
            </section>
        </div>
    </main>

</body>
</html>