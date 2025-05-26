<?php
session_start();
include '../includes/header.php';
include '../config/db.php';

$message = '';
$showForm = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT Email, token_expiry FROM patients WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (strtotime($row['token_expiry']) > time()) {
            $email = $row['Email'];
            $showForm = true;
        } else {
            $message = "<div class='alert alert-danger'>This reset link has expired. Please request a new one.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Invalid reset token.</div>";
    }
    $stmt->close();
} else {
    $message = "<div class='alert alert-warning'>No reset token provided.</div>";
}

if (isset($_POST['reset_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];

    if ($password !== $confirm_password) {
        $message = "<div class='alert alert-warning'>Passwords do not match.</div>";
        $showForm = true;
    } elseif (strlen($password) < 6 || strlen($password) > 15) {
        $message = "<div class='alert alert-warning'>Password should be between 6 and 15 characters.</div>";
        $showForm = true;
    } else {
        $plain_password = $password;

        $stmt = $conn->prepare("UPDATE patients SET Password=?, reset_token=NULL, token_expiry=NULL WHERE reset_token=?");
        $stmt->bind_param("ss", $plain_password, $token);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Password has been reset successfully. You can now <a href='patient_login.php'>login</a>.</div>";
            $showForm = false;
        } else {
            $message = "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
            $showForm = true;
        }
        $stmt->close();
    }
}
?>

<div class="container mt-5">
    <div class="card p-4 shadow-lg" style="max-width: 450px; margin: auto;">
        <h2 class="text-center text-primary mb-4">Reset Password (Patient)</h2>
        <?php echo $message; ?>

        <?php if ($showForm): ?>
            <form method="POST" action="">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="mb-3">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6" maxlength="15" placeholder="Enter new password">
                </div>
                <div class="mb-3">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6" maxlength="15" placeholder="Confirm new password">
                </div>
                <button type="submit" name="reset_password" class="btn btn-primary w-100">Reset Password</button><p></p>
            </form>

                    <a href="patient_forgot_password.php" ><button type="submit" name="submit" class="btn btn-outline-secondary btn-back-home w-100">← Change Email</button></a>

        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
