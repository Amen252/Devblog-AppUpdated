<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . "/app/config/database.php"; 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// If no ID is passed, we grab the most recent post automatically
if($id === 0) {
    $res = $conn->query("SELECT id FROM posts ORDER BY id DESC LIMIT 1");
    $row = $res->fetch_assoc();
    $id = $row['id'] ?? 0;
}

$query = "SELECT posts.*, users.role FROM posts 
          LEFT JOIN users ON posts.author = users.name 
          WHERE posts.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) { die("No post found."); }

include __DIR__ . "/views/layouts/header.php"; 
?>

<div id="reading-progress" style="position: fixed; top: 0; left: 0; width: 0%; height: 6px; background: #085add; z-index: 99999;"></div>

<div id="blog-fixed-layout" style="width: 100vw; display: flex; flex-direction: column; align-items: center; background-color: #ffffff; padding-top: 180px; margin: 0; min-height: 100vh; position: relative; left: 0;">

    <div style="width: 90%; max-width: 800px; text-align: left; position: relative;">
        
        <div style="margin-bottom: 40px;">
            <a href="blog.php" style="text-decoration: none; color: #64748b; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="arrow-left" style="width: 18px;"></i> BACK TO ARCHIVE
            </a>
        </div>

        <article style="width: 100%;">
            <header style="margin-bottom: 50px; border-bottom: 1px solid #f1f5f9; padding-bottom: 30px;">
                <span style="font-size: 0.8rem; font-weight: 700; color: #085add; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;">
                    ENGINEERING LOG • <?= date('M d, Y', strtotime($post['created_at'])) ?>
                </span>
                <h1 style="font-size: clamp(2.5rem, 5vw, 3.5rem); font-weight: 800; line-height: 1.1; color: #0f172a; margin: 0; letter-spacing: -2px;">
                    <?= htmlspecialchars($post['title']) ?>
                </h1>
            </header>

            <div style="font-size: 1.25rem; line-height: 2.2; color: #334155; white-space: pre-line; margin-bottom: 100px; text-align: left;">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>

            <footer style="display: flex; align-items: center; gap: 20px; padding: 40px; background: #f8fafc; border-radius: 24px; border: 1px solid #e2e8f0; margin-bottom: 150px;">
                <div style="width: 60px; height: 60px; background: #fff; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: #085add; border: 1px solid #e2e8f0;">
                    <i data-lucide="user-round" style="width: 30px; stroke-width: 2.5px;"></i>
                </div>
                <div>
                    <span style="font-weight: 800; display: block; font-size: 1.2rem; color: #1e293b;">
                        <?= htmlspecialchars($post['author']) ?>
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 800; color: #085add; background: rgba(8, 90, 221, 0.1); padding: 5px 12px; border-radius: 8px; text-transform: uppercase;">
                        <?= htmlspecialchars($post['role'] ?? 'CONTRIBUTOR') ?>
                    </span>
                </div>
            </footer>
        </article>
    </div>
</div>

<?php 
$conn->close();
include __DIR__ . "/views/layouts/header.php"; 
?>

<script>  
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    // Scroll progress bar
    window.onscroll = function() {
        var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var scrolled = (winScroll / height) * 100;
        document.getElementById("reading-progress").style.width = scrolled + "%";
    };
</script>