<?php
include '../../../includes/header.php';
include '../../../config/db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_nic = $_POST['patient_nic'];
    $requested_by = $_POST['requested_by'];  
    $assigned_to = $_POST['assigned_to'];    
    $test_type_id = $_POST['test_type_id'];
    $status = $_POST['status'];
    $result_availability = $_POST['result_availability'];
    $notes = $_POST['notes'] ?? null;
    $sample_collected_date = $_POST['sample_collected_date'] ?? null;
    $result_delivery_date = $_POST['result_delivery_date'] ?? null;
    $request_date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO test_requests 
        (patient_nic, requested_by, assigned_to, test_type_id, request_date, status, result_availability, notes, sample_collected_date, result_delivery_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissssss", $patient_nic, $requested_by, $assigned_to, $test_type_id, $request_date, $status, $result_availability, $notes, $sample_collected_date, $result_delivery_date);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Test request added successfully.</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Fetch data
$patients = $conn->query("SELECT NIC, Fullname FROM patients");
$dentists = $conn->query("SELECT NIC, user_code, Fullname, role FROM users WHERE role = 'Dentist'");
$labTechs = $conn->query("SELECT NIC, user_code, Fullname, role FROM users WHERE role = 'Lab_Technician'");
$testTypes = $conn->query("SELECT test_type_id, test_name FROM test_types");
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary mb-4">Add New Test Request</h2>

        <form action="" method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="patient_nic" class="form-label">Patient NIC</label>
                    <select name="patient_nic" id="patient_nic" class="form-select" required>
                        <option value="">Select Patient NIC</option>
                        <?php while ($row = $patients->fetch_assoc()): ?>
                            <option value="<?= $row['NIC'] ?>" data-name="<?= $row['Fullname'] ?>">
                                <?= $row['NIC'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="patient_name" class="form-label">Patient Name</label>
                    <input type="text" id="patient_name" class="form-control" disabled>
                </div>
            </div>

            <!-- Dentist Selection -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="requested_by" class="form-label">Requested By (Dentist - user_code)</label>
                    <select name="requested_by" id="requested_by" class="form-select" required>
                        <option value="">Select Dentist</option>
                        <?php while ($row = $dentists->fetch_assoc()): ?>
                            <option value="<?= $row['NIC'] ?>" 
                                data-name="<?= $row['Fullname'] ?>" 
                                data-role="<?= $row['role'] ?>">
                                <?= $row['user_code'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="dentist_name" class="form-label">Dentist Name</label>
                    <input type="text" id="dentist_name" class="form-control" disabled>
                </div>
                <div class="col-md-4">
                    <label for="dentist_role" class="form-label">Dentist Role</label>
                    <input type="text" id="dentist_role" class="form-control" disabled>
                </div>
            </div>

            <!-- Lab Technician Selection -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="assigned_to" class="form-label">Assigned To (Lab Tech - user_code)</label>
                    <select name="assigned_to" id="assigned_to" class="form-select" required>
                        <option value="">Select Lab Technician user_code</option>
                        <?php while ($row = $labTechs->fetch_assoc()): ?>
                            <option value="<?= $row['NIC'] ?>" 
                                data-name="<?= $row['Fullname'] ?>" 
                                data-role="<?= $row['role'] ?>">
                                <?= $row['user_code'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tech_name" class="form-label">Technician Name</label>
                    <input type="text" id="tech_name" class="form-control" disabled>
                </div>
                <div class="col-md-4">
                    <label for="tech_role" class="form-label">Technician Role</label>
                    <input type="text" id="tech_role" class="form-control" disabled>
                </div>
            </div>

            <div class="mb-3">
                <label for="test_type_id" class="form-label">Test Type</label>
                <select name="test_type_id" id="test_type_id" class="form-select" required>
                    <option value="">Select Test Type</option>
                    <?php while ($row = $testTypes->fetch_assoc()): ?>
                        <option value="<?= $row['test_type_id'] ?>"><?= $row['test_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="result_availability" class="form-label">Result Availability</label>
                    <select name="result_availability" class="form-select" required>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="sample_collected_date" class="form-label">Sample Collected Date</label>
                    <input type="date" name="sample_collected_date" id="sample_collected_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="result_delivery_date" class="form-label">Result Delivery Date</label>
                    <input type="date" name="result_delivery_date" id="result_delivery_date" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" ></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="test_request_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- JS for auto-fill -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#patient_nic').on('change', function () {
        $('#patient_name').val($(this).find(':selected').data('name') || '');
    });

    $('#requested_by').on('change', function () {
        let option = $(this).find(':selected');
        $('#dentist_name').val(option.data('name') || '');
        $('#dentist_role').val(option.data('role') || '');
    });

    $('#assigned_to').on('change', function () {
        let option = $(this).find(':selected');
        $('#tech_name').val(option.data('name') || '');
        $('#tech_role').val(option.data('role') || '');
    });
</script>

<?php include '../../../includes/footer.php'; ?>
