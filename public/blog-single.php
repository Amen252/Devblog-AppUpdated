<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . "/app/config/database.php"; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_user_name = $_SESSION['user_name'] ?? null;
$current_user_role = $_SESSION['user_role'] ?? 'guest';

// --- 1. HANDLE DELETE LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    // Security check: Verify ownership before deleting
    $check_stmt = $conn->prepare("SELECT author FROM posts WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $auth_check = $check_stmt->get_result()->fetch_assoc();

    if ($auth_check && ($auth_check['author'] === $current_user_name || $current_user_role === 'admin')) {
        $delete_stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        if ($delete_stmt->execute()) {
            // After deletion, we MUST redirect to the archive because this post no longer exists
            header("Location: blog.php?status=deleted");
            exit();
        }
    }
}

// --- 2. HANDLE UPDATE LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];
    
    $check_stmt = $conn->prepare("SELECT author FROM posts WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $auth_check = $check_stmt->get_result()->fetch_assoc();

    if ($auth_check && ($auth_check['author'] === $current_user_name || $current_user_role === 'admin')) {
        $update_stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $new_title, $new_content, $id);
        $update_stmt->execute();
        header("Location: blog-single.php?id=$id&status=updated");
        exit();
    }
}

// --- 3. FETCH POST DATA ---
$query = "SELECT posts.*, users.role FROM posts 
          LEFT JOIN users ON posts.author = users.name 
          WHERE posts.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) { die("No post found."); }

$can_manage = ($current_user_role === 'admin' || $post['author'] === $current_user_name);
$is_editing = isset($_GET['edit']) && $can_manage;

include __DIR__ . "/views/layouts/header.php"; 
?>

<div id="reading-progress" class="fixed-top" style="width: 0%; height: 4px; background: #085add; z-index: 9999;"></div>

<main class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4">Post updated successfully!</div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="blog.php" class="text-decoration-none text-muted fw-bold small">
                    <i class="fas fa-arrow-left me-1"></i> BACK TO ARCHIVE
                </a>
                
                <?php if ($can_manage && !$is_editing): ?>
                <div class="d-flex gap-2">
                    <a href="blog-single.php?id=<?= $id ?>&edit=true" class="btn btn-sm btn-light border text-success px-3 shadow-sm">
                        <i class="fas fa-pencil-alt me-1"></i> Edit
                    </a>
                    
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this post permanently?');" class="m-0">
                        <button type="submit" name="delete_post" class="btn btn-sm btn-light border text-danger px-3 shadow-sm">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($is_editing): ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4 text-primary">Edit Your Log</h3>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Title</label>
                                <input type="text" name="title" class="form-control form-control-lg border-2" value="<?= htmlspecialchars($post['title']) ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Content</label>
                                <textarea name="content" class="form-control border-2" rows="12" required><?= htmlspecialchars($post['content']) ?></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="update_post" class="btn btn-primary px-4 fw-bold shadow-sm">Save Changes</button>
                                <a href="blog-single.php?id=<?= $id ?>" class="btn btn-light border px-4 shadow-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <article>
                    <header class="mb-4 pb-3 border-bottom">
                        <p class="text-primary fw-bold small text-uppercase mb-1">
                            Engineering Log • <?= date('M d, Y', strtotime($post['created_at'])) ?>
                        </p>
                        <h1 class="fw-bolder text-dark mb-0" style="letter-spacing: -1px; line-height: 1.2;">
                            <?= htmlspecialchars($post['title']) ?>
                        </h1>
                    </header>

                    <div class="blog-content text-secondary mb-5" style="font-size: 1.15rem; line-height: 1.6; text-align: justify;">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>

                    <footer class="bg-light border rounded-3 p-3 mb-5">
                        <div class="d-flex flex-column">
                            <h6 class="fw-bold mb-1 text-dark">
                                <span class="text-muted fw-normal small">Written by</span> <?= htmlspecialchars($post['author']) ?>
                            </h6>
                            <div>
                                <span class="badge bg-primary-subtle text-primary py-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($post['role'] ?? 'CONTRIBUTOR') ?>
                                </span>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php 
$conn->close();
include __DIR__ . "/views/layouts/footer.php"; 
?>