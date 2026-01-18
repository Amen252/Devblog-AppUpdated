<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php"; 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); exit();
}

$id = $_GET['id'];
$post = $conn->query("SELECT * FROM posts WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, author = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $content, $author, $id);
    
    if ($stmt->execute()) {
        header("Location: list.php?status=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post | DEVBLOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body style="background: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif;">

    <?php require_once __DIR__ . "/../layouts/admin_sidebar.php"; ?>

    <main class="main-content">
        <div class="container-fluid px-4 py-4">
            <h2 class="fw-bold h4 mb-4">Edit Post</h2>
            <div class="card border-0 shadow-sm p-4" style="border-radius: 24px;">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control bg-light border-0" value="<?= htmlspecialchars($post['title']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Author</label>
                        <input type="text" name="author" class="form-control bg-light border-0" value="<?= htmlspecialchars($post['author']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Content</label>
                        <textarea name="content" class="form-control bg-light border-0" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 rounded-pill">Update Changes</button>
                        <a href="list.php" class="btn btn-light px-4 rounded-pill">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>