<?php
include '../../includes/header.php';
include '../../config/db.php';

// Fetch appointment data
$appointments = $conn->query("
    SELECT a.appointment_id, a.appointment_date, a.patient_nic, a.patient_name, a.dentist_code, a.dentist_name
    FROM appointments a
    WHERE NOT EXISTS (
        SELECT 1 FROM bills b 
        WHERE b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
    )
");
?>


<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        Bill added successfully!
    </div>
<?php endif; ?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h3 class="text-primary text-center">Add Bill</h3>
        <form action="./insert_bill.php" method="POST">
            <div class="mb-3">
                <label for="appointment_select" class="form-label">Select Appointment</label>
                <select name="appointment_select" id="appointment_select" class="form-select" required>
                    <option value="" disabled selected>Select appointment</option>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                        <option 
                            value="<?= $row['appointment_id'] ?>|<?= $row['appointment_date'] ?>"
                            data-appointment-id="<?= $row['appointment_id'] ?>"
                            data-appointment-date="<?= $row['appointment_date'] ?>"
                            data-patient-nic="<?= $row['patient_nic'] ?>"
                            data-patient-name="<?= $row['patient_name'] ?>"
                            data-dentist-code="<?= $row['dentist_code'] ?>"
                            data-dentist-name="<?= $row['dentist_name'] ?>"
                        >
                            <?= $row['appointment_id'] ?> - <?= $row['appointment_date'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="appointment_id" class="form-label">Appointment ID</label>
                    <input type="text" name="appointment_id" id="appointment_id" class="form-control" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appointment_date" class="form-label">Appointment Date</label>
                    <input type="date" name="appointment_date" id="appointment_date" class="form-control" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="patient_nic" class="form-label">Patient NIC</label>
                    <input type="text" name="patient_nic" id="patient_nic" class="form-control" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="patient_name" class="form-label">Patient Name</label>
                    <input type="text" id="patient_name" class="form-control" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="dentist_code" class="form-label">Dentist Code</label>
                    <input type="text" id="dentist_code" class="form-control" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dentist_name" class="form-label">Dentist Name</label>
                    <input type="text" id="dentist_name" class="form-control" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" maxlength="500"></textarea>
            </div>

            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" required>
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
                <a href="../billing_management.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const appointmentSelect = document.getElementById('appointment_select');
    const appointmentIdInput = document.getElementById('appointment_id');
    const appointmentDateInput = document.getElementById('appointment_date');
    const patientNicInput = document.getElementById('patient_nic');
    const patientNameInput = document.getElementById('patient_name');
    const dentistCodeInput = document.getElementById('dentist_code');
    const dentistNameInput = document.getElementById('dentist_name');

    const totalAmountInput = document.getElementById('total_amount');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const grandTotalInput = document.getElementById('grand_total');
    const amountPaidInput = document.getElementById('amount_paid');
    const amountRemainingInput = document.getElementById('amount_remaining');

    appointmentSelect.addEventListener('change', function () {
        const selectedOption = this.selectedOptions[0];
        appointmentIdInput.value = selectedOption.dataset.appointmentId;
        appointmentDateInput.value = selectedOption.dataset.appointmentDate;
        patientNicInput.value = selectedOption.dataset.patientNic;
        patientNameInput.value = selectedOption.dataset.patientName;
        dentistCodeInput.value = selectedOption.dataset.dentistCode;
        dentistNameInput.value = selectedOption.dataset.dentistName;
    });

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
