<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<div class='alert alert-danger'>Invalid request method.</div>";
    exit;
}

// Validate and sanitize POST data
$bill_id = intval($_POST['bill_id']);
$appointment_id = intval($_POST['appointment_id']);
$appointment_date = $_POST['appointment_date'];
$notes = trim($_POST['notes']);
$total_amount = floatval($_POST['total_amount']);
$discount = floatval($_POST['discount']);
$tax = floatval($_POST['tax']);
$grand_total = floatval($_POST['grand_total']);
$amount_paid = floatval($_POST['amount_paid']);
$amount_remaining = floatval($_POST['amount_remaining']);
$payment_status = $_POST['payment_status'];

// Validate required fields
if (!$bill_id || !$appointment_id || !$appointment_date || !$payment_status) {
    echo "<div class='alert alert-danger'>Missing required fields.</div>";
    exit;
}

// Update bill in the database
$stmt = $conn->prepare("
    UPDATE bills 
    SET 
        notes = ?, 
        total_amount = ?, 
        discount = ?, 
        tax = ?, 
        grand_total = ?, 
        amount_paid = ?, 
        amount_remaining = ?, 
        payment_status = ?
    WHERE bill_id = ?
");

$stmt->bind_param(
    "sddddddsi",
    $notes,
    $total_amount,
    $discount,
    $tax,
    $grand_total,
    $amount_paid,
    $amount_remaining,
    $payment_status,
    $bill_id
);

if ($stmt->execute()) {
    // Redirect to billing_management with an update success message
    header("Location: ../billing_management.php?updated=1");
    exit();
} else {
    // Redirect with update error
    header("Location: ../billing_management.php?update_error=1");
    exit();
}

$stmt->close();
$conn->close();
?>
