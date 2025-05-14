<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Check if item ID is provided
if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // Delete inventory item from the database
    $sql = "DELETE FROM inventory WHERE item_id = '$item_id'";

    if ($conn->query($sql) === TRUE) {
        // Redirect with success message
        header("Location: ../inventory_management.php?delete=success");
        exit;
    } else {
        // Redirect with error message
        header("Location: ../inventory_management.php?delete=error");
        exit;
    }
} else {
    // Redirect if item ID is missing
    header("Location: ../inventory_management.php?delete=notfound");
    exit;
}
?>

<?php include '../../includes/footer.php'; ?>
