<?php 
session_start();
require_once __DIR__ . "/app/config/database.php"; 

$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // SMART LOGIN: Accepts BCrypt hash OR plain-text (for your admin123 fix)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role']; 
            
            if ($_SESSION['user_role'] === 'admin') {
                header("Location: views/admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}

include __DIR__ . "/views/layouts/header.php"; 
?>

<style>
    :root { 
        --primary-blue: #085add; 
        --soft-slate: #f1f5f9; 
    }
    body { 
        background-color: #f8fafc; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
    }
    .auth-container { 
        min-height: 100vh; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        padding: 2rem; 
    }
    .modern-card { 
        background: #ffffff; 
        border-radius: 2rem; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); 
        overflow: hidden; 
        border: 1px solid rgba(0, 0, 0, 0.05); 
        display: flex; 
        width: 100%; 
        max-width: 1000px; 
    }
    .brand-aside { 
        background: linear-gradient(135deg, #4f46e5 0%, var(--primary-blue) 100%); 
        padding: 4rem; 
        color: white; 
        width: 40%; 
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
    }
    .form-aside { 
        padding: 4rem; 
        width: 60%; 
        background: white; 
    }
    .input-wrapper { 
        position: relative; 
        margin-bottom: 1.5rem; 
    }
    .input-wrapper i.form-icon { 
        position: absolute; 
        left: 1rem; 
        top: 50%; 
        transform: translateY(-50%); 
        color: #94a3b8; 
    }
    /* Password Toggle Styling */
    .password-toggle {
        position: absolute;
        right: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        transition: all 0.3s ease;
        z-index: 100; /* Ensures icon is above the input field */
    }
    .password-toggle:hover {
        color: var(--primary-blue);
    }
    .form-control-modern { 
        width: 100%; 
        background: var(--soft-slate); 
        border: 2px solid transparent; 
        border-radius: 1rem; 
        padding: 1rem 3.5rem 1rem 3rem; /* Extra right padding for the eye icon */
        transition: all 0.3s ease; 
    }
    .form-control-modern:focus { 
        outline: none; 
        background: white; 
        border-color: var(--primary-blue); 
        box-shadow: 0 0 0 4px rgba(8, 90, 221, 0.1); 
    }
    .btn-ship { 
        background: var(--primary-blue); 
        color: white; 
        border: none; 
        border-radius: 1rem; 
        padding: 1rem; 
        font-weight: 600; 
        width: 100%; 
        transition: all 0.3s ease; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
    }
    .btn-ship:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 10px 15px -3px rgba(8, 90, 221, 0.3); 
    }
</style>

<div class="auth-container">
    <div class="modern-card">
        <div class="brand-aside">
            <div>
                <div class="mb-4">
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold small">DevBlog Somalia</span>
                </div>
                <h2 class="display-5 mb-4">Code.<br>Connect.<br>Ship.</h2>
                <p class="opacity-75">Your daily dose of technical insights and community-driven knowledge.</p>
            </div>
            <div class="small opacity-50">&copy; 2026 Developer Community.</div>
        </div>

        <div class="form-aside">
            <div class="mb-5">
                <h3 class="fw-bold text-dark mb-1">Welcome Back</h3>
                <p class="text-muted small">Enter your credentials to access your dashboard.</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger border-0 rounded-4 p-3 mb-4" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="input-wrapper">
                    <i class="fas fa-at form-icon"></i>
                    <input type="email" name="email" class="form-control-modern" placeholder="Email Address" required>
                </div>

                <div class="input-wrapper">
                    <i class="fas fa-key form-icon"></i>
                    <input type="password" name="password" id="passwordInput" class="form-control-modern" placeholder="Password" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>

                <button type="submit" class="btn-ship mb-4">
                    Login Account <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Robust Password Toggle Script
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('#togglePassword');
        const passInput = document.querySelector('#passwordInput');

        if(toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                // Switch input type
                const isPassword = passInput.getAttribute('type') === 'password';
                passInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                // Switch icon classes
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
                
                // Visual feedback
                this.style.color = isPassword ? '#085add' : '#94a3b8';
            });
        }
    });
</script>

<?php include __DIR__ . "/views/layouts/footer.php"; ?>