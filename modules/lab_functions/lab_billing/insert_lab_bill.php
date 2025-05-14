<?php
include '../../../config/db.php';

// Get the data from the form
$patient_nic = $_POST['patient_nic'];
$total_amount = $_POST['total_amount'];
$discount = $_POST['discount'];
$tax = $_POST['tax'];
$grand_total = $_POST['grand_total'];
$amount_paid = $_POST['amount_paid'];
$amount_remaining = $_POST['amount_remaining'];
$payment_status = $_POST['payment_status'];
$created_at = $_POST['created_at']; // Date field
$test_types = $_POST['test_types']; // Array of selected test_type_ids

// Calculate amount_remaining
$amount_remaining = $grand_total - $amount_paid;

// Start a transaction
$conn->begin_transaction();

try {
    // Insert into lab_bills table (including amount_remaining)
    $sql = "INSERT INTO lab_bills (patient_nic, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare the statement and bind parameters (amount_remaining is calculated here)
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddddsss", $patient_nic, $total_amount, $discount, $tax, $grand_total, $amount_paid, $amount_remaining, $payment_status, $created_at);
    $stmt->execute();
    $bill_id = $stmt->insert_id; // Get the inserted bill ID

    // Insert selected test types into lab_bill_items table
    foreach ($test_types as $test_type_id) {
        $sql_item = "INSERT INTO lab_bill_items (bill_id, test_type_id) VALUES (?, ?)";
        $stmt_item = $conn->prepare($sql_item);
        $stmt_item->bind_param("ii", $bill_id, $test_type_id);
        $stmt_item->execute();
    }

    // Commit the transaction
    $conn->commit();
    header("Location: lab_billing_management.php?msg=success");
exit();
} catch (Exception $e) {
    // Rollback in case of an error
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?>
