<?php
include '../../../config/db.php';

// Check if test_id is set and numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Delete test request from the database
    $sql = "DELETE FROM test_requests WHERE test_id = '$id'";

    if ($conn->query($sql) === TRUE) {
        // Return success response
        echo json_encode(['status' => 'success']);
    } else {
        // Return error response
        echo json_encode(['status' => 'error']);
    }
} else {
    // Return error response for invalid or missing ID
    echo json_encode(['status' => 'error']);
}
?>
