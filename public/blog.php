<?php 
session_start(); 
require_once __DIR__ . "/app/config/database.php"; 
require_once __DIR__ . "/views/layouts/header.php"; 

// 1. USER IDENTITY
$current_user_name = $_SESSION['user_name'] ?? null;
$current_user_role = $_SESSION['user_role'] ?? 'guest';

// 2. PAGINATION LOGIC
$limit = 6; 
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_results = $conn->query("SELECT COUNT(*) as total FROM posts")->fetch_assoc();
$total_pages = ceil($total_results['total'] / $limit);

// 3. FETCH DATA
$sql = "SELECT * FROM posts ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<style>
    /* Sticky Footer CSS */
    html, body { height: 100%; }
    body { display: flex; flex-direction: column; background-color: #fcfcfd; }
    #main-content { flex: 1 0 auto; }
    footer { flex-shrink: 0; }

    /* Modern Card Design */
    .eng-card {
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease-in-out;
        background: white;
    }
    .eng-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        border-color: #085add;
    }
</style>

<div id="main-content">
    <header class="py-5 border-bottom bg-white">
        <div class="container text-center">
            <h1 class="fw-bolder display-6 mb-1">DevBlog Somalia</h1>
            <p class="text-muted mb-0">System Engineering Archive</p>
        </div>
    </header>

    <main class="container py-5">
        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $can_manage = ($current_user_role === 'admin' || $row['author'] === $current_user_name);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 eng-card rounded-4 border-0 shadow-sm position-relative">
                        <div class="card-body p-4">
                            <h3 class="h5 fw-bold mb-3">
                                <a href="blog-single.php?id=<?= $row['id'] ?>" class="stretched-link text-dark text-decoration-none">
                                    <?= htmlspecialchars($row['title']) ?>
                                </a>
                            </h3>
                            <p class="text-muted small mb-0">
                                <?= htmlspecialchars(substr($row['content'], 0, 110)) ?>...
                            </p>
                        </div>
                        
                        <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-0" style="z-index: 5; position: relative;">
                            <hr class="my-3 opacity-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="small text-muted">By <?= htmlspecialchars($row['author']) ?></div>

                                <?php if ($can_manage): ?>
                                    <div class="btn-group border rounded-3 overflow-hidden shadow-sm">
                                        <a href="../views/blog/edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-white text-success px-2 border-end">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="../views/blog/delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-white text-danger px-2" onclick="return confirm('Delete this log?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav class="mt-5 mb-5">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link shadow-sm border mx-1 rounded" href="blog.php?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </main>
</div>

<?php 
$conn->close();
require_once __DIR__ . "/views/layouts/footer.php"; 
?>