<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

$nic = $_GET['nic'];

// Fetch user details (added Role column)
$sql = "SELECT NIC, user_code, Fullname, Address, Contact, Gender, Email, Username, Password, Role FROM users WHERE NIC = '$nic'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $message = "User not found!";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usercode = $_POST['usercode'];
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // ✅ New role field

    // Update user including Role
    $update_sql = "UPDATE users SET user_code ='$usercode', Fullname = '$fullname', Address = '$address', Contact = '$contact', Gender = '$gender', Email = '$email', Username = '$username', Password = '$password', Role = '$role' WHERE NIC = '$nic'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                });
              </script>";
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit User Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning"><?php echo $message; ?></div>
        <?php } ?>

        <?php if ($result->num_rows > 0) { ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">NIC</label>
                    <input type="text" class="form-control" value="<?php echo $user['NIC']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">User Code</label>
                    <input type="text" name="usercode" class="form-control" value="<?php echo $user['user_code']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" value="<?php echo $user['Fullname']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo $user['Address']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control" value="<?php echo $user['Contact']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="Male" <?php echo $user['Gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $user['Gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $user['Gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $user['Email']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $user['Username']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo $user['Password']; ?>" required>
                </div>
                <!-- ✅ Role Dropdown -->
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="Admin" <?php echo $user['Role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="Dentist" <?php echo $user['Role'] == 'Dentist' ? 'selected' : ''; ?>>Dentist</option>
                        <option value="Receptionist" <?php echo $user['Role'] == 'Receptionist' ? 'selected' : ''; ?>>Receptionist</option>
                        <option value="Lab_Technician" <?php echo $user['Role'] == 'Lab_Technician' ? 'selected' : ''; ?>>Lab Technician</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../user_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Changes have been successfully saved!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
