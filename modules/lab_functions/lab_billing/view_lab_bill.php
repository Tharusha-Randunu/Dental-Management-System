<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

$bill = null;
$message = null;

// Determine where the user came from
$redirect_from = isset($_GET['from']) ? $_GET['from'] : 'lab_billing_management'; // default

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $bill_id = intval($_GET['id']);

    // Fetch bill and patient info
    $stmt = $conn->prepare("
        SELECT b.*, p.Fullname 
        FROM lab_bills b 
        JOIN patients p ON b.patient_nic = p.NIC 
        WHERE b.bill_id = ?
    ");
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $bill = $result->fetch_assoc();

        // Fetch test types
        $test_stmt = $conn->prepare("
            SELECT t.test_name, t.cost 
            FROM lab_bill_items i 
            JOIN test_types t ON i.test_type_id = t.test_type_id 
            WHERE i.bill_id = ?
        ");
        $test_stmt->bind_param("i", $bill_id);
        $test_stmt->execute();
        $test_result = $test_stmt->get_result();

        $tests = [];
        while ($row = $test_result->fetch_assoc()) {
            $tests[] = $row;
        }
        $test_stmt->close();

    } else {
        $message = "Lab bill not found!";
    }

    $stmt->close();
} else {
    $message = "Bill ID is missing!";
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Lab Bill Details</h2>

        <?php if ($message) { ?>
            <div class="alert alert-warning">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <?php if ($bill) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="<?= $redirect_from === 'view_patient' 
                    ? '../../patient_functions/view_patient.php?nic=' . urlencode($bill['patient_nic']) 
                    : 'lab_billing_management.php'; ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <!-- Bill Info Table -->
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Bill ID</th><td><?= htmlspecialchars($bill['bill_id']); ?></td></tr>
                    <tr><th>Patient NIC</th><td><?= htmlspecialchars($bill['patient_nic']); ?></td></tr>
                    <tr><th>Patient Name</th><td><?= htmlspecialchars($bill['Fullname']); ?></td></tr>
                    <tr><th>Total Amount</th><td>Rs. <?= htmlspecialchars($bill['total_amount']); ?></td></tr>
                    <tr><th>Discount</th><td>Rs. <?= htmlspecialchars($bill['discount']); ?></td></tr>
                    <tr><th>Tax</th><td>Rs. <?= htmlspecialchars($bill['tax']); ?></td></tr>
                    <tr><th>Grand Total</th><td>Rs. <?= htmlspecialchars($bill['grand_total']); ?></td></tr>
                    <tr><th>Amount Paid</th><td>Rs. <?= htmlspecialchars($bill['amount_paid']); ?></td></tr>
                    <tr><th>Amount Remaining</th><td>Rs. <?= htmlspecialchars($bill['amount_remaining']); ?></td></tr>
                    <tr><th>Payment Status</th><td><?= htmlspecialchars($bill['payment_status']); ?></td></tr>
                    <tr><th>Created At</th><td><?= htmlspecialchars($bill['created_at']); ?></td></tr>
                    <tr>
                        <th>Test Types</th>
                        <td>
                            <ul class="mb-0">
                                <?php foreach ($tests as $test): ?>
                                    <li><?= htmlspecialchars($test['test_name']) ?> (Rs. <?= number_format($test['cost'], 2) ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Print Button -->
            <div class="d-flex justify-content-end mt-4">
                <a href="generate_lab_pdf.php?id=<?= urlencode($bill['bill_id']); ?>" target="_blank" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Bill
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
