<?php
// Define the base admin path to avoid 404 errors
$admin_base = "/devblog/public/views/admin";
$blog_base  = "/devblog/public/views/blog";
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-icon"><i class="fas fa-feather-alt"></i></div>
        <div class="logo-text">DEV<span>BLOG</span></div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-label text-white-50">Main Menu</li>
            
            <li>
                <a href="<?= $admin_base ?>/dashboard.php">
                    <i class="fas fa-th-large"></i> <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="<?= $blog_base ?>/list.php">
                    <i class="fas fa-file-alt"></i> <span>All Posts</span>
                </a>
            </li>

            <li>
                <a href="<?= $admin_base ?>/users.php">
                    <i class="fas fa-users"></i> <span>Users</span>
                </a>
            </li>

            <li>
                <a href="<?= $admin_base ?>/messages.php">
                    <i class="fas fa-comment-dots"></i> <span>Messages</span>
                </a>
            </li>

            <li>
                <a href="<?= $admin_base ?>/report.php">
                    <i class="fas fa-chart-line"></i> <span>System Report</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="/devblog/public/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span>Sign Out</span>
        </a>
    </div>
</aside>