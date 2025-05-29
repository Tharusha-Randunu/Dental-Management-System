<?php
include '../../includes/header.php';
include '../../config/db.php';

$message = ""; // Initialize message variable

// Fetch patients
$patients_result = $conn->query("SELECT NIC, Fullname FROM patients");
$patients = $patients_result->fetch_all(MYSQLI_ASSOC);

// Fetch dentists
$dentists_result = $conn->query("SELECT user_code, Fullname FROM users WHERE Role = 'Dentist'");
$dentists = $dentists_result->fetch_all(MYSQLI_ASSOC);

// Check if appointment_id and appointment_date are set in the URL
if (isset($_GET['id']) && isset($_GET['date'])) {
    $appointment_id = $_GET['id'];
    $appointment_date = $_GET['date'];

    // Fetch appointment details using composite key
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = ? AND appointment_date = ?");
    $stmt->bind_param("ss", $appointment_id, $appointment_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $appointment = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-danger'>Appointment not found!</div>";
    }
} else {
    $message = "<div class='alert alert-danger'>Invalid request!</div>";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_date = $_POST['appointment_date'];
    $new_time = $_POST['appointment_time'];
    $new_status = $_POST['status'];
    $patient_nic = $_POST['patient_nic'];
    $patient_name = $_POST['patient_name'];
    $dentist_code = $_POST['dentist_code'];
    $dentist_name = $_POST['dentist_name'];

    $update_stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ?, status = ?, patient_nic = ?, patient_name = ?, dentist_code = ?, dentist_name = ? WHERE appointment_id = ? AND appointment_date = ?");
    $update_stmt->bind_param("sssssssss", $new_date, $new_time, $new_status, $patient_nic, $patient_name, $dentist_code, $dentist_name, $appointment_id, $appointment_date);

    if ($update_stmt->execute()) {
        $message = "<div class='alert alert-success'>Appointment updated successfully!</div>";
        $appointment['appointment_date'] = $new_date;
        $appointment['appointment_time'] = $new_time;
        $appointment['status'] = $new_status;
        $appointment['patient_nic'] = $patient_nic;
        $appointment['patient_name'] = $patient_name;
        $appointment['dentist_code'] = $dentist_code;
        $appointment['dentist_name'] = $dentist_name;
    } else {
        $message = "<div class='alert alert-danger'>Error updating appointment. Please try again.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit Appointment</h2>

        <?= $message; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Appointment ID</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($appointment['appointment_id']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Patient (NIC - Name)</label>
                <select class="form-select" id="patient_select">
                    <option value="">-- Select Patient --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['NIC'] ?>|<?= $p['Fullname'] ?>" <?= ($appointment['patient_nic'] === $p['NIC']) ? 'selected' : '' ?>>
                            <?= $p['NIC'] . " - " . $p['Fullname'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Patient NIC</label>
                <input type="text" name="patient_nic" id="patient_nic" class="form-control" value="<?= htmlspecialchars($appointment['patient_nic']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Patient Name</label>
                <input type="text" name="patient_name" id="patient_name" class="form-control" value="<?= htmlspecialchars($appointment['patient_name']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Dentist (Code - Name)</label>
                <select class="form-select" id="dentist_select">
                    <option value="">-- Select Dentist --</option>
                    <?php foreach ($dentists as $d): ?>
                        <option value="<?= $d['user_code'] ?>|<?= $d['Fullname'] ?>" <?= ($appointment['dentist_code'] === $d['user_code']) ? 'selected' : '' ?>>
                            <?= $d['user_code'] . " - " . $d['Fullname'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Dentist Code</label>
                <input type="text" name="dentist_code" id="dentist_code" class="form-control" value="<?= htmlspecialchars($appointment['dentist_code']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Dentist Name</label>
                <input type="text" name="dentist_name" id="dentist_name" class="form-control" value="<?= htmlspecialchars($appointment['dentist_name']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Appointment Date</label>
                <input type="date" name="appointment_date" class="form-control" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Appointment Time</label>
                <input type="time" name="appointment_time" class="form-control" value="<?= htmlspecialchars($appointment['appointment_time']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Scheduled" <?= ($appointment['status'] == "Scheduled") ? "selected" : "" ?>>Scheduled</option>
                    <option value="Completed" <?= ($appointment['status'] == "Completed") ? "selected" : "" ?>>Completed</option>
                    <option value="Cancelled" <?= ($appointment['status'] == "Cancelled") ? "selected" : "" ?>>Cancelled</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="../appointment_scheduling.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("patient_select").addEventListener("change", function () {
        const selected = this.value.split('|');
        document.getElementById("patient_nic").value = selected[0] || "";
        document.getElementById("patient_name").value = selected[1] || "";
    });

    document.getElementById("dentist_select").addEventListener("change", function () {
        const selected = this.value.split('|');
        document.getElementById("dentist_code").value = selected[0] || "";
        document.getElementById("dentist_name").value = selected[1] || "";
    });
</script>

<?php include '../../includes/footer.php'; ?>
