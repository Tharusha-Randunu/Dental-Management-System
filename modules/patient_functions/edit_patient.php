<?php
include '../../includes/header.php';  // Corrected path to header.php
include '../../includes/sidebar.php'; // Corrected path to sidebar.php
include '../../config/db.php';        // Corrected path to db.php

// Get the patient NIC from the URL
$nic = $_GET['nic'];

// Fetch patient details from the database
$sql = "SELECT NIC, Fullname, Address, Contact, Gender, Email, Username, Password FROM patients WHERE NIC = '$nic'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the patient data
    $patient = $result->fetch_assoc();
} else {
    // Patient not found
    $message = "Patient not found!";
}

// Handle form submission and save changes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated form data
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Update the patient information in the database
    $update_sql = "UPDATE patients SET Fullname = '$fullname', Address = '$address', Contact = '$contact', Gender = '$gender', Email = '$email', Username = '$username', Password = '$password' WHERE NIC = '$nic'";

    if ($conn->query($update_sql) === TRUE) {
        // Patient successfully updated, show the success modal
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
        <h2 class="text-center text-primary">Edit Patient Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if ($result->num_rows > 0) { ?>
            <form method="POST">
                <!-- Patient Information -->
                <div class="mb-3">
                    <label class="form-label">NIC</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($patient['NIC']); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($patient['Fullname']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($patient['Address']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($patient['Contact']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="Male" <?php echo ($patient['Gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($patient['Gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($patient['Gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($patient['Email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($patient['Username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars($patient['Password']); ?>" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../patient_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Cancel</a>
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
