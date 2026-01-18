<?php 
// 1. Always start the session at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Define the base path for easier navigation links
$base = "/devblog/public"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevBlog | For Developers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<header class="glass-nav">
  <nav class="navbar navbar-expand-lg">
    <div class="container nav-max px-4">

      <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $base ?>/index.php">
        <span class="brand-icon">
          <i data-lucide="code"></i>
        </span>
        <div class="d-flex flex-column">
            <span class="logo">DevBlog</span>
            <span class="tagline d-none d-sm-inline">for Developers</span>
        </div>
      </a>

      <button class="navbar-toggler border-0 fs-3" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
         <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="mainNav">
        <ul class="navbar-nav align-items-lg-center gap-lg-3 mt-3 mt-lg-0">
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/blog.php">Blogs</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $base ?>/contact.php">Contact Us</a></li>

          <?php if(isset($_SESSION['user_id'])): ?>
            
            <li class="nav-item">
                <a class="nav-link fw-bold text-success d-flex align-items-center gap-1 me-lg-2" href="<?= $base ?>/create-post.php">
                  <i data-lucide="plus-square" style="width: 18px; height: 18px;"></i>
                  Create Blog
                </a>
            </li>

            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link fw-bold text-primary d-flex align-items-center gap-1" href="<?= $base ?>/views/admin/dashboard.php">
                  <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i>
                  Dashboard
                </a>
              </li>
            <?php endif; ?>

            <li class="nav-item">
              <span class="nav-link text-muted d-none d-lg-inline">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </li>
            <li class="nav-item">
              <a class="btn btn-outline-danger ms-lg-2 rounded-pill px-4" href="<?= $base ?>/logout.php">Logout</a>
            </li>
          
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?= $base ?>/login.php">Login</a></li>
            <li class="nav-item">
              <a class="btn btn-primary ms-lg-3 rounded-pill px-4 shadow-sm" href="<?= $base ?>/register.php">Register</a>
            </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
  </nav>
</header>

<script>
  lucide.createIcons();
</script>