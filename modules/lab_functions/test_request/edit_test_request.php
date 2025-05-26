<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Get the test ID from the URL
$test_id = $_GET['id'] ?? null;

if (!$test_id) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Test ID is missing!</div></div>";
    include '../../../includes/footer.php';
    exit;
}

// Fetch the existing test request details
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

        tt.test_name,
        tt.test_type_id

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
    echo "<div class='container mt-4'><div class='alert alert-danger'>Test Request not found!</div></div>";
    include '../../../includes/footer.php';
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $result_availability = $_POST['result_availability'];
    $notes = $_POST['notes'];
    $test_type_id = $_POST['test_type_id'];
    $sample_collected_date = $_POST['sample_collected_date'] ?: null;
    $result_delivery_date = $_POST['result_delivery_date'] ?: null;

    $update_sql = "
        UPDATE test_requests 
        SET status = ?, result_availability = ?, notes = ?, test_type_id = ?, sample_collected_date = ?, result_delivery_date = ?
        WHERE test_id = ?
    ";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssi", $status, $result_availability, $notes, $test_type_id, $sample_collected_date, $result_delivery_date, $test_id);

    if ($update_stmt->execute()) {
        $message = "Test request updated successfully!";
        // Refresh request data
        header("Location: edit_test_request.php?id=$test_id&updated=1");
        exit;
    } else {
        $message = "Error updating test request!";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit Test Request</h2>

        <?php if (isset($_GET['updated'])) { ?>
            <div class="alert alert-success">
                Test request updated successfully!
            </div>
        <?php } elseif (isset($message)) { ?>
            <div class="alert alert-warning"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="form-group mb-3">
                <label for="patient_name">Patient Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['patient_name']); ?>" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="dentist_name">Dentist</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['dentist_code']) . ' - ' . htmlspecialchars($request['dentist_name']); ?>" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="tech_name">Lab Technician</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['tech_code']) . ' - ' . htmlspecialchars($request['tech_name']); ?>" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="test_name">Test Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($request['test_name']); ?>" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="status">Status</label>
                <select class="form-control" name="status">
                    <option value="Pending" <?php if ($request['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="In Progress" <?php if ($request['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Completed" <?php if ($request['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="result_availability">Result Availability</label>
                <select class="form-control" name="result_availability">
                    <option value="Yes" <?php if ($request['result_availability'] == 'Yes') echo 'selected'; ?>>Yes</option>
                    <option value="No" <?php if ($request['result_availability'] == 'No') echo 'selected'; ?>>No</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="sample_collected_date">Sample Collected Date</label>
                <input type="date" class="form-control" name="sample_collected_date" value="<?php echo htmlspecialchars($request['sample_collected_date']); ?>">
            </div>

            <div class="form-group mb-3">
                <label for="result_delivery_date">Result Delivery Date</label>
                <input type="date" class="form-control" name="result_delivery_date" value="<?php echo htmlspecialchars($request['result_delivery_date']); ?>">
            </div>

            <div class="form-group mb-3">
                <label for="notes">Notes</label>
                <textarea class="form-control" name="notes" ><?php echo htmlspecialchars($request['notes']); ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="test_type">Test Type</label>
                <select class="form-control" name="test_type_id">
                    <?php
                    $test_types_sql = "SELECT test_type_id, test_name FROM test_types";
                    $test_types_result = $conn->query($test_types_sql);

                    while ($test_type = $test_types_result->fetch_assoc()) {
                        $selected = ($request['test_type_id'] == $test_type['test_type_id']) ? 'selected' : '';
                        echo "<option value='{$test_type['test_type_id']}' $selected>{$test_type['test_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="test_request_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-pencil"></i> Update Test Request</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
