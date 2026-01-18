<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php"; 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'] ?: 'Mohamed'; // Fallback to DB default

    $stmt = $conn->prepare("INSERT INTO posts (title, content, author) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $author);
    
    if ($stmt->execute()) {
        header("Location: list.php?status=created");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post | DEVBLOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body style="background: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif;">

    <?php require_once __DIR__ . "/../layouts/admin_sidebar.php"; ?>

    <main class="main-content">
        <div class="container-fluid px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold h4">Create New Article</h2>
                <a href="list.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 24px;">
                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Blog Title</label>
                        <input type="text" name="title" class="form-control form-control-lg border-0 bg-light" placeholder="Enter a catchy title..." required style="border-radius: 12px;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Author Name</label>
                        <input type="text" name="author" class="form-control border-0 bg-light" value="Mohamed" style="border-radius: 12px;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Content</label>
                        <textarea name="content" class="form-control border-0 bg-light" rows="10" placeholder="Write your content here..." required style="border-radius: 12px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">Publish Post</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>