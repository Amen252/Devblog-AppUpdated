<?php
session_start();
require_once __DIR__ . "/../../app/config/database.php"; 

// 1. SECURITY: Admin Only
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login.php"); 
    exit();
}

// 2. DELETE HANDLER
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: messages.php?status=deleted");
    }
    $stmt->close();
    exit();
}

// 3. FETCH MESSAGES
$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox | DEVBLOG Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7fe; }
        
        .message-card {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.05);
            padding: 24px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .sender-circle {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: #eef2ff;
            color: #4e73df;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.2rem;
        }

        .msg-content {
            background: #f8fafc;
            border-radius: 12px;
            padding: 18px;
            color: #334155;
            font-size: 0.95rem;
            line-height: 1.6;
            border: 1px solid #f1f5f9;
        }

        .btn-action {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .btn-delete { background: #fff1f2; color: #e11d48; border: none; }
        .btn-delete:hover { background: #e11d48; color: white; }

        .badge-new {
            background: #4e73df; color: white;
            font-size: 0.65rem; padding: 4px 8px; border-radius: 6px;
        }
    </style>
</head>
<body>

    <?php require_once __DIR__ . "/../layouts/admin_sidebar.php"; ?>

    <main class="main-content">
        <header class="top-header d-flex justify-content-between align-items-center bg-white px-4 py-3 mb-4 shadow-sm">
            <div>
                <h2 class="h4 mb-0 fw-bold">Messages Inbox</h2>
                <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                    <small class="text-danger fw-bold"><i class="fas fa-check-circle"></i> Message deleted successfully</small>
                <?php else: ?>
                    <p class="text-muted small mb-0">Manage your communication with users</p>
                <?php endif; ?>
            </div>
        </header>

        <div class="container-fluid px-4">
            <div class="row">
                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($msg = $result->fetch_assoc()): 
                        // FIX: Check if 'name' exists to avoid PHP Warning
                        $senderName = $msg['name'] ?? explode('@', $msg['email'])[0];
                        $is_new = (strtotime($msg['created_at']) > strtotime('-24 hours'));
                    ?>
                    <div class="col-12">
                        <div class="message-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sender-circle">
                                        <?= strtoupper(substr($senderName, 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">
                                            <?= htmlspecialchars($msg['subject'] ?? 'Inquiry') ?>
                                            <?php if($is_new): ?><span class="badge-new ms-2">NEW</span><?php endif; ?>
                                        </h5>
                                        <div class="text-muted small">
                                            <span class="text-primary"><?= htmlspecialchars($msg['email']) ?></span>
                                            <span class="mx-1">•</span>
                                            <?= date('M d, h:i A', strtotime($msg['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <a href="?delete_id=<?= $msg['id'] ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Permanently delete this message?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                            
                            <div class="msg-content">
                                <?= nl2br(htmlspecialchars($msg['message'] ?? '')) ?>
                            </div>

                            <div class="mt-3">
                                <a href="mailto:<?= $msg['email'] ?>?subject=Re: <?= urlencode($msg['subject'] ?? 'Inquiry') ?>" 
                                   class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="fas fa-paper-plane me-2"></i> Reply via Email
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p>No messages available.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>