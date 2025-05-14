<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Check if appointment ID and date are provided
if (isset($_GET['id']) && isset($_GET['date'])) {
    $appointment_id = $_GET['id'];
    $appointment_date = $_GET['date'];

    // Fetch appointment details from the database
    $sql = "SELECT appointment_id, appointment_date, appointment_time, patient_nic, patient_name, status, created_at, updated_at, dentist_code, dentist_name 
            FROM appointments 
            WHERE appointment_id = ? AND appointment_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $appointment_id, $appointment_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $appointment = $result->fetch_assoc();
    } else {
        echo "<script>alert('Appointment not found!'); window.location.href='appointments.php';</script>";
        exit();
    }
    $stmt->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='appointments.php';</script>";
    exit();
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Appointment Details</h2>

        <!-- Back Button -->
        <div class="d-flex justify-content-start mb-3">
            <a href="../appointment_scheduling.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Appointment ID</th>
                    <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                </tr>
                <tr>
                    <th>Time</th>
                    <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                </tr>
                <tr>
                    <th>Patient NIC</th>
                    <td><?php echo htmlspecialchars($appointment['patient_nic']); ?></td>
                </tr>
                <tr>
                    <th>Patient Name</th>
                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                </tr>
                <tr>
                    <th>Dentist Code</th>
                    <td><?php echo htmlspecialchars($appointment['dentist_code']); ?></td>
                </tr>
                <tr>
                    <th>Dentist Name</th>
                    <td><?php echo htmlspecialchars($appointment['dentist_name']); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php
                        $status = $appointment['status'];
                        $status_badge = ($status === 'Completed') ? 'badge bg-success' :
                                        (($status === 'Pending') ? 'badge bg-warning' :
                                        (($status === 'Cancelled') ? 'badge bg-danger' : 'badge bg-secondary'));
                        echo "<span class='$status_badge'>$status</span>";
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td><?php echo htmlspecialchars($appointment['created_at']); ?></td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td><?php echo htmlspecialchars($appointment['updated_at']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
