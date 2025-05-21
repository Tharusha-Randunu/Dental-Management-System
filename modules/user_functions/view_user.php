<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Check if the 'nic' parameter is set in the URL
if (isset($_GET['nic'])) {
    // Get the NIC from the URL
    $nic = $_GET['nic'];

    // Fetch user details from the database using the NIC
    $sql = "SELECT NIC, user_code, Fullname, Role, Address, Contact, Gender, Email, Username, Password, profile_picture FROM users WHERE NIC = '$nic'";
    $result = $conn->query($sql);

    // Check if the user is found
    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
    } else {
        $message = "User not found!";
    }
} else {
    $message = "NIC parameter is missing!";
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">User Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if (isset($user)) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="../user_management.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </div>

            <table class="table table-bordered text-center">
                <tbody>
                    <tr>
                        <th colspan="2">Profile Picture</th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" 
                                     alt="Profile Picture" 
                                     class="rounded shadow" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <span class="text-muted">No profile picture available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>NIC</th>
                        <td><?php echo htmlspecialchars($user['NIC']); ?></td>
                    </tr>
                    <tr>
                        <th>User Code</th>
                        <td><?php echo htmlspecialchars($user['user_code']); ?></td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td><?php echo htmlspecialchars($user['Fullname']); ?></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?php echo htmlspecialchars($user['Role']); ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?php echo htmlspecialchars($user['Address']); ?></td>
                    </tr>
                    <tr>
                        <th>Contact</th>
                        <td><?php echo htmlspecialchars($user['Contact']); ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?php echo htmlspecialchars($user['Gender']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($user['Email']); ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?php echo htmlspecialchars($user['Username']); ?></td>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <td><?php echo htmlspecialchars($user['Password']); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
