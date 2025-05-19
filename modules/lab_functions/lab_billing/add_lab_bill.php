<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Fetch patients
$patients = $conn->query("SELECT NIC, Fullname FROM patients");

// Fetch test types
$testTypes = $conn->query("SELECT test_type_id, test_name, cost FROM test_types");
?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h3 class="text-primary text-center">Add Lab Bill</h3>
        <form action="insert_lab_bill.php" method="POST">
            <div class="mb-3">
                <label for="patient_nic" class="form-label">Patient NIC</label>
                <select name="patient_nic" class="form-select" required>
                    <option value="" disabled selected>Select patient</option>
                    <?php while ($row = $patients->fetch_assoc()): ?>
                        <option value="<?= $row['NIC'] ?>"><?= $row['NIC'] ?> - <?= $row['Fullname'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="test_types" class="form-label">Test Types</label>
                <select name="test_types[]" class="form-select" id="test_types" multiple required>
                    <?php while ($row = $testTypes->fetch_assoc()): ?>
                        <option value="<?= $row['test_type_id'] ?>" data-cost="<?= $row['cost'] ?>">
                            <?= $row['test_name'] ?> (Rs. <?= $row['cost'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="text" name="total_amount" id="total_amount" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="discount" class="form-label">Discount</label>
                <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="0">
            </div>

            <div class="mb-3">
                <label for="tax" class="form-label">Tax</label>
                <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="0">
            </div>

            <div class="mb-3">
                <label for="grand_total" class="form-label">Grand Total</label>
                <input type="text" name="grand_total" id="grand_total" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="amount_paid" class="form-label">Amount Paid</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" value="0">
            </div>

            <div class="mb-3">
                <label for="amount_remaining" class="form-label">Remaining Amount</label>
                <input type="text" name="amount_remaining" id="amount_remaining" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select name="payment_status" class="form-select" required>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Partially Paid">Partially Paid</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>


            <div class="text-end">
                <button type="submit" class="btn btn-success">Add Bill</button>
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
