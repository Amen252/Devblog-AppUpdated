<?php 
// 1. Path verified from VS Code: public/app/config/database.php
include __DIR__ . "/app/config/database.php"; 
// 2. Path verified from VS Code: public/views/layouts/header.php
include __DIR__ . "/views/layouts/header.php"; 

$limit = 6; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_results = $conn->query("SELECT COUNT(*) as id FROM posts")->fetch_assoc();
$total_pages = ceil($total_results['id'] / $limit);

$sql = "SELECT * FROM posts ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<style>
  :root {
    --brand-blue: #085add;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border-base: #e2e8f0;
  }

  body { 
    background-color: #ffffff; 
    color: var(--text-main); 
    font-family: 'Poppins', sans-serif;
    padding-top: 80px; /* Space for glass header */
  }

  .blog-hero {
    padding: 100px 0 60px;
    border-bottom: 1px solid var(--border-base);
    margin-bottom: 60px;
  }

  .post-card {
    border: 1px solid var(--border-base);
    border-radius: 16px;
    padding: 35px;
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    transition: all 0.3s ease;
    text-decoration: none !important; /* Makes entire card clickable without underline */
  }

  .post-card:hover {
    border-color: var(--brand-blue);
    box-shadow: 0 10px 30px rgba(8, 90, 221, 0.05);
    transform: translateY(-5px);
  }

  .post-date {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-muted);
    margin-bottom: 12px;
    display: block;
  }

  .post-title {
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1.4;
    color: var(--text-main);
    margin-bottom: 12px;
    transition: color 0.3s ease;
  }

  .post-card:hover .post-title {
    color: var(--brand-blue);
  }

  .post-excerpt {
    font-size: 0.9rem;
    line-height: 1.6;
    color: var(--text-muted);
    margin-bottom: 25px;
  }

  .post-footer {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 20px;
    border-top: 1px solid #f8fafc;
  }

  .author-meta {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-main);
  }

  .read-more {
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--brand-blue);
    display: flex;
    align-items: center;
    gap: 6px;
    transition: gap 0.2s ease;
  }

  .post-card:hover .read-more {
    gap: 12px;
  }

  .pagination .page-link {
    border: none;
    color: var(--text-muted);
    font-weight: 600;
    padding: 8px 16px;
  }

  .pagination .page-item.active .page-link {
    color: var(--brand-blue) !important;
    background: rgba(8, 90, 221, 0.05) !important;
    border-radius: 8px;
  }
</style>

<header class="blog-hero">
  <div class="container text-center">
    <h1 class="fw-bold display-5">The Archive<span style="color: var(--brand-blue);">.</span></h1>
    <p class="text-muted small text-uppercase fw-semibold" style="letter-spacing: 2px;">Technical Logs & Engineering Insights</p>
  </div>
</header>

<main class="container pb-5">
  <div class="row g-4">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <a href="blog-single.php?id=<?= $row['id'] ?>" class="post-card">
          
          <span class="post-date">
            <?= date('M d, Y', strtotime($row['created_at'])) ?>
          </span>

          <h3 class="post-title">
            <?= htmlspecialchars($row['title']) ?>
          </h3>
          
          <p class="post-excerpt">
            <?= htmlspecialchars(substr($row['content'], 0, 110)) ?>...
          </p>

          <div class="post-footer">
            <div class="author-meta">
              <span class="text-muted fw-normal">By</span> <?= htmlspecialchars($row['author']) ?>
            </div>
            <div class="read-more">
              Read More <i data-lucide="arrow-right" style="width: 16px;"></i>
            </div>
          </div>

        </a>
      </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

  <?php if ($total_pages > 1): ?>
  <nav class="mt-5">
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
          <a class="page-link" href="blog.php?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
</main>

<?php 
$conn->close();
include __DIR__ . "/views/layouts/footer.php"; 
?>