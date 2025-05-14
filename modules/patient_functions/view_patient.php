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
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Patient Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if ($result->num_rows > 0) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="../patient_management.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </div>

            <!-- Patient Information Table -->
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>NIC</th>
                        <td><?php echo htmlspecialchars($patient['NIC']); ?></td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td><?php echo htmlspecialchars($patient['Fullname']); ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?php echo htmlspecialchars($patient['Address']); ?></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td><?php echo htmlspecialchars($patient['Contact']); ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?php echo htmlspecialchars($patient['Gender']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($patient['Email']); ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?php echo htmlspecialchars($patient['Username']); ?></td>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <td><?php echo htmlspecialchars($patient['Password']); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<?php include '../../includes/footer.php';  // Corrected path to footer.php ?>
