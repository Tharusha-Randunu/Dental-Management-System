<?php
session_start();
if (!isset($_SESSION['patient_nic'])) {
    header("Location: patient_login.php");
    exit();
}

include '../includes/header.php';
include '../config/db.php';

$nic = $_SESSION['patient_nic'];

// Fetch patient details
$sql = "SELECT NIC, Fullname, Address, Contact, Gender, Email, Username, profile_picture FROM patients WHERE NIC = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nic);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    $message = "Patient not found!";
}

// Fetch appointment history
$appointments = [];
$appt_sql = "SELECT appointment_id, appointment_date, appointment_time, status, created_at, updated_at, dentist_code, dentist_name 
             FROM appointments WHERE patient_nic = ? ORDER BY appointment_date DESC, appointment_time DESC";
$appt_stmt = $conn->prepare($appt_sql);
$appt_stmt->bind_param("s", $nic);
$appt_stmt->execute();
$appt_result = $appt_stmt->get_result();
while ($row = $appt_result->fetch_assoc()) {
    $appointments[] = $row;
}
$appt_stmt->close();

// Dental bills
$bills = [];
$bill_sql = "SELECT bill_id, appointment_id, appointment_date, notes, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status, created_at 
             FROM bills WHERE appointment_id IN 
             (SELECT appointment_id FROM appointments WHERE patient_nic = ?) 
             ORDER BY created_at DESC";
$bill_stmt = $conn->prepare($bill_sql);
$bill_stmt->bind_param("s", $nic);
$bill_stmt->execute();
$bill_result = $bill_stmt->get_result();
while ($row = $bill_result->fetch_assoc()) {
    $bills[] = $row;
}
$bill_stmt->close();

// Lab test results
$test_results = [];
$test_sql = "
    SELECT 
        tr.result_id, 
        tr.test_id, 
        tr.notes, 
        tt.test_name
    FROM test_results tr
    LEFT JOIN test_requests treq ON tr.test_id = treq.test_id
    LEFT JOIN test_types tt ON treq.test_type_id = tt.test_type_id
    WHERE tr.patient_nic = ?
    ORDER BY tr.result_id DESC
";
$test_stmt = $conn->prepare($test_sql);
$test_stmt->bind_param("s", $nic);
$test_stmt->execute();
$test_result = $test_stmt->get_result();
while ($row = $test_result->fetch_assoc()) {
    $test_results[] = $row;
}
$test_stmt->close();

// Lab bills
$lab_bills = [];
$lab_sql = "SELECT bill_id, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status, created_at 
            FROM lab_bills WHERE patient_nic = ? ORDER BY created_at DESC";
$lab_stmt = $conn->prepare($lab_sql);
$lab_stmt->bind_param("s", $nic);
$lab_stmt->execute();
$lab_result = $lab_stmt->get_result();
while ($row = $lab_result->fetch_assoc()) {
    $lab_bills[] = $row;
}
$lab_stmt->close();
?>

