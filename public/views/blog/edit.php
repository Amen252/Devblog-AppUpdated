<?php
session_start();
// Go up two levels to reach the root, then into app/config
require_once __DIR__ . "/../../app/config/database.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_user = $_SESSION['user_name'] ?? null;

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post || ($post['author'] !== $current_user && $_SESSION['user_role'] !== 'admin')) {
    die("Unauthorized.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $update = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $update->bind_param("ssi", $title, $content, $id);
    
    if ($update->execute()) {
        // Redirect back to the blog list in the public folder
        header("Location: ../../public/blog.php");
        exit();
    }
}

// Go up one level to reach views, then layouts
include __DIR__ . "/../layouts/header.php";
?>

<div class="container py-5 mt-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-4">Edit Log</h2>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Content</label>
                    <textarea name="content" class="form-control" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="../../public/blog.php" class="btn btn-light border">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../layouts/footer.php"; ?>