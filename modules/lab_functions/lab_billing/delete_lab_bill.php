<?php
include '../../../config/db.php';

// Check if ID is set and numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Delete associated lab bill items
    $conn->query("DELETE FROM lab_bill_items WHERE bill_id = '$id'");

    // Delete main bill
    $sql = "DELETE FROM lab_bills WHERE bill_id = '$id'";
    if ($conn->query($sql) === TRUE) {
        // Return success response
        echo json_encode(['status' => 'success']);
    } else {
        // Return error response
        echo json_encode(['status' => 'error']);
    }
} else {
    // Return invalid ID response
    echo json_encode(['status' => 'invalid']);
}
?>
