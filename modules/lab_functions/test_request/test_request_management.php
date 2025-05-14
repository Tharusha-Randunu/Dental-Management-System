<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Fetch test requests with joined details
$sql = "
    SELECT tr.test_id, tr.patient_nic, tr.request_date, tr.status, tr.result_availability, tr.notes,
           p.Fullname AS patient_name,
           t.test_name,
           u1.Fullname AS requested_by, u1.user_code AS requested_by_code,
           u2.Fullname AS assigned_to, u2.user_code AS assigned_to_code
    FROM test_requests tr
    JOIN patients p ON tr.patient_nic = p.NIC
    JOIN test_types t ON tr.test_type_id = t.test_type_id
    JOIN users u1 ON tr.requested_by = u1.NIC
    LEFT JOIN users u2 ON tr.assigned_to = u2.NIC
    ORDER BY tr.test_id DESC
";
$result = $conn->query($sql);

// Dropdown values
$patients = $conn->query("SELECT NIC, Fullname FROM patients");
$test_types = $conn->query("SELECT test_name FROM test_types");
$statuses = $conn->query("SELECT DISTINCT status FROM test_requests");
$results = $conn->query("SELECT DISTINCT result_availability FROM test_requests");
// Separate user queries based on roles
$requested_by_users = $conn->query("SELECT NIC, Fullname, user_code FROM users WHERE role = 'Dentist'");
$assigned_to_users = $conn->query("SELECT NIC, Fullname, user_code FROM users WHERE role = 'Lab_Technician'");


?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Laboratory Test Requests</h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="../../laboratory_management.php" class="btn btn-danger">
                <i class="bi bi-arrow-left"></i> Back to Laboratory
            </a>
            <a href="add_test_request.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Test Request
            </a>
        </div>

        <!-- Search Inputs -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="1">
                    <option value="">All Patient NICs</option>
                    <?php while ($p = $patients->fetch_assoc()) echo "<option value='{$p['NIC']}'>{$p['NIC']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="2">
                    <option value="">All Patient Names</option>
                    <?php mysqli_data_seek($patients, 0); while ($p = $patients->fetch_assoc()) echo "<option value='{$p['Fullname']}'>{$p['Fullname']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="3">
                    <option value="">All Test Types</option>
                    <?php while ($t = $test_types->fetch_assoc()) echo "<option value='{$t['test_name']}'>{$t['test_name']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="4">
                    <option value="">All Statuses</option>
                    <?php while ($s = $statuses->fetch_assoc()) echo "<option value='{$s['status']}'>{$s['status']}</option>"; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="5">
    <option value="">All Requested By (Code)</option>
    <?php while ($u = $requested_by_users->fetch_assoc()) echo "<option value='{$u['user_code']}'>{$u['user_code']}</option>"; ?>
</select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="6">
    <option value="">All Assigned To (Code)</option>
    <?php while ($u = $assigned_to_users->fetch_assoc()) echo "<option value='{$u['user_code']}'>{$u['user_code']}</option>"; ?>
</select>
            </div>
            <div class="col-md-3 mb-2">
                <input type="date" class="form-control search-input" data-column="7">
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="8">
                    <option value="">All Results</option>
                    <?php while ($r = $results->fetch_assoc()) echo "<option value='{$r['result_availability']}'>{$r['result_availability']}</option>"; ?>
                </select>
            </div>
        </div>

        <!-- Test Requests Table -->
        <div class="table-responsive">
            <table id="testRequestTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark text-light">
                    <tr>
                        <th>Request ID</th>
                        <th>Patient NIC</th>
                        <th>Patient</th>
                        <th>Test Type</th>
                        <th>Status</th>
                        <th>Requested By (Code)</th>
                        <th>Assigned To (Code)</th>
                        <th>Request Date</th>
                        <th>Result</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['test_id']}'>";
                            echo "<td>{$row['test_id']}</td>";
                            echo "<td>{$row['patient_nic']}</td>";
                            echo "<td>{$row['patient_name']}</td>";
                            echo "<td>{$row['test_name']}</td>";
                            echo "<td>{$row['status']}</td>";
                            echo "<td>{$row['requested_by_code']}</td>";
                            echo "<td>" . ($row['assigned_to_code'] ?? '-') . "</td>";
                            echo "<td>{$row['request_date']}</td>";
                            echo "<td>{$row['result_availability']}</td>";
                            echo "<td>
                                    <a href='view_test_request.php?id={$row['test_id']}' class='btn btn-sm btn-info' title='View'><i class='bi bi-eye'></i></a>
                                    <a href='edit_test_request.php?id={$row['test_id']}' class='btn btn-sm btn-warning' title='Edit'><i class='bi bi-pencil'></i></a>
                                    <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['test_id']}' title='Delete'><i class='bi bi-trash'></i></button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' class='text-muted text-center'>No test requests found.</td></tr>";
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
        <h5 class="modal-title">Delete Test Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to delete this test request?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Success/Error Messages -->
<div id="message"></div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".search-input").on("change keyup", function () {
        var filters = [];
        $(".search-input").each(function () {
            filters.push($(this).val().toLowerCase().trim());
        });

        $("#testRequestTable tbody tr").each(function () {
            var match = true;
            $(this).find("td").each(function (index) {
                if (filters[index] && !$(this).text().toLowerCase().includes(filters[index])) {
                    match = false;
                }
            });
            $(this).toggle(match);
        });
    });

    $(".delete-btn").on("click", function () {
        var id = $(this).data("id");
        $("#confirmDelete").data("id", id);
        $("#deleteModal").modal("show");
    });

    $("#confirmDelete").on("click", function () {
        var id = $(this).data("id");
        $.ajax({
            url: 'delete_test_request.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == "success") {
                    $("#row-" + id).remove();
                    $("#deleteModal").modal("hide");
                    $("#message").html("<div class='alert alert-success fixed-top w-100' style='z-index: 1050;'><strong>Success!</strong> Test request deleted successfully.</div>");
                    setTimeout(function() {
                        $("#message").fadeOut("slow");
                    }, 3000);
                } else {
                    $("#message").html("<div class='alert alert-danger fixed-top w-100' style='z-index: 1050;'><strong>Error!</strong> There was a problem deleting the test request. Please try again.</div>");
                    setTimeout(function() {
                        $("#message").fadeOut("slow");
                    }, 3000);
                }
            },
            error: function() {
                $("#message").html("<div class='alert alert-danger fixed-top w-100' style='z-index: 1050;'><strong>Error!</strong> Unexpected error occurred.</div>");
                setTimeout(function() {
                    $("#message").fadeOut("slow");
                }, 3000);
            }
        });
    });
});
</script>

<?php include '../../../includes/footer.php'; ?>
