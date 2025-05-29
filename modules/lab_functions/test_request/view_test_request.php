<?php
include '../../../includes/header.php';
include '../../../config/db.php';

// Get the test request ID from the URL
$test_id = $_GET['id'] ?? null;

if (!$test_id) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Test Request ID is missing!</div></div>";
    include '../../includes/footer.php';
    exit;
}

// Fetch test request details with joins
$sql = "
    SELECT 
        tr.test_id,
        tr.request_date,
        tr.status,
        tr.result_availability,
        tr.notes,
        tr.sample_collected_date,
        tr.result_delivery_date,

        p.NIC AS patient_nic,
        p.Fullname AS patient_name,

        d.NIC AS dentist_nic,
        d.Fullname AS dentist_name,
        d.user_code AS dentist_code,

        lt.NIC AS tech_nic,
        lt.Fullname AS tech_name,
        lt.user_code AS tech_code,

        tt.test_name

    FROM test_requests tr
    LEFT JOIN patients p ON tr.patient_nic = p.NIC
    LEFT JOIN users d ON tr.requested_by = d.NIC
    LEFT JOIN users lt ON tr.assigned_to = lt.NIC
    LEFT JOIN test_types tt ON tr.test_type_id = tt.test_type_id
    WHERE tr.test_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $request = $result->fetch_assoc();
} else {
    $message = "Test Request not found!";
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Test Request Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if ($result->num_rows > 0) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="test_request_management.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </div>

            <!-- Test Request Details Table -->
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Test ID</th>
                        <td><?php echo htmlspecialchars($request['test_id']); ?></td>
                    </tr>
                    <tr>
                        <th>Request Date</th>
                        <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                    </tr>
                    <tr>
                        <th>Patient NIC</th>
                        <td><?php echo htmlspecialchars($request['patient_nic']); ?></td>
                    </tr>
                    <tr>
                        <th>Patient Name</th>
                        <td><?php echo htmlspecialchars($request['patient_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Dentist</th>
                        <td>
                            <?php echo htmlspecialchars($request['dentist_code']) . " - " . htmlspecialchars($request['dentist_name']); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Lab Technician</th>
                        <td>
                            <?php echo htmlspecialchars($request['tech_code']) . " - " . htmlspecialchars($request['tech_name']); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Test Type</th>
                        <td><?php echo htmlspecialchars($request['test_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                    </tr>
                    <tr>
                        <th>Result Availability</th>
                        <td><?php echo htmlspecialchars($request['result_availability']); ?></td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td style="white-space: pre-wrap; word-break: break-word; max-width: 600px;"><?php echo htmlspecialchars($request['notes'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Sample Collected Date</th>
                        <td><?php echo htmlspecialchars($request['sample_collected_date'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Result Delivery Date</th>
                        <td><?php echo htmlspecialchars($request['result_delivery_date'] ?: 'N/A'); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
