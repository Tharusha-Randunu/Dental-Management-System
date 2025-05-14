<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

$errors = [];
$success = "";

// Fetch patients
$patients = [];
$patient_sql = "SELECT NIC, Fullname FROM patients";
$patient_result = $conn->query($patient_sql);
if ($patient_result && $patient_result->num_rows > 0) {
    while ($row = $patient_result->fetch_assoc()) {
        $patients[] = $row;
    }
}

// Fetch dentists
$dentists = [];
$dentist_sql = "SELECT user_code, Fullname FROM users WHERE Role = 'Dentist'";
$dentist_result = $conn->query($dentist_sql);
if ($dentist_result && $dentist_result->num_rows > 0) {
    while ($row = $dentist_result->fetch_assoc()) {
        $dentists[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = trim($_POST['appointment_id']);
    $appointment_date = trim($_POST['appointment_date']);
    $appointment_time = trim($_POST['appointment_time']);
    $patient_nic = trim($_POST['patient_nic']);
    $patient_name = trim($_POST['patient_name']);
    $dentist_code = trim($_POST['dentist_code']);
    $dentist_name = trim($_POST['dentist_name']);
    $status = trim($_POST['status']);

    // Validation
    if (empty($appointment_id) || empty($appointment_date) || empty($appointment_time) || empty($patient_nic) || empty($patient_name) || empty($dentist_code) || empty($dentist_name) || empty($status)) {
        $errors[] = "All fields are required.";
    } else {
        // Check if composite key already exists
        $check_sql = "SELECT * FROM appointments WHERE appointment_id = ? AND appointment_date = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $appointment_id, $appointment_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = "An appointment with this ID and Date already exists.";
        } else {
            // Insert into database
            $sql = "INSERT INTO appointments (appointment_id, appointment_date, appointment_time, patient_nic, patient_name, dentist_code, dentist_name, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $errors[] = "Error preparing statement: " . $conn->error;
            } else {
                $stmt->bind_param("isssssss", $appointment_id, $appointment_date, $appointment_time, $patient_nic, $patient_name, $dentist_code, $dentist_name, $status);
                if ($stmt->execute()) {
                    $success = "Appointment added successfully!";
                } else {
                    $errors[] = "Error executing statement: " . $stmt->error;
                }
                $stmt->close();
            }
        }

        $check_stmt->close();
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add New Appointment</h2>

        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Display Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Appointment Form -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="appointment_id" class="form-label">Appointment ID</label>
                <input type="number" class="form-control" name="appointment_id" required>
            </div>

            <div class="mb-3">
                <label for="appointment_date" class="form-label">Appointment Date</label>
                <input type="date" class="form-control" name="appointment_date" required>
            </div>

            <div class="mb-3">
                <label for="appointment_time" class="form-label">Appointment Time</label>
                <input type="time" class="form-control" name="appointment_time" required>
            </div>

            <div class="mb-3">
                <label for="patient_select" class="form-label">Select Patient</label>
                <select class="form-select" id="patient_select" required>
                    <option value="">-- Select Patient --</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?php echo htmlspecialchars($patient['NIC']); ?>" data-name="<?php echo htmlspecialchars($patient['Fullname']); ?>">
                            <?php echo htmlspecialchars($patient['NIC'] . ' - ' . $patient['Fullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="patient_nic" class="form-label">Patient NIC</label>
                <input type="text" class="form-control" name="patient_nic" id="patient_nic" readonly required>
            </div>

            <div class="mb-3">
                <label for="patient_name" class="form-label">Patient Name</label>
                <input type="text" class="form-control" name="patient_name" id="patient_name" readonly required>
            </div>

            <div class="mb-3">
                <label for="dentist_select" class="form-label">Select Dentist</label>
                <select class="form-select" id="dentist_select" required>
                    <option value="">-- Select Dentist --</option>
                    <?php foreach ($dentists as $dentist): ?>
                        <option value="<?php echo htmlspecialchars($dentist['user_code']); ?>" data-name="<?php echo htmlspecialchars($dentist['Fullname']); ?>">
                            <?php echo htmlspecialchars($dentist['user_code'] . ' - ' . $dentist['Fullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="dentist_code" class="form-label">Dentist Code</label>
                <input type="text" class="form-control" name="dentist_code" id="dentist_code" readonly required>
            </div>

            <div class="mb-3">
                <label for="dentist_name" class="form-label">Dentist Name</label>
                <input type="text" class="form-control" name="dentist_name" id="dentist_name" readonly required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" name="status" required>
                    <option value="Scheduled" selected>Scheduled</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <a href="../appointment_scheduling.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Add Appointment</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const patientSelect = document.getElementById('patient_select');
        const patientNIC = document.getElementById('patient_nic');
        const patientName = document.getElementById('patient_name');

        patientSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            patientNIC.value = selectedOption.value;
            patientName.value = selectedOption.getAttribute('data-name');
        });

        const dentistSelect = document.getElementById('dentist_select');
        const dentistCode = document.getElementById('dentist_code');
        const dentistName = document.getElementById('dentist_name');

        dentistSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            dentistCode.value = selectedOption.value;
            dentistName.value = selectedOption.getAttribute('data-name');
        });
    });
</script>

<?php include '../../includes/footer.php'; ?>
