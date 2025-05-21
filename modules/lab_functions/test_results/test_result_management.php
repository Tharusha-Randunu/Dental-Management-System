<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Fetch test results with joined test_requests and patient data
$sql = "
    SELECT r.result_id, r.notes, tr.test_id, tr.patient_nic, p.Fullname AS patient_name
    FROM test_results r
    JOIN test_requests tr ON r.test_id = tr.test_id
    JOIN patients p ON tr.patient_nic = p.NIC
    ORDER BY r.result_id DESC
";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Test Result Management</h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="../../laboratory_management.php" class="btn btn-danger">
                <i class="bi bi-arrow-left"></i> Back to Laboratory
            </a>
            <a href="add_test_result.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Test Result
            </a>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col">
                <select id="filterResultId" class="form-control search-input" data-column="0">
                    <option value="">Select Result ID</option>
                    <?php
                    // Generate unique result_id options dynamically from database
                    $sql_result_ids = "SELECT DISTINCT result_id FROM test_results ORDER BY result_id";
                    $result_ids = $conn->query($sql_result_ids);
                    while ($row = $result_ids->fetch_assoc()) {
                        echo "<option value='{$row['result_id']}'>{$row['result_id']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <select id="filterTestId" class="form-control search-input" data-column="1">
                    <option value="">Select Test ID</option>
                    <?php
                    // Generate unique test_id options dynamically from database
                    $sql_test_ids = "SELECT DISTINCT test_id FROM test_requests ORDER BY test_id";
                    $test_ids = $conn->query($sql_test_ids);
                    while ($row = $test_ids->fetch_assoc()) {
                        echo "<option value='{$row['test_id']}'>{$row['test_id']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <select id="filterPatientNic" class="form-control search-input" data-column="2">
                    <option value="">Select Patient NIC</option>
                    <?php
                    // Generate unique patient NIC options dynamically from database
                    $sql_patient_nic = "SELECT DISTINCT patient_nic FROM test_requests ORDER BY patient_nic";
                    $patient_nic = $conn->query($sql_patient_nic);
                    while ($row = $patient_nic->fetch_assoc()) {
                        echo "<option value='{$row['patient_nic']}'>{$row['patient_nic']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <select id="filterPatientName" class="form-control search-input" data-column="3">
                    <option value="">Select Patient Name</option>
                    <?php
                    // Generate unique patient names dynamically from database
                    $sql_patient_names = "SELECT DISTINCT Fullname FROM patients ORDER BY Fullname";
                    $patient_names = $conn->query($sql_patient_names);
                    while ($row = $patient_names->fetch_assoc()) {
                        echo "<option value='{$row['Fullname']}'>{$row['Fullname']}</option>";
                    }
                    ?>
                </select>
            </div>
          
        </div>

        <div class="table-responsive">
            <table id="testResultTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark text-light">
                    <tr>
                        <th>Result ID</th>
                        <th>Test Request ID</th>
                        <th>Patient NIC</th>
                        <th>Patient Name</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['result_id']}'>";
                            echo "<td>{$row['result_id']}</td>";
                            echo "<td>{$row['test_id']}</td>";
                            echo "<td>{$row['patient_nic']}</td>";
                            echo "<td>{$row['patient_name']}</td>";
                            echo "<td>{$row['notes']}</td>";
                            echo "<td>
                                    <a href='view_test_result.php?id={$row['result_id']}' class='btn btn-sm btn-info' title='View'><i class='bi bi-eye'></i></a>
                                    <a href='edit_test_result.php?id={$row['result_id']}' class='btn btn-sm btn-warning' title='Edit'><i class='bi bi-pencil'></i></a>
                                    <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['result_id']}' title='Delete'><i class='bi bi-trash'></i></button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-muted text-center'>No test results found.</td></tr>";
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
        <h5 class="modal-title">Delete Test Result</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to delete this test result?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<div id="message"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".search-input").on("change", function () {
        var column = $(this).data("column");
        var value = $(this).val().toLowerCase().trim();

        // Loop through each row and apply filtering based on the selected dropdown value
        $("#testResultTable tbody tr").each(function () {
            var cellText = $(this).find("td").eq(column).text().toLowerCase().trim();

            // If the filter value is empty, show the row; otherwise, check for a match
            if (value === "" || cellText.includes(value)) {
                $(this).show();
            } else {
                $(this).hide();
            }
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
            url: 'delete_test_result.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == "success") {
                    $("#row-" + id).remove();
                    $("#deleteModal").modal("hide");
                    $("#message").html("<div class='alert alert-success fixed-top w-100' style='z-index: 1050;'><strong>Success!</strong> Test result deleted successfully.</div>");
                    setTimeout(function() { $("#message").fadeOut("slow"); }, 3000);
                } else {
                    $("#message").html("<div class='alert alert-danger fixed-top w-100' style='z-index: 1050;'><strong>Error!</strong> Could not delete the test result.</div>");
                    setTimeout(function() { $("#message").fadeOut("slow"); }, 3000);
                }
            },
            error: function() {
                $("#message").html("<div class='alert alert-danger fixed-top w-100' style='z-index: 1050;'><strong>Error!</strong> Unexpected error occurred.</div>");
                setTimeout(function() { $("#message").fadeOut("slow"); }, 3000);
            }
        });
    });
});
</script>

<?php include '../../../includes/footer.php'; ?>
