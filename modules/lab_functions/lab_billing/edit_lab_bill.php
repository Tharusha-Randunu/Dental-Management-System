<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid Request'); window.location.href='lab_billing_management.php';</script>";
    exit();
}

$bill_id = $_GET['id'];

// Fetch bill details
$billQuery = $conn->prepare("SELECT * FROM lab_bills WHERE bill_id = ?");
$billQuery->bind_param("i", $bill_id);
$billQuery->execute();
$bill = $billQuery->get_result()->fetch_assoc();

if (!$bill) {
    echo "<script>alert('Bill not found'); window.location.href='lab_billing_management.php';</script>";
    exit();
}

// Fetch selected test types for this bill
$selectedTests = [];
$testsResult = $conn->query("SELECT test_type_id FROM lab_bill_items WHERE bill_id = $bill_id");
while ($row = $testsResult->fetch_assoc()) {
    $selectedTests[] = $row['test_type_id'];
}

// Fetch patients
$patients = $conn->query("SELECT NIC, Fullname FROM patients");

// Fetch test types
$testTypes = $conn->query("SELECT test_type_id, test_name, cost FROM test_types");
?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h3 class="text-primary text-center">Edit Lab Bill</h3>
        <form action="update_lab_bill.php" method="POST">
            <input type="hidden" name="bill_id" value="<?= $bill_id ?>">

            <div class="mb-3">
                <label for="patient_nic" class="form-label">Patient NIC</label>
                <select name="patient_nic" class="form-select" required>
                    <?php while ($row = $patients->fetch_assoc()): ?>
                        <option value="<?= $row['NIC'] ?>" <?= $row['NIC'] == $bill['patient_nic'] ? 'selected' : '' ?>>
                            <?= $row['NIC'] ?> - <?= $row['Fullname'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="test_types" class="form-label">Test Types</label>
                <select name="test_types[]" class="form-select" id="test_types" multiple required>
                    <?php while ($row = $testTypes->fetch_assoc()): ?>
                        <option value="<?= $row['test_type_id'] ?>" data-cost="<?= $row['cost'] ?>" <?= in_array($row['test_type_id'], $selectedTests) ? 'selected' : '' ?>>
                            <?= $row['test_name'] ?> (Rs. <?= $row['cost'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="text" name="total_amount" id="total_amount" class="form-control" value="<?= $bill['total_amount'] ?>" readonly>
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
                <a href="lab_billing_management.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const testTypesSelect = document.getElementById('test_types');
    const totalAmountInput = document.getElementById('total_amount');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const grandTotalInput = document.getElementById('grand_total');
    const amountPaidInput = document.getElementById('amount_paid');
    const amountRemainingInput = document.getElementById('amount_remaining');

    function calculateTotals() {
        let total = 0;
        const selectedOptions = Array.from(testTypesSelect.selectedOptions);
        selectedOptions.forEach(opt => {
            total += parseFloat(opt.dataset.cost || 0);
        });

        const discount = parseFloat(discountInput.value || 0);
        const tax = parseFloat(taxInput.value || 0);
        const grandTotal = total - discount + tax;
        const amountPaid = parseFloat(amountPaidInput.value || 0);
        const amountRemaining = grandTotal - amountPaid;

        totalAmountInput.value = total.toFixed(2);
        grandTotalInput.value = grandTotal.toFixed(2);
        amountRemainingInput.value = amountRemaining.toFixed(2);
    }

    testTypesSelect.addEventListener('change', calculateTotals);
    discountInput.addEventListener('input', calculateTotals);
    taxInput.addEventListener('input', calculateTotals);
    amountPaidInput.addEventListener('input', calculateTotals);
});
</script>

<?php include '../../../includes/footer.php'; ?>
