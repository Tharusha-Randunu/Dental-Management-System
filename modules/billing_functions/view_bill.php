<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

$bill = null;
$message = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $bill_id = intval($_GET['id']);

    // Fetch bill and appointment info
    $stmt = $conn->prepare("
        SELECT b.*, a.patient_nic, a.patient_name, a.dentist_code, a.dentist_name 
        FROM bills b 
        JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date 
        WHERE b.bill_id = ?
    ");
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $bill = $result->fetch_assoc();
    } else {
        $message = "Dental bill not found!";
    }

    $stmt->close();
} else {
    $message = "Bill ID is missing!";
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Dental Bill Details</h2>

        <?php if ($message) { ?>
            <div class="alert alert-warning">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <?php if ($bill) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="../billing_management.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <!-- Bill Info Table -->
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Bill ID</th><td><?= htmlspecialchars($bill['bill_id']); ?></td></tr>
                    <tr><th>Appointment ID</th><td><?= htmlspecialchars($bill['appointment_id']); ?></td></tr>
                    <tr><th>Appointment Date</th><td><?= htmlspecialchars($bill['appointment_date']); ?></td></tr>
                    <tr><th>Patient NIC</th><td><?= htmlspecialchars($bill['patient_nic']); ?></td></tr>
                    <tr><th>Patient Name</th><td><?= htmlspecialchars($bill['patient_name']); ?></td></tr>
                    <tr><th>Dentist Code</th><td><?= htmlspecialchars($bill['dentist_code']); ?></td></tr>
                    <tr><th>Dentist Name</th><td><?= htmlspecialchars($bill['dentist_name']); ?></td></tr>
                    <tr><th>Notes</th><td><?= nl2br(htmlspecialchars($bill['notes'])); ?></td></tr>
                    <tr><th>Total Amount</th><td>Rs. <?= number_format($bill['total_amount'], 2); ?></td></tr>
                    <tr><th>Discount</th><td>Rs. <?= number_format($bill['discount'], 2); ?></td></tr>
                    <tr><th>Tax</th><td>Rs. <?= number_format($bill['tax'], 2); ?></td></tr>
                    <tr><th>Grand Total</th><td>Rs. <?= number_format($bill['grand_total'], 2); ?></td></tr>
                    <tr><th>Amount Paid</th><td>Rs. <?= number_format($bill['amount_paid'], 2); ?></td></tr>
                    <tr><th>Amount Remaining</th><td>Rs. <?= number_format($bill['amount_remaining'], 2); ?></td></tr>
                    <tr><th>Payment Status</th><td><?= htmlspecialchars($bill['payment_status']); ?></td></tr>
                    <tr><th>Created At</th><td><?= htmlspecialchars($bill['created_at']); ?></td></tr>
                </tbody>
            </table>

            <!-- Print Button -->
            <div class="d-flex justify-content-end mt-4">
                <a href="generate_pdf.php?id=<?= urlencode($bill['bill_id']); ?>" target="_blank" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Bill
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