<div class="container mt-4">
   

    <div class="card shadow-lg p-4 mb-4">
        <h2 class="text-center text-primary">My Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php } else { ?>
            <!-- Patient Information Table -->
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Profile Picture</th>
                        <td>
                            <?php if (!empty($patient['profile_picture'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($patient['profile_picture']) ?>" alt="Profile Picture" style="max-width:150px; max-height:150px; object-fit:cover; border-radius: 8px;">
                            <?php else: ?>
                                <span>No picture available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th>NIC</th><td><?= htmlspecialchars($patient['NIC']) ?></td></tr>
                    <tr><th>Full Name</th><td><?= htmlspecialchars($patient['Fullname']) ?></td></tr>
                    <tr><th>Address</th><td><?= htmlspecialchars($patient['Address']) ?></td></tr>
                    <tr><th>Contact</th><td><?= htmlspecialchars($patient['Contact']) ?></td></tr>
                    <tr><th>Gender</th><td><?= htmlspecialchars($patient['Gender']) ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($patient['Email']) ?></td></tr>
                    <tr><th>Username</th><td><?= htmlspecialchars($patient['Username']) ?></td></tr>
                </tbody>
            </table>
             <div class="d-flex justify-content-end mb-3">
                <a href="edit_patient_profile.php" class="btn btn-warning me-2">Edit Profile</a>
                <a href="patient_logout.php" class="btn btn-danger">Logout</a>
            </div>
        <?php } ?>
    </div>

    <!-- Appointment History -->
    <div class="card shadow-lg p-4 mb-4">
        <h3 class="text-primary mb-3">My Appointment History</h3>
        <?php if (count($appointments) === 0) { ?>
            <div class="alert alert-info">No appointments found.</div>
        <?php } else { ?>
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Dentist Code</th>
                        <th>Dentist Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appt) : ?>
                        <tr>
                            <td><?= htmlspecialchars($appt['appointment_id']) ?></td>
                            <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                            <td><?= htmlspecialchars($appt['appointment_time']) ?></td>
                            <td><?= htmlspecialchars($appt['status']) ?></td>
                            <td><?= htmlspecialchars($appt['dentist_code']) ?></td>
                            <td><?= htmlspecialchars($appt['dentist_name']) ?></td>
                            <td><?= htmlspecialchars($appt['created_at']) ?></td>
                            <td><?= htmlspecialchars($appt['updated_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <!-- Dental Billing History -->
    <div class="card shadow-lg p-4 mb-4">
        <h3 class="text-primary mb-3">My Dental Billing History</h3>
        <?php if (count($bills) === 0) { ?>
            <div class="alert alert-info">No dental bills found.</div>
        <?php } else { ?>
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Bill ID</th>
                        <th>Appointment ID</th>
                        <th>Appointment Date</th>
                        <th>Notes</th>
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Tax</th>
                        <th>Grand Total</th>
                        <th>Amount Paid</th>
                        <th>Amount Remaining</th>
                        <th>Payment Status</th>
                        <th>Created At</th>
                        <th>View Bill</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bills as $bill) : ?>
                        <tr>
                            <td><?= htmlspecialchars($bill['bill_id']) ?></td>
                            <td><?= htmlspecialchars($bill['appointment_id']) ?></td>
                            <td><?= htmlspecialchars($bill['appointment_date']) ?></td>
                            <td style="word-break: break-word; max-width: 600px;"><?= htmlspecialchars($bill['notes']) ?></td>
                            <td>Rs. <?= number_format($bill['total_amount'], 2) ?></td>
                            <td>Rs. <?= number_format($bill['discount'], 2) ?></td>
                            <td>Rs. <?= number_format($bill['tax'], 2) ?></td>
                            <td>Rs. <?= number_format($bill['grand_total'], 2) ?></td>
                            <td>Rs. <?= number_format($bill['amount_paid'], 2) ?></td>
                            <td>Rs. <?= number_format($bill['amount_remaining'], 2) ?></td>
                            <td><?= htmlspecialchars($bill['payment_status']) ?></td>
                            <td><?= htmlspecialchars($bill['created_at']) ?></td>
                            <td><a href="../modules/billing_functions/view_bill.php?id=<?= urlencode($bill['bill_id']) ?>&from=patient_dashboard" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <!-- Lab Test Results -->
    <div class="card shadow-lg p-4 mb-4">
        <h3 class="text-primary mb-3">My Lab Test Results</h3>
        <?php if (count($test_results) === 0) { ?>
            <div class="alert alert-info">No lab test results found.</div>
        <?php } else { ?>
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Result ID</th>
                        <th>Test Request ID</th>
                        <th>Test Name</th>
                        <th>Notes</th>
                        <th>View Results</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($test_results as $test) : ?>
                        <tr>
                            <td><?= htmlspecialchars($test['result_id']) ?></td>
                            <td><?= htmlspecialchars($test['test_id']) ?></td>
                            <td><?= htmlspecialchars($test['test_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($test['notes']) ?></td>
                            <td><a href="../modules/lab_functions/test_results/view_test_result.php?id=<?= urlencode($test['result_id']) ?>&from=patient_dashboard" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <!-- Lab Billing History -->
    <div class="card shadow-lg p-4 mb-4">
        <h3 class="text-primary mb-3">My Lab Billing History</h3>
        <?php if (count($lab_bills) === 0) { ?>
            <div class="alert alert-info">No lab bills found.</div>
        <?php } else { ?>
            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Bill ID</th>
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Tax</th>
                        <th>Grand Total</th>
                        <th>Amount Paid</th>
                        <th>Amount Remaining</th>
                        <th>Payment Status</th>
                        <th>Created At</th>
                        <th>View Bill</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lab_bills as $lab_bill) : ?>
                        <tr>
                            <td><?= htmlspecialchars($lab_bill['bill_id']) ?></td>
                            <td>Rs. <?= number_format($lab_bill['total_amount'], 2) ?></td>
                            <td>Rs. <?= number_format($lab_bill['discount'], 2) ?></td>
                            <td>Rs. <?= number_format($lab_bill['tax'], 2) ?></td>
                            <td>Rs. <?= number_format($lab_bill['grand_total'], 2) ?></td>
                            <td>Rs. <?= number_format($lab_bill['amount_paid'], 2) ?></td>
                            <td>Rs. <?= number_format($lab_bill['amount_remaining'], 2) ?></td>
                            <td><?= htmlspecialchars($lab_bill['payment_status']) ?></td>
                            <td><?= htmlspecialchars($lab_bill['created_at']) ?></td>
                            <td><a href="../modules/lab_functions/lab_billing/view_lab_bill.php?id=<?= urlencode($lab_bill['bill_id']) ?>&from=patient_dashboard" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
         