<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Get the bill ID from the URL
$bill_id = $_GET['id'];

// Fetch bill details from the database
$sql = "SELECT bill_id, NIC, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status, created_at FROM bills WHERE bill_id = '$bill_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $bill = $result->fetch_assoc();
} else {
    $message = "Bill not found!";
}

// Handle form submission and save changes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nic = $_POST['nic'];
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'];
    $tax = $_POST['tax'];
    $grand_total = $_POST['grand_total'];
    $amount_paid = $_POST['amount_paid'];
    $amount_remaining = $_POST['amount_remaining'];
    $payment_status = $_POST['payment_status'];

    $update_sql = "UPDATE bills 
                   SET NIC = '$nic', 
                       total_amount = '$total_amount', 
                       discount = '$discount', 
                       tax = '$tax', 
                       grand_total = '$grand_total',
                       amount_paid = '$amount_paid',
                       amount_remaining = '$amount_remaining',
                       payment_status = '$payment_status' 
                   WHERE bill_id = '$bill_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                });
              </script>";
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit Bill Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning"><?php echo $message; ?></div>
        <?php } ?>

        <?php if ($result->num_rows > 0) { ?>
            <form method="POST" onsubmit="return calculateGrandTotal();">
                <div class="mb-3">
                    <label class="form-label">Bill ID</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($bill['bill_id']); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">NIC</label>
                    <input type="text" name="nic" class="form-control" value="<?php echo htmlspecialchars($bill['NIC']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Total Amount</label>
                    <input type="number" name="total_amount" class="form-control" id="total_amount" value="<?php echo htmlspecialchars($bill['total_amount']); ?>" step="0.01" required oninput="calculateGrandTotal();">
                </div>

                <div class="mb-3">
                    <label class="form-label">Discount</label>
                    <input type="number" name="discount" class="form-control" id="discount" value="<?php echo htmlspecialchars($bill['discount']); ?>" step="0.01" required oninput="calculateGrandTotal();">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tax</label>
                    <input type="number" name="tax" class="form-control" id="tax" value="<?php echo htmlspecialchars($bill['tax']); ?>" step="0.01" required oninput="calculateGrandTotal();">
                </div>

                <div class="mb-3">
                    <label class="form-label">Grand Total</label>
                    <input type="number" name="grand_total" class="form-control" id="grand_total" value="<?php echo htmlspecialchars($bill['grand_total']); ?>" step="0.01" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount Paid</label>
                    <input type="number" name="amount_paid" class="form-control" id="amount_paid" value="<?php echo htmlspecialchars($bill['amount_paid']); ?>" step="0.01" required oninput="calculateRemaining();">
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount Remaining</label>
                    <input type="number" name="amount_remaining" class="form-control" id="amount_remaining" value="<?php echo htmlspecialchars($bill['amount_remaining']); ?>" step="0.01" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select" required>
                        <option value="Paid" <?php echo ($bill['payment_status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                        <option value="Partially Paid" <?php echo ($bill['payment_status'] == 'Partially Paid') ? 'selected' : ''; ?>>Partially Paid</option>
                        <option value="Unpaid" <?php echo ($bill['payment_status'] == 'Unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../billing_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Changes have been successfully saved!</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateGrandTotal() {
        var totalAmount = parseFloat(document.getElementById("total_amount").value) || 0;
        var discount = parseFloat(document.getElementById("discount").value) || 0;
        var tax = parseFloat(document.getElementById("tax").value) || 0;

        var grandTotal = (totalAmount - discount) + tax;
        document.getElementById("grand_total").value = grandTotal.toFixed(2);

        calculateRemaining();
        return true;
    }

    function calculateRemaining() {
        var grandTotal = parseFloat(document.getElementById("grand_total").value) || 0;
        var amountPaid = parseFloat(document.getElementById("amount_paid").value) || 0;
        var remaining = grandTotal - amountPaid;
        document.getElementById("amount_remaining").value = remaining.toFixed(2);
    }
</script>

<?php include '../../includes/footer.php'; ?>
