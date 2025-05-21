<?php
session_start();
if (!isset($_SESSION['patient_nic'])) {
    header("Location: patient_login.php");
    exit();
}

include '../includes/header.php';
include '../includes/sidebar.php';
include '../config/db.php';

$nic = $_SESSION['patient_nic'];
$message = '';

// Fetch existing data
$sql = "SELECT Contact, Email, Username, Password FROM patients WHERE NIC = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nic);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password']; // no hashing

    $update_sql = "UPDATE patients SET Contact = ?, Email = ?, Username = ?, Password = ? WHERE NIC = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssss", $contact, $email, $username, $password, $nic);

    if ($update_stmt->execute()) {
        $message = "<div class='alert alert-success'>Profile updated successfully.</div>";
        // Update values to reflect changes in form
        $patient['Contact'] = $contact;
        $patient['Email'] = $email;
        $patient['Username'] = $username;
        $patient['Password'] = $password;
    } else {
        $message = "<div class='alert alert-danger'>Failed to update profile.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary mb-4">Edit My Profile</h2>

        <?= $message ?>

        <form method="post">
            <div class="mb-3">
                <label for="contact" class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact" id="contact" value="<?= htmlspecialchars($patient['Contact']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($patient['Email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="<?= htmlspecialchars($patient['Username']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" name="password" id="password" value="<?= htmlspecialchars($patient['Password']) ?>" required>
            </div>

            <button type="submit" class="btn btn-success">Update Profile</button>
            <a href="patient_dashboard.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
