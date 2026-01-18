<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php"; 

// 1. SECURITY: Admin Only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); 
    exit();
}

$admin_id = $_SESSION['user_id']; // Current logged-in admin ID

// 2. ACTION HANDLER: Update role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $u_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    
    if ($u_id == $admin_id) {
        header("Location: users.php?error=self_role");
        exit();
    }

    if (in_array($new_role, ['admin', 'user'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $u_id);
        $stmt->execute();
        $stmt->close();
        header("Location: users.php?success=1");
        exit();
    }
}

// 3. FETCH USERS (With Search Filter)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT id, name, email, role, created_at FROM users";

if (!empty($search)) {
    $search_term = "%$search%";
    $stmt = $conn->prepare("$sql WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("$sql ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | DEVBLOG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary-blue: #205bb9;
        --light-blue-bg: rgba(255, 255, 255, 0.15);
        --soft-border: rgba(255, 255, 255, 0.4);
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7fe; }
    
    /* Sidebar Customization */
    .sidebar { 
        width: 260px; 
        height: 100vh; 
        position: fixed; 
        background: var(--primary-blue); 
        color: white; 
        padding: 20px; 
        box-shadow: 4px 0 15px rgba(0,0,0,0.05);
    }

    .logo-text { font-weight: 800; font-size: 1.5rem; margin-bottom: 35px; color: #fff; }
    .logo-text span { opacity: 0.8; }

    .sidebar-nav ul { list-style: none; padding: 0; }
    .sidebar-nav li a { 
        color: rgba(255, 255, 255, 0.75); 
        text-decoration: none; 
        display: block; 
        padding: 12px 18px; 
        border-radius: 12px; 
        margin-bottom: 8px; 
        transition: all 0.3s ease;
        border: 1px solid transparent; /* Placeholder for active border */
    }

    /* Active & Hover State with soft border */
    .sidebar-nav li a.active { 
        background: var(--light-blue-bg); 
        color: white; 
        font-weight: 600;
        border: 1px solid var(--soft-border); /* Light soft border */
        backdrop-filter: blur(5px);
    }

    .sidebar-nav li a:hover:not(.active) { 
        background: rgba(255, 255, 255, 0.08);
        color: white;
    }

    /* Rest of the UI */
    .main-content { margin-left: 260px; padding: 25px; }
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
        padding: 25px;
    }
    .role-badge {
        padding: 8px 16px; font-weight: 700; font-size: 0.75rem;
        border-radius: 12px; display: inline-flex; align-items: center; gap: 6px;
    }
    .role-admin { background: var(--primary-blue); color: white; }
    .role-user { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    
    .avatar-box {
        width: 40px; height: 40px; border-radius: 12px;
        background: #eef2ff; color: var(--primary-blue);
        display: flex; align-items: center; justify-content: center; font-weight: 800;
    }
    
    .btn-primary { background-color: var(--primary-blue); border: none; }
    .btn-primary:hover { background-color: #1a4a99; }
</style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-text">DEV<span>BLOG</span></div>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-label small text-uppercase text-muted mb-2">Main Menu</li>
                <li><a href="dashboard.php"><i class="fas fa-th-large me-2"></i> Dashboard</a></li>
                <li><a href="../blog/list.php"><i class="fas fa-file-alt me-2"></i> All Posts</a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users me-2"></i> Users</a></li>
                <li><a href="messages.php"><i class="fas fa-comment-dots me-2"></i> Messages</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="top-header d-flex justify-content-between align-items-center bg-white px-4 py-3 mb-4 shadow-sm rounded-4">
            <div class="d-flex align-items-center gap-3">
                <h2 class="h4 mb-0 fw-bold text-dark">User Management</h2>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <i class="fas fa-plus-circle me-1"></i> Add User
                </button>
            </div>

            <div class="d-flex align-items-center gap-3">
                <form action="" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-0 bg-light shadow-none" 
                               placeholder="Search users..." value="<?= htmlspecialchars($search) ?>" style="border-radius: 10px 0 0 10px;">
                        <button class="btn btn-dark" type="submit" style="border-radius: 0 10px 10px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <?php if(isset($_GET['success'])): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">Success!</span>
                <?php endif; ?>
            </div>
        </header>

        <div class="container-fluid">
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th class="border-0">USER IDENTITY</th>
                                <th class="border-0">EMAIL</th>
                                <th class="border-0 text-center">ROLE</th>
                                <th class="border-0">JOINED</th>
                                <th class="border-0 text-end">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-box"><?= strtoupper(substr($row['name'], 0, 1)) ?></div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                            <?php if($row['id'] == $admin_id): ?>
                                                <span class="badge bg-primary-subtle text-primary x-small">You</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary small"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn role-badge <?= $row['role'] == 'admin' ? 'role-admin' : 'role-user' ?> dropdown-toggle border-0" 
                                                type="button" data-bs-toggle="dropdown" <?= $row['id'] == $admin_id ? 'disabled' : '' ?>>
                                            <?= strtoupper($row['role']) ?>
                                        </button>
                                        <ul class="dropdown-menu shadow border-0">
                                            <li>
                                                <form method="POST" class="px-2">
                                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                    <input type="hidden" name="new_role" value="admin">
                                                    <button type="submit" name="update_role" class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2">
                                                        <i class="fas fa-shield-halved text-primary"></i> Promote to Admin
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" class="px-2">
                                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                    <input type="hidden" name="new_role" value="user">
                                                    <button type="submit" name="update_role" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger rounded-2">
                                                        <i class="fas fa-user-minus"></i> Demote to User
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-muted small"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                <td class="text-end">
                                    <a href="delete_user.php?id=<?= $row['id'] ?>" 
                                       class="action-btn btn-delete <?= $row['id'] == $admin_id ? 'btn-disabled' : '' ?>" 
                                       onclick="return confirm('Are you sure? User data will be permanently lost.')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="fw-bold">Register New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="add_user_process.php" method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Full Name</label>
                            <input type="text" name="name" class="form-control border-0 bg-light p-2" required placeholder="Name">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Email</label>
                            <input type="email" name="email" class="form-control border-0 bg-light p-2" required placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Password</label>
                            <input type="password" name="password" class="form-control border-0 bg-light p-2" required placeholder="Min. 8 chars">
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-1">Account Role</label>
                            <select name="role" class="form-select border-0 bg-light p-2">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm">
                            Create User Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>