<?php
include '../../includes/header.php';  
include '../../includes/sidebar.php'; 
include '../../config/db.php';        

// Initialize variables
$bill = null;
$message = null;

// Check if bill_id is passed
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $bill_id = $_GET['id'];

    // Prepare a secure query to prevent SQL injection
    $stmt = $conn->prepare("SELECT bill_id, NIC, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status, created_at FROM bills WHERE bill_id = ?");
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if bill exists
    if ($result->num_rows > 0) {
        $bill = $result->fetch_assoc();
    } else {
        $message = "Bill not found!";
    }

    $stmt->close();
} else {
    $message = "Bill ID is missing!";
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Bill Details</h2>

        <?php if ($message) { ?>
            <div class="alert alert-warning">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <?php if ($bill) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="../billing_management.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <!-- Bill Information -->
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Bill ID</th><td><?php echo htmlspecialchars($bill['bill_id']); ?></td></tr>
                    <tr><th>NIC</th><td><?php echo htmlspecialchars($bill['NIC']); ?></td></tr>
                    <tr><th>Total Amount</th><td><?php echo htmlspecialchars($bill['total_amount']); ?></td></tr>
                    <tr><th>Discount</th><td><?php echo htmlspecialchars($bill['discount']); ?></td></tr>
                    <tr><th>Tax</th><td><?php echo htmlspecialchars($bill['tax']); ?></td></tr>
                    <tr><th>Grand Total</th><td><?php echo htmlspecialchars($bill['grand_total']); ?></td></tr>
                    <tr><th>Amount Paid</th><td><?php echo htmlspecialchars($bill['amount_paid']); ?></td></tr>
                    <tr><th>Amount Remaining</th><td><?php echo htmlspecialchars($bill['amount_remaining']); ?></td></tr>
                    <tr><th>Payment Status</th><td><?php echo htmlspecialchars($bill['payment_status']); ?></td></tr>
                    <tr><th>Created At</th><td><?php echo htmlspecialchars($bill['created_at']); ?></td></tr>
                </tbody>
            </table>

            <!-- Print Button -->
            <div class="d-flex justify-content-end mt-4">
                <a href="generate_pdf.php?id=<?php echo urlencode($bill['bill_id']); ?>" target="_blank" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Bill
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
