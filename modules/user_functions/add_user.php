<?php
include '../../includes/header.php';  // Corrected path to header.php
include '../../includes/sidebar.php'; // Corrected path to sidebar.php
include '../../config/db.php';        // Corrected path to db.php

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get form data
    $nic = $_POST['nic'];
    $usercode = $_POST['usercode'];
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $role = $_POST['role'];  // Added role input
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert user into the database
    $sql = "INSERT INTO users (NIC,user_code, Fullname, Address, Contact, Gender, Email, Username, Role, Password) 
            VALUES ('$nic','$usercode', '$fullname', '$address', '$contact', '$gender', '$email', '$username', '$role', '$password')";

    if ($conn->query($sql) === TRUE) {
        $message = "User added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add New User</h2>

        <!-- Display success/error message -->
        <?php if (isset($message)) { ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <!-- User Add Form -->
        <form method="POST" action="add_user.php">
            <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input type="text" name="nic" id="nic" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="usercode" class="form-label">User Code</label>
                <input type="text" name="usercode" id="usercode" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" name="contact" id="contact" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="Admin">Admin</option>
                    <option value="Dentist">Dentist</option>
                    <option value="Receptionist">Receptionist</option>
                    <option value="Lab_Technician">Lab Technician</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
            <a href="../user_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Add User</button>
             </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php';  // Corrected path to footer.php ?>
