<?php 
// 1. DATABASE LOGIC AT THE TOP
require_once __DIR__ . "/app/config/database.php"; 

$message_sent = false;
$error_message = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $subject   = mysqli_real_escape_string($conn, $_POST['subject']);
    $message   = mysqli_real_escape_string($conn, $_POST['message']);

    // Prepare SQL
    $sql = "INSERT INTO messages (full_name, email, subject, message) 
            VALUES ('$full_name', '$email', '$subject', '$message')";

    if ($conn->query($sql) === TRUE) {
        $message_sent = true;
    } else {
        $error_message = "Database Error: " . $conn->error;
    }
}

// 2. INCLUDE HEADER
include __DIR__ . "/views/layouts/header.php"; 
?>


<style>
  :root {
    --primary-blue: #085add;
    --text-navy: #0f172a;
  }
  body { font-family: 'Poppins', sans-serif; background-color: #ffffff; }
  .contact-hero { padding: 80px 0; }
  .contact-wrapper {
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 24px;
    box-shadow: 0 30px 60px rgba(8, 90, 221, 0.05);
    overflow: hidden;
  }
  .visual-panel {
    background: var(--primary-blue);
    color: white;
    padding: 60px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .form-control-modern {
    width: 100%;
    background: #f8fafc;
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    padding: 14px 18px;
    transition: all 0.2s ease;
  }
  .form-control-modern:focus {
    outline: none;
    border-color: var(--primary-blue);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(8, 90, 221, 0.05);
  }
  .btn-ship {
    background: var(--primary-blue);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 18px;
    font-weight: 700;
    width: 100%;
    text-transform: uppercase;
    transition: all 0.3s ease;
  }
  .btn-ship:hover { transform: translateY(-2px); filter: brightness(1.1); }
</style>

<section class="contact-hero">
  <div class="container">
    <div class="contact-wrapper">
      <div class="row g-0">
        
        <div class="col-lg-5 d-none d-lg-block">
          <div class="visual-panel">
            <div>
              <h2 class="display-6 fw-bold mb-4">Let's solve <br> <span style="color: var(--primary-blue)">together.</span></h2>
              <p class="opacity-75">Connect with the Somalia developer community core team.</p>
            </div>
            <div>
              <p class="small fw-bold text-uppercase opacity-50 mb-1">HQ</p>
              <p class="fw-medium">Mogadishu, Banaadir</p>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="p-5">
            
            <?php if($message_sent): ?>
                <div class="alert alert-success border-0 rounded-3 p-3 mb-4 d-flex align-items-center" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
                    <i class="fas fa-check-circle me-3"></i> 
                    <strong>Shipment Successful!</strong> Your message has been saved to the database.
                </div>
            <?php endif; ?>

            <?php if($error_message): ?>
                <div class="alert alert-danger border-0 rounded-3 p-3 mb-4">
                    <strong>Error:</strong> <?= $error_message ?>
                </div>
            <?php endif; ?>

            <h3 class="fw-bold mb-4 text-dark">Send a Message</h3>
            
            <form action="" method="POST">
              <div class="row">
                <div class="col-md-6 mb-4">
                  <label class="small fw-bold text-uppercase text-muted d-block mb-2">Full Name</label>
                  <input type="text" name="full_name" class="form-control-modern" placeholder="Ali Mohamed" required>
                </div>
                
                <div class="col-md-6 mb-4">
                  <label class="small fw-bold text-uppercase text-muted d-block mb-2">Email Address</label>
                  <input type="email" name="email" class="form-control-modern" placeholder="ali@dev.so" required>
                </div>

                <div class="col-12 mb-4">
                  <label class="small fw-bold text-uppercase text-muted d-block mb-2">Subject</label>
                  <input type="text" name="subject" class="form-control-modern" placeholder="Technical Inquiry">
                </div>

                <div class="col-12 mb-4">
                  <label class="small fw-bold text-uppercase text-muted d-block mb-2">Your Message</label>
                  <textarea name="message" rows="5" class="form-control-modern" placeholder="Tell us what's on your mind..." required></textarea>
                </div>

                <div class="col-12">
                  <button type="submit" class="btn-ship">Ship Message <i class="fas fa-paper-plane ms-2"></i></button>
                </div>
              </div>
            </form>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php 
$conn->close();
include __DIR__ . "/views/layouts/footer.php"; 
?>