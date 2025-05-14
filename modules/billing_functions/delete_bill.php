<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Fetch bill details by bill ID
if (isset($_GET['id'])) {
    $bill_id = $_GET['id'];

    // Delete bill from the database
    $sql = "DELETE FROM bills WHERE bill_id = '$bill_id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back with a success parameter
        header("Location: ../billing_management.php?delete=success");
        exit;
    } else {
        // Redirect back with an error parameter
        header("Location: ../billing_management.php?delete=error");
        exit;
    }
} else {
    // If bill ID is not found in the URL, redirect with an error
    header("Location: ../billing_management.php?delete=notfound");
    exit;
}
?>

<?php include '../../includes/footer.php'; ?>
