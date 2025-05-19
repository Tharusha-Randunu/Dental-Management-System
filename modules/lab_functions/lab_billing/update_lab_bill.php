<?php
include '../../../config/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bill_id = $_POST['bill_id'];
    $patient_nic = $_POST['patient_nic'];
    $test_types = $_POST['test_types'];
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'];
    $tax = $_POST['tax'];
    $grand_total = $_POST['grand_total'];
    $amount_paid = $_POST['amount_paid'];
    $amount_remaining = $_POST['amount_remaining'];
    $payment_status = $_POST['payment_status'];

    // Update main lab bill
    $stmt = $conn->prepare("UPDATE lab_bills SET patient_nic=?, total_amount=?, discount=?, tax=?, grand_total=?, amount_paid=?, amount_remaining=?, payment_status=?, created_at=NOW() WHERE bill_id=?");
    $stmt->bind_param("sddddddsi", $patient_nic, $total_amount, $discount, $tax, $grand_total, $amount_paid, $amount_remaining, $payment_status, $bill_id);
    $stmt->execute();

    // Delete old test types
    $conn->query("DELETE FROM lab_bill_items WHERE bill_id = $bill_id");

    // Insert new test types
    $stmt2 = $conn->prepare("INSERT INTO lab_bill_items (bill_id, test_type_id) VALUES (?, ?)");
    foreach ($test_types as $test_type_id) {
        $stmt2->bind_param("ii", $bill_id, $test_type_id);
        $stmt2->execute();
    }

    // Redirect back to management page with a success flag
    header("Location: lab_billing_management.php?edit_success=1");
    exit();
}



?>
