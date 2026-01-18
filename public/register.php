<?php 
require_once __DIR__ . "/app/config/database.php"; 

$success = false;
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // 1. Check if email already exists
    $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($check_email->num_rows > 0) {
        $error = "This email is already registered.";
    } else {
        // 2. Hash password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. Insert into database
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            $success = "Account created! You can now sign in.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

include __DIR__ . "/views/layouts/header.php"; 
?>

<style>
  /* Keep your existing CSS here */
  :root { --primary-blue: #085add; --soft-slate: #f1f5f9; }
  body { background-color: #f8fafc; font-family: 'Poppins', sans-serif; }
  .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2.5rem 1rem; }
  .modern-card { background: #ffffff; border-radius: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); overflow: hidden; display: flex; width: 100%; max-width: 1050px; }
  .brand-aside { background: linear-gradient(135deg, #4f46e5 0%, var(--primary-blue) 100%); padding: 4.5rem; color: white; width: 40%; display: flex; flex-direction: column; justify-content: space-between; }
  .form-aside { padding: 4.5rem; width: 60%; background: white; }
  .input-wrapper { position: relative; margin-bottom: 1.25rem; }
  .input-wrapper i { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #94a3b8; }
  .form-control-modern { width: 100%; background: var(--soft-slate); border: 2px solid transparent; border-radius: 1rem; padding: 1rem 1rem 1rem 3.25rem; transition: all 0.3s ease; }
  .form-control-modern:focus { outline: none; border-color: var(--primary-blue); background: white; box-shadow: 0 0 0 4px rgba(8, 90, 221, 0.1); }
  .btn-ship { background: var(--primary-blue); color: white; border: none; border-radius: 1rem; padding: 1.1rem; font-weight: 600; width: 100%; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1.5px; margin-top: 1rem; }
  .btn-ship:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(8, 90, 221, 0.3); }
  @media (max-width: 992px) { .brand-aside { display: none; } .form-aside { width: 100%; padding: 3.5rem 2rem; } }
</style>

<div class="auth-container">
  <div class="modern-card">
    
    <div class="brand-aside">
      <div>
        <div class="mb-4">
            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold small">DevBlog Somalia</span>
        </div>
        <h2 class="display-5 fw-bold mb-4">Start your<br>Journey.</h2>
        <p class="opacity-75 fs-5">Join our community of developers to learn and grow.</p>
      </div>
      <div class="small opacity-50">&copy; 2026 Developer Community.</div>
    </div>

    <div class="form-aside">
      <div class="mb-5">
        <h3 class="fw-bold text-dark mb-1">Create Account</h3>
        <p class="text-muted small">It only takes a minute to get started.</p>
      </div>

      <?php if($success): ?>
        <div class="alert alert-success border-0 rounded-4 p-3 mb-4" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
            <i class="fas fa-check-circle me-2"></i> <?= $success ?>
        </div>
      <?php endif; ?>

      <?php if($error): ?>
        <div class="alert alert-danger border-0 rounded-4 p-3 mb-4" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST">
        
        <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Full Name</label>
        <div class="input-wrapper">
          <i class="fas fa-user"></i>
          <input type="text" name="name" class="form-control-modern" placeholder="John Doe" required>
        </div>

        <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Email Address</label>
        <div class="input-wrapper">
          <i class="fas fa-at"></i>
          <input type="email" name="email" class="form-control-modern" placeholder="john@example.com" required>
        </div>

        <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Password</label>
        <div class="input-wrapper">
          <i class="fas fa-key"></i>
          <input type="password" name="password" class="form-control-modern" placeholder="Min. 8 characters" required>
        </div>

        <button type="submit" class="btn-ship mb-4">
          Create Account <i class="fas fa-paper-plane ms-2"></i>
        </button>

        <div class="text-center">
          <span class="text-muted small">Already have an account?</span>
          <a href="login.php" class="ms-1 fw-bold text-decoration-none" style="color: var(--primary-blue);">Sign In</a>
        </div>
      </form>
    </div>

  </div>
</div>

<?php include __DIR__ . "/views/layouts/footer.php"; ?>