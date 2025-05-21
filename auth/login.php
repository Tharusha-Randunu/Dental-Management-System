
<?php
include '../includes/header.php';
include '../config/db.php'; // Adjusted to reflect the folder structure

$error_message = '';  // Variable to store the error message

if (isset($_POST['login'])) {
    // Get the username and password entered by the user
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Using plain text password (avoid this in production)

    // Query to fetch user details from the users table
    $sql = "SELECT * FROM users WHERE Username='$username' AND Password='$password'";
    $result = $conn->query($sql);

    // Check if the query returned a result
    if ($result->num_rows > 0) {
        // User is found, log them in
        $user = $result->fetch_assoc(); // Fetch user details
        $_SESSION['username'] = $username;  // Store the username in session
        $_SESSION['role'] = $user['Role'];  // Store the role in session (useful for role-based access)
        $_SESSION['fullname'] = $user['Fullname'];  // Store the fullname in session

        header("Location: ../views/dashboard.php");  // Redirect to the dashboard inside the views folder
        exit();
    } else {
        // Invalid credentials
        $error_message = 'Invalid credentials! Please try again.';  // Set error message
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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            width: 350px;
            padding: 20px;
        }
        .error-message {
            color: red;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        h4{
            color: #67a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Dental Management System Heading -->
            <div class="text-center mb-4">
                <h1 class="text-primary">Dental Hub </h1>
                <h4 >Dental Management System </h4>
            </div>
            
            <!-- Login Form -->
            <h2 class="text-center mb-4">Login</h2>
            
            <!-- Error Message Div -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
            <br>
            <a href="forgot_password.php">Forgot Password?</a>

            

        </div>
    </div>

<?php include '../includes/footer.php'; ?>

