<?php
include '../../includes/header.php';  // Corrected path to header.php
include '../../includes/sidebar.php'; // Corrected path to sidebar.php
include '../../config/db.php';        // Corrected path to db.php

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get form data
    $nic = $_POST['nic'];
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert patient into the database
    $sql = "INSERT INTO patients (NIC, Fullname, Address, Contact, Gender, Email, Username, Password) 
            VALUES ('$nic', '$fullname', '$address', '$contact', '$gender', '$email', '$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        $message = "Patient added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add New Patient</h2>

        <!-- Display success/error message -->
        <?php if (isset($message)) { ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <!-- Patient Add Form -->
        <form method="POST" action="add_patient.php">
            <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input type="text" name="nic" id="nic" class="form-control" required>
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
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="../patient_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <!-- Move the Add Patient button to the left side -->
                <button type="submit" class="btn btn-success ms-2"><i class="bi bi-check-lg"></i> Add Patient</button>
            </div>
        </form>
    </div>
</div>
  
        

<?php include '../../includes/footer.php';  // Corrected path to footer.php ?>
