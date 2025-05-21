<?php
session_start();
include '../includes/header.php';
include '../config/db.php';

$error_message = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Note: Consider hashing in production

    $sql = "SELECT * FROM users WHERE Username='$username' AND Password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['Role'];
        $_SESSION['fullname'] = $user['Fullname'];

        header("Location: ../views/dashboard.php");
        exit();
    } else {
        $error_message = 'Invalid credentials! Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dental Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1e3c72, #2a5298, #6dd5fa);
            background-size: 300% 300%;
            animation: gradientBackground 12s ease infinite;
        }

        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .error-message {
            color: #842029;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #f5c2c7;
        }

        h1, h4 {
            color: #1e3c72;
        }

        .btn-back-home {
            border: 1px solid #6c757d;
        }

        .login-card:hover {
            transform: translateY(-2px);
            transition: 0.3s ease;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="login-card text-center">
            <!-- Dental Hub Header -->
            <h1 class="fw-bold mb-1">Dental Hub</h1>
            <h4 class="mb-4 text-muted">Dental Management System</h4>

            <!-- Login Title -->
            <h5 class="mb-4 text-primary">Staff Login</h5>

            <!-- Error Display -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>

            <!-- Forgot Password -->
            <div class="mt-3">
                <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
            </div>

            <!-- Back Button -->
            <div class="mt-4">
                <a href="../index.php" class="btn btn-outline-secondary btn-back-home w-100">← Back to Home</a>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
