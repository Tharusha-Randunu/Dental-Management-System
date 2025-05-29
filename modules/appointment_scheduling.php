<?php
include '../includes/header.php';
include '../includes/notifications.php';
include '../includes/sidebar.php';


include '../config/db.php';

// Fetch appointments from the database
$sql = "SELECT appointment_id, appointment_date, appointment_time, patient_nic, patient_name, dentist_code, dentist_name, status, created_at, updated_at FROM appointments";
$result = $conn->query($sql);

// Get distinct values for dropdowns
$distinct_sql = "SELECT DISTINCT appointment_id, appointment_date, appointment_time, patient_nic, patient_name, status FROM appointments";
$distinct_result = $conn->query($distinct_sql);

$appointment_ids = $dates = $times = $nics = $names = $statuses = [];

if ($distinct_result->num_rows > 0) {
    while ($row = $distinct_result->fetch_assoc()) {
        $appointment_ids[] = $row['appointment_id'];
        $dates[] = $row['appointment_date'];
        $times[] = $row['appointment_time'];
        $nics[] = $row['patient_nic'];
        $names[] = $row['patient_name'];
        $statuses[] = $row['status'];
    }
}

function createOptions($array) {
    $unique = array_unique($array);
    sort($unique);
    $options = "<option value=''>Select</option>";
    foreach ($unique as $value) {
        $options .= "<option value='" . htmlspecialchars($value) . "'>" . htmlspecialchars($value) . "</option>";
    }
    return $options;
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Appointments</h2>

        <!-- Add Appointment & Back Buttons -->
        <div class="d-flex justify-content-between mb-3">
            <a href="../views/dashboard.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <a href="appointment_functions/add_appointment.php" class="btn btn-success"><i class="bi bi-calendar-plus"></i> Add Appointment</a>
        </div>

        <!-- Search Filters below buttons with spaces in between -->
        <div class="row mb-4">
            <div class="col-md-2 mb-2">
                <select id="searchAppointmentId" class="form-control search-input" data-column="0">
                    <option value="" disabled selected hidden>Select Appointment ID</option>
                    <?= createOptions($appointment_ids) ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select id="searchDate" class="form-control search-input" data-column="1">
                    <option value="" disabled selected hidden>Select Date</option>
                    <?= createOptions($dates) ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select id="searchTime" class="form-control search-input" data-column="2">
                    <option value="" disabled selected hidden>Select Time</option>
                    <?= createOptions($times) ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select id="searchPatientNIC" class="form-control search-input" data-column="3">
                    <option value="" disabled selected hidden>Select NIC</option>
                    <?= createOptions($nics) ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select id="searchPatientName" class="form-control search-input" data-column="4">
                    <option value="" disabled selected hidden>Select Name</option>
                    <?= createOptions($names) ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select id="searchStatus" class="form-control search-input" data-column="7">
                    <option value="" >Select Status</option>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="table-responsive">
            <table id="appointmentTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark text-light">
                    <tr>
                        <th>Appointment ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient NIC</th>
                        <th>Patient Name</th>
                        <th>Dentist Code</th>
                        <th>Dentist Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status = $row['status'];
                            $status_badge = ($status === 'Completed') ? 'badge bg-success' :
                                            (($status === 'Pending') ? 'badge bg-warning' :
                                            (($status === 'Cancelled') ? 'badge bg-danger' : 'badge bg-secondary'));
                            echo "<tr>";
                            echo "<td>{$row['appointment_id']}</td>";
                            echo "<td>{$row['appointment_date']}</td>";
                            echo "<td>{$row['appointment_time']}</td>";
                            echo "<td>{$row['patient_nic']}</td>";
                            echo "<td>{$row['patient_name']}</td>";
                            echo "<td>{$row['dentist_code']}</td>";
                            echo "<td>{$row['dentist_name']}</td>";
                            echo "<td><span class='$status_badge'>{$row['status']}</span></td>";
                            echo "<td>{$row['created_at']}</td>";
                            echo "<td>{$row['updated_at']}</td>";
                            echo "<td>
                                    <a href='appointment_functions/view_appointment.php?id={$row['appointment_id']}&date={$row['appointment_date']}' class='btn btn-sm btn-info' title='View'><i class='bi bi-eye'></i></a>
                                    <a href='appointment_functions/edit_appointment.php?id={$row['appointment_id']}&date={$row['appointment_date']}' class='btn btn-sm btn-warning mx-1' title='Update'><i class='bi bi-pencil'></i></a>
                                    <a href='#' class='btn btn-sm btn-danger delete-btn' data-id='{$row['appointment_id']}' data-date='{$row['appointment_date']}' title='Delete'><i class='bi bi-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center text-muted'>No appointments found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this appointment?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($_GET['delete'])) {
    if ($_GET['delete'] == 'success') {
        echo "<div class='alert alert-success'>Appointment has been successfully deleted!</div>";
    } elseif ($_GET['delete'] == 'error') {
        echo "<div class='alert alert-danger'>There was an error deleting the appointment. Please try again.</div>";
    } elseif ($_GET['delete'] == 'invalid') {
        echo "<div class='alert alert-danger'>Invalid request. Appointment not found.</div>";
    }
}
?>

<!-- JavaScript for Search Filter -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Search function
        $(".search-input").on("change", function () {
            var column = $(this).data("column");
            var searchValue = $(this).val().toLowerCase().trim();

            $("#appointmentTable tbody tr").each(function () {
                var cellText = $(this).find("td").eq(column).text().toLowerCase().trim();

                if (searchValue === "" || cellText === searchValue) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });  
        });

// Handle delete button click
    $('.delete-btn').click(function () {
        var appointmentId = $(this).data('id');
        var appointmentDate = $(this).data('date');
        var deleteUrl = 'appointment_functions/delete_appointment.php?id=' + encodeURIComponent(appointmentId) + '&date=' + encodeURIComponent(appointmentDate);
        $('#confirmDelete').attr('href', deleteUrl);
        $('#deleteModal').modal('show');
    });
});


    
</script>
<?php include '../includes/footer.php'; ?>
