<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/app/config/database.php"; 

$error = "";

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $author = $_SESSION['user_name'];

    if (!empty($title) && !empty($content)) {
        $query = "INSERT INTO posts (title, content, author) VALUES ('$title', '$content', '$author')";
        if ($conn->query($query)) {
            header("Location: index.php?status=success");
            exit();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Article | DevBlog</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --glass-bg: rgba(255, 255, 255, 0.8);
            --border-color: rgba(226, 232, 240, 0.8);
        }

        body { 
            background: #fdfdfd; 
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 30px 30px;
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding-top: 100px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content { flex: 1; }

        .editor-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            border: 1px solid var(--border-color);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            padding: 50px;
            transition: transform 0.3s ease;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .form-control {
            border-radius: 16px;
            padding: 16px 20px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .title-input {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
        }

        .btn-publish {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 14px 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-publish:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
            opacity: 0.9;
            color: white;
        }

        .btn-cancel {
            font-weight: 600;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .btn-cancel:hover { color: #ef4444; }

        .badge-author {
            background: #f1f5f9;
            color: #475569;
            padding: 8px 16px;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <?php include_once __DIR__ . "/views/layouts/header.php"; ?>

    <main class="container main-content py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                <div class="editor-card">
                    <header class="mb-5 d-flex justify-content-between align-items-end">
                        <div>
                            <h1 class="fw-800 mb-2" style="font-weight: 800; font-size: 2.2rem; color: #0f172a;">New Article</h1>
                            <p class="text-muted mb-0">Share your technical insights with the world.</p>
                        </div>
                        <div class="badge-author d-none d-md-block">
                            <i data-lucide="user" class="me-1" style="width: 16px;"></i> 
                            <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </div>
                    </header>

                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-2">
                            <i data-lucide="alert-circle" style="width: 20px;"></i>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control title-input" placeholder="Give it a great title..." required>
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Body Content</label>
                            <textarea name="content" class="form-control" rows="15" placeholder="Once upon a code..." style="font-size: 1.1rem; line-height: 1.6;" required></textarea>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-4 border-top">
                            <a href="index.php" class="btn-cancel">Discard Draft</a>
                            <button type="submit" class="btn-publish">
                                <i data-lucide="send" style="width: 20px;"></i>
                                Publish Article
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <?php include_once __DIR__ . "/views/layouts/footer.php"; ?>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>