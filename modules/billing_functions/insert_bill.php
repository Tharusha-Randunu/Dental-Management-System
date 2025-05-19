<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $appointment_date = $_POST['appointment_date'];
    $patient_nic = $_POST['patient_nic'];  // Note: not inserted in bills, can be removed here
    $notes = $_POST['notes'];
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'];
    $tax = $_POST['tax'];
    $grand_total = $_POST['grand_total'];
    $amount_paid = $_POST['amount_paid'];
    $amount_remaining = $_POST['amount_remaining'];
    $payment_status = $_POST['payment_status'];
    $created_at = $_POST['created_at'];

    // Check if bill for this appointment_id and appointment_date already exists
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM bills WHERE appointment_id = ? AND appointment_date = ?");
    $check_stmt->bind_param("is", $appointment_id, $appointment_date);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // Bill already exists for this appointment composite key
        header("Location: ../add_bill.php?error=duplicate");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO bills 
        (appointment_id, appointment_date, notes, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("issddddddss", $appointment_id, $appointment_date, $notes, $total_amount, $discount, $tax, $grand_total, $amount_paid, $amount_remaining, $payment_status, $created_at);

    if ($stmt->execute()) {
        // Success → Redirect with success flag
        header("Location: ../billing_management.php?success=1");
        exit();
    } else {
        // Failure → Redirect with error flag
        header("Location: add_bill.php?error=1");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
