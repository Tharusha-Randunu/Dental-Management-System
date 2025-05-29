<?php
include '../../includes/header.php';
include '../../config/db.php';

// Get bill ID from query parameter
if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Bill ID is missing.</div>";
    exit;
}

$bill_id = $_GET['id'];

// Fetch bill details
$stmt = $conn->prepare("
    SELECT b.*, 
           a.patient_nic, a.patient_name, a.dentist_code, a.dentist_name
    FROM bills b
    JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
    WHERE b.bill_id = ?
");
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$result = $stmt->get_result();
$bill = $result->fetch_assoc();

if (!$bill) {
    echo "<div class='alert alert-danger'>Bill not found.</div>";
    exit;
}
?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h3 class="text-primary text-center">Edit Bill</h3>
        <form action="update_bill.php" method="POST">
            <input type="hidden" name="bill_id" value="<?= $bill['bill_id'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="appointment_id" class="form-label">Appointment ID</label>
                    <input type="text" name="appointment_id" class="form-control" value="<?= $bill['appointment_id'] ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appointment_date" class="form-label">Appointment Date</label>
                    <input type="date" name="appointment_date" class="form-control" value="<?= $bill['appointment_date'] ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Patient NIC</label>
                    <input type="text" class="form-control" value="<?= $bill['patient_nic'] ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Patient Name</label>
                    <input type="text" class="form-control" value="<?= $bill['patient_name'] ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Dentist Code</label>
                    <input type="text" class="form-control" value="<?= $bill['dentist_code'] ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Dentist Name</label>
                    <input type="text" class="form-control" value="<?= $bill['dentist_name'] ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" maxlength='500'><?= htmlspecialchars($bill['notes']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="<?= $bill['total_amount'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="discount" class="form-label">Discount</label>
                <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="<?= $bill['discount'] ?>">
            </div>

            <div class="mb-3">
                <label for="tax" class="form-label">Tax</label>
                <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="<?= $bill['tax'] ?>">
            </div>

            <div class="mb-3">
                <label for="grand_total" class="form-label">Grand Total</label>
                <input type="text" name="grand_total" id="grand_total" class="form-control" value="<?= $bill['grand_total'] ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="amount_paid" class="form-label">Amount Paid</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" value="<?= $bill['amount_paid'] ?>">
            </div>

            <div class="mb-3">
                <label for="amount_remaining" class="form-label">Remaining Amount</label>
                <input type="text" name="amount_remaining" id="amount_remaining" class="form-control" value="<?= $bill['amount_remaining'] ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select name="payment_status" class="form-select" required>
                    <option value="Unpaid" <?= $bill['payment_status'] == 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="Partially Paid" <?= $bill['payment_status'] == 'Partially Paid' ? 'selected' : '' ?>>Partially Paid</option>
                    <option value="Paid" <?= $bill['payment_status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Update Bill</button>
                <a href="../billing_management.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalAmountInput = document.getElementById('total_amount');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const grandTotalInput = document.getElementById('grand_total');
    const amountPaidInput = document.getElementById('amount_paid');
    const amountRemainingInput = document.getElementById('amount_remaining');

    function calculateTotals() {
        const total = parseFloat(totalAmountInput.value || 0);
        const discount = parseFloat(discountInput.value || 0);
        const tax = parseFloat(taxInput.value || 0);
        const paid = parseFloat(amountPaidInput.value || 0);

        const grandTotal = total - discount + tax;
        const remaining = grandTotal - paid;

        grandTotalInput.value = grandTotal.toFixed(2);
        amountRemainingInput.value = remaining.toFixed(2);
    }

    totalAmountInput.addEventListener('input', calculateTotals);
    discountInput.addEventListener('input', calculateTotals);
    taxInput.addEventListener('input', calculateTotals);
    amountPaidInput.addEventListener('input', calculateTotals);
});
</script>

<?php include '../../includes/footer.php'; ?>
