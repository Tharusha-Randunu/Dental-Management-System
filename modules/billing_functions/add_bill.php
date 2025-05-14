<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Initialize message
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get form data
    $nic = $_POST['nic'];
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'];
    $tax = $_POST['tax'];
    $grand_total = $_POST['grand_total'];
    $amount_paid = $_POST['amount_paid'];
    $amount_remaining = $_POST['amount_remaining'];
    $payment_status = $_POST['payment_status'];

    // Check if NIC exists in patients table
    $nicCheck = $conn->query("SELECT NIC FROM patients WHERE NIC = '$nic'");
    if ($nicCheck->num_rows == 0) {
        $message = "Error: NIC does not exist in the patients table.";
    } else {
        // Insert bill
        $sql = "INSERT INTO bills (NIC, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status) 
                VALUES ('$nic', '$total_amount', '$discount', '$tax', '$grand_total', '$amount_paid', '$amount_remaining', '$payment_status')";

        if ($conn->query($sql) === TRUE) {
            $message = "Bill added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add New Bill</h2>

        <?php if (!empty($message)) { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST" action="add_bill.php">
            <div class="mb-3">
                <label for="nic" class="form-label">Patient NIC</label>
                <input type="text" name="nic" id="nic" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Discount</label>
                <input type="number" step="0.01" name="discount" id="discount" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tax" class="form-label">Tax</label>
                <input type="number" step="0.01" name="tax" id="tax" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="grand_total" class="form-label">Grand Total</label>
                <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control" required readonly>
            </div>
            <div class="mb-3">
                <label for="amount_paid" class="form-label">Amount Paid</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="amount_remaining" class="form-label">Amount Remaining</label>
                <input type="number" step="0.01" name="amount_remaining" id="amount_remaining" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select name="payment_status" id="payment_status" class="form-control" required>
                    <option value="Paid">Paid</option>
                    <option value="Partially Paid">Partially Paid</option>
                    <option value="Unpaid">Unpaid</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="../billing_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success ms-2"><i class="bi bi-check-lg"></i> Add Bill</button>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateTotals() {
        const total = parseFloat(document.getElementById('total_amount').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const paid = parseFloat(document.getElementById('amount_paid').value) || 0;

        const grandTotal = total - discount + tax;
        document.getElementById('grand_total').value = grandTotal.toFixed(2);

        const remaining = grandTotal - paid;
        document.getElementById('amount_remaining').value = remaining.toFixed(2);
    }

    document.addEventListener('input', calculateTotals);
</script>

<?php include '../../includes/footer.php'; ?>
