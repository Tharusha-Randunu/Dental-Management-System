<?php
include '../../includes/header.php';
include '../../config/db.php';

// Fetch user details by NIC
if (isset($_GET['nic'])) {
    $nic = $_GET['nic'];

    // Delete user from the database
    $sql = "DELETE FROM users WHERE NIC = '$nic'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back with a success parameter
        header("Location: ../user_management.php?delete=success");
        exit;
    } else {
        // Redirect back with an error parameter
        header("Location: ../user_management.php?delete=error");
        exit;
    }
} else {
    // If NIC is not found in the URL, redirect with an error
    header("Location: ../user_management.php?delete=notfound");
    exit;
}
?>
<?php include '../../includes/footer.php'; ?>
