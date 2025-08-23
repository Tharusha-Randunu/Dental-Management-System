<?php
session_start();
include '../includes/header.php';
include '../config/db.php';

// Load Composer's autoloader for PHPMailer
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $result = $conn->query("SELECT * FROM users WHERE Email='$email'");

    if ($result->num_rows > 0) {
        // Generate token and update DB
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $conn->query("UPDATE users SET reset_token='$token', token_expiry='$expiry' WHERE Email='$email'");

        // Create reset link
        $resetLink = "http://localhost/Dental_System/auth/reset_password.php?token=$token";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tharushapereraonline@gmail.com';         /
            $mail->Password   = 'rcgu jwed yljz ajnb';           
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('tharushapereraonline@gmail.com', 'Dental Hub');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - Dental Hub';
            $mail->Body    = "
                <h3>Password Reset Request</h3>
                <p>Click the link below to reset your password. This link is valid for 1 hour:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>If you didn’t request a reset, you can ignore this message.</p>
            ";

            $mail->send();
            $message = "<div class='alert alert-success'>A password reset link has been sent to your email.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Email not found in our system.</div>";
    }
}
?>

<div class="container mt-5">
    <div class="card p-4 shadow-lg">
        <h2 class="text-center text-primary">Forgot Password</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Send Reset Link</button><p></p>
        </form>

        <a href="login.php" ><button type="submit" name="submit" class="btn btn-outline-secondary btn-back-home w-100">← Back to Login</button></a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
