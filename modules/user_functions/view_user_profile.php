<?php
session_start();
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Check if user is logged in by username in session
if (!isset($_SESSION['username'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user details by username
$sql = "SELECT NIC, user_code, Fullname, Role, Address, Contact, Gender, Email, Username, Password, profile_picture FROM users WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $message = "User not found!";
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">My Profile</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if (isset($user)) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="/Dental_System/views/dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
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
