<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php"; 

// 1. SECURITY: Admin Only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); 
    exit();
}

// 2. PAGINATION LOGIC
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch total count
$total_res = $conn->query("SELECT COUNT(id) AS id FROM posts");
$total_posts = $total_res->fetch_assoc()['id'];
$pages = ceil($total_posts / $limit);

// 3. FETCH POSTS
$query = "SELECT id, title, author, created_at FROM posts ORDER BY created_at DESC LIMIT $start, $limit";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs | DEVBLOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7fe; }
        .glass-card { background: white; border-radius: 24px; border: 1px solid rgba(0, 0, 0, 0.05); padding: 25px; }
        .author-avatar { width: 32px; height: 32px; border-radius: 10px; background: #eef2ff; color: #4e73df; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }
        .action-btn { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; text-decoration: none; }
        .btn-view { background: #eef2ff; color: #4e73df; }
        .btn-edit { background: #ecfdf5; color: #059669; }
        .btn-delete { background: #fff1f2; color: #e11d48; }
        
        /* Arrow Pagination Styling */
        .pagination .page-link { 
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #4e73df;
            padding: 10px 18px;
            border-radius: 12px !important;
            transition: all 0.3s;
        }
        .pagination .page-item.disabled .page-link { background: #f8fafc; border-color: #f1f5f9; color: #cbd5e1; }
        .pagination .page-link:hover:not(.disabled) { background: #4e73df; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

    <?php require_once __DIR__ . "/../layouts/admin_sidebar.php"; ?>

    <main class="main-content">
        <header class="top-header d-flex justify-content-between align-items-center bg-white px-4 py-3 mb-4 shadow-sm">
            <div>
                <h2 class="h4 mb-0 fw-bold">All Blog Posts</h2>
                <small class="text-muted">Page <?= $page ?> of <?= $pages ?></small>
            </div>
            <a href="create.php" class="btn btn-primary rounded-pill px-4">New Post</a>
        </header>

        <div class="container-fluid px-4">
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>TITLE</th>
                                <th>AUTHOR</th>
                                <th>DATE</th>
                                <th class="text-end">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result->num_rows > 0): ?>
                                <?php while($post = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="fw-bold text-dark"><?= htmlspecialchars($post['title']) ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="author-avatar"><?= strtoupper(substr($post['author'], 0, 1)) ?></div>
                                            <span class="small fw-medium"><?= htmlspecialchars($post['author']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($post['created_at'])) ?></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="../../blog-single.php?id=<?= $post['id'] ?>" class="action-btn btn-view"><i class="fas fa-eye"></i></a>
                                            <a href="edit.php?id=<?= $post['id'] ?>" class="action-btn btn-edit"><i class="fas fa-pen"></i></a>
                                            <a href="delete.php?id=<?= $post['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-5">No posts found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center gap-3">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </a>
                        </li>

                        <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>