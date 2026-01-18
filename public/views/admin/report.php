<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php"; 

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); 
    exit();
}

// 1. Get filter values from URL
$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';
$role_filter = $_GET['role'] ?? '';

// 2. Build the WHERE clause dynamically
$where_clauses = [];

if (!empty($start) && !empty($end)) {
    $start_clean = $conn->real_escape_string($start);
    $end_clean = $conn->real_escape_string($end);
    $where_clauses[] = "p.created_at BETWEEN '$start_clean 00:00:00' AND '$end_clean 23:59:59'";
}

if (!empty($role_filter)) {
    $role_clean = $conn->real_escape_string($role_filter);
    $where_clauses[] = "u.role = '$role_clean'";
}

$where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

// --- FUNCTIONALITY: EXPORT CSV ---
if (isset($_GET['action']) && $_GET['action'] == 'export') {
    $filename = "report_" . ($role_filter ?: 'all_roles') . "_" . date('Ymd') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Author', 'Role', 'Post Count', 'Last Activity']);
    
    $csv_query = "SELECT p.author, u.role, COUNT(p.id) as total, MAX(p.created_at) as last 
                  FROM posts p 
                  INNER JOIN users u ON p.author = u.name 
                  $where_sql GROUP BY p.author, u.role";
    $res = $conn->query($csv_query);
    while ($row = $res->fetch_assoc()) { fputcsv($output, $row); }
    fclose($output);
    exit();
}

// --- VIEW DATA ---
$count_query = "SELECT COUNT(p.id) as total FROM posts p INNER JOIN users u ON p.author = u.name $where_sql";
$total_articles = $conn->query($count_query)->fetch_assoc()['total'];

$author_stats = $conn->query("SELECT p.author, u.role, COUNT(p.id) as total_posts, MAX(p.created_at) as last_post 
                             FROM posts p 
                             INNER JOIN users u ON p.author = u.name 
                             $where_sql 
                             GROUP BY p.author, u.role 
                             ORDER BY total_posts DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Analytics | DEVBLOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7fe; }
        .admin-container { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: 260px; padding: 25px; transition: 0.3s; }
        .glass-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-bottom: 25px; border: 1px solid rgba(0,0,0,0.05); }
        .avatar-box { width: 40px; height: 40px; border-radius: 10px; background: #eef2ff; color: #6366f1; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .badge-admin { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .badge-author { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <div class="admin-container">
        <?php include __DIR__ . "/../layouts/admin_sidebar.php"; ?>

        <main class="main-content">
            <header class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-4 shadow-sm">
                <h2 class="h4 mb-0 fw-bold">System Analytics</h2>
                <div class="d-flex gap-2">
                    <button type="button" onclick="exportCSV()" class="btn btn-outline-primary btn-sm rounded-pill px-3">Export CSV</button>
                    <a href="report.php" class="btn btn-light btn-sm rounded-pill px-3 border">Reset</a>
                </div>
            </header>

            <div class="glass-card">
                <form method="GET" action="report.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">START DATE</label>
                        <input type="date" name="start_date" class="form-control border-0 bg-light" value="<?= $start ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">END DATE</label>
                        <input type="date" name="end_date" class="form-control border-0 bg-light" value="<?= $end ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">FILTER BY ROLE</label>
                        <select name="role" class="form-select border-0 bg-light">
                            <option value="">All Roles</option>
                            <option value="admin" <?= $role_filter == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="author" <?= $role_filter == 'author' ? 'selected' : '' ?>>Author</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-bold">Apply Filter</button>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="glass-card text-center h-100 d-flex flex-column justify-content-center">
                        <p class="text-muted small fw-bold mb-1">ARTICLES BY <?= strtoupper($role_filter ?: 'ALL ROLES') ?></p>
                        <h1 class="display-3 fw-bold text-primary mb-0"><?= $total_articles ?></h1>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="glass-card">
                        <h6 class="fw-bold mb-4">Post Distribution</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>AUTHOR</th>
                                        <th>ROLE</th>
                                        <th class="text-center">POSTS</th>
                                        <th>LAST ACTIVE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($author_stats && $author_stats->num_rows > 0): ?>
                                        <?php while($row = $author_stats->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-box"><?= strtoupper(substr($row['author'], 0, 1)) ?></div>
                                                    <span class="fw-bold"><?= htmlspecialchars($row['author']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill px-2 <?= $row['role'] == 'admin' ? 'badge-admin' : 'badge-author' ?>">
                                                    <?= strtoupper($row['role']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold"><?= $row['total_posts'] ?></td>
                                            <td class="text-muted small"><?= date('M d, Y', strtotime($row['last_post'])) ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No data found for the selected criteria.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function exportCSV() {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('action', 'export');
        window.location.href = 'report.php?' + urlParams.toString();
    }
    </script>
</body>
</html>