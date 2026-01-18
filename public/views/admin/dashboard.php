<?php
// 1. DATABASE & SESSION SETUP
session_start();

// Using __DIR__ prevents the blank page by using absolute paths
require_once __DIR__ . "/../../app/config/database.php"; 

// 2. SECURITY CHECK
// If not admin, redirect to the login page in the root
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); 
    exit();
}

// 3. FETCH DATA FOR CARDS
// Count total users
$user_res = $conn->query("SELECT COUNT(id) as total FROM users");
$user_count = ($user_res) ? $user_res->fetch_assoc()['total'] : 0;

// Count total posts
$post_res = $conn->query("SELECT COUNT(id) as total FROM posts");
$post_count = ($post_res) ? $post_res->fetch_assoc()['total'] : 0;

// Count total messages
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

    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon"><i class="fas fa-feather-alt"></i></div>
            <div class="logo-text">DEV<span>BLOG</span></div>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-label">Main Menu</li>
                
                <li>
                    <a href="dashboard.php" class="active">
                        <i class="fas fa-th-large"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                <li>
                    <a href="../blog/list.php">
                        <i class="fas fa-file-alt"></i> <span>All Posts</span>
                    </a>
                </li>
                
                <li>
                    <a href="users.php">
                        <i class="fas fa-users"></i> <span>Users</span>
                    </a>
                </li>
                
                <li>
                    <a href="messages.php">
                        <i class="fas fa-comment-dots"></i> <span>Messages</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="../../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Sign Out</span>
            </a>
        </div>
    </aside>

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
                    <p class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?></div>
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