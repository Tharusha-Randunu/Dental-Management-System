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
$redirectAfterUpdate = false;

// Fetch existing data
$sql = "SELECT Contact, Email, Username, Password, profile_picture FROM patients WHERE NIC = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nic);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password']; // no hashing

    // Profile picture handling
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $imgData = file_get_contents($_FILES['profile_picture']['tmp_name']);
        $profile_picture = $imgData;
    }

    if ($profile_picture !== null) {
        $update_sql = "UPDATE patients SET Contact = ?, Email = ?, Username = ?, Password = ?, profile_picture = ? WHERE NIC = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssbs", $contact, $email, $username, $password, $null, $nic);
        // For BLOB binding
        $update_stmt->send_long_data(4, $profile_picture);
    } else {
        $update_sql = "UPDATE patients SET Contact = ?, Email = ?, Username = ?, Password = ? WHERE NIC = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssss", $contact, $email, $username, $password, $nic);
    }

    if ($update_stmt->execute()) {
        $message = "<div class='alert alert-success'>Profile updated successfully.</div>";
        // Update values to reflect changes in form
        $patient['Contact'] = $contact;
        $patient['Email'] = $email;
        $patient['Username'] = $username;
        $patient['Password'] = $password;
        if ($profile_picture !== null) {
            $patient['profile_picture'] = $profile_picture;
        }
        $redirectAfterUpdate = true;
    } else {
        $message = "<div class='alert alert-danger'>Failed to update profile.</div>";
    }
    $update_stmt->close();
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary mb-4">Edit My Profile</h2>

        <?= $message ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3 text-center">
                <?php if (!empty($patient['profile_picture'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($patient['profile_picture']) ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; margin-bottom: 10px;">
                <?php else: ?>
                    <p>No profile picture uploaded.</p>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="profile_picture" class="form-label">Change Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label for="contact" class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact" id="contact" value="<?= htmlspecialchars($patient['Contact']) ?>" required minlength="10" maxlength="10">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($patient['Email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="<?= htmlspecialchars($patient['Username']) ?>" required minlength="6" maxlength="15">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" name="password" id="password" value="<?= htmlspecialchars($patient['Password']) ?>" required minlength="6" maxlength="15">
                <div class="form-text">Allowed file types: JPEG, JPG, PNG.</div>
            </div>

            <button type="submit" class="btn btn-success">Update Profile</button>
            <a href="patient_dashboard.php" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>

<?php if (!empty($redirectAfterUpdate)): ?>
<script>
    setTimeout(() => {
        window.location.href = 'patient_dashboard.php';
    }, 2000);
</script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>



                
