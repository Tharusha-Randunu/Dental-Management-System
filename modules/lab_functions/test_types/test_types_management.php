<?php
include '../../../includes/header.php';
include '../../../includes/notifications.php';

include '../../../includes/sidebar.php'; 

include '../../../config/db.php';

// Fetch test types from the database
$sql = "SELECT test_type_id, test_name, cost, sample_type FROM test_types";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Laboratory Test Types</h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="../../laboratory_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back to Laboratory</a>
            <a href="add_test_type.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Test Type</a>
        </div>

        <!-- Search Inputs -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="0">
                    <option value="">Select Test ID</option>
                    <?php 
                        // Populate Test ID Dropdown
                        $id_result = $conn->query("SELECT DISTINCT test_type_id FROM test_types");
                        while($row = $id_result->fetch_assoc()) {
                            echo "<option value='{$row['test_type_id']}'>{$row['test_type_id']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="1">
                    <option value="">Select Test Name</option>
                    <?php 
                        // Populate Test Name Dropdown
                        $name_result = $conn->query("SELECT DISTINCT test_name FROM test_types");
                        while($row = $name_result->fetch_assoc()) {
                            echo "<option value='{$row['test_name']}'>{$row['test_name']}</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="2">
                    <option value="">Select Price</option>
                    <?php 
                        // Populate Price Dropdown
                        $price_result = $conn->query("SELECT DISTINCT cost FROM test_types");
                        while($row = $price_result->fetch_assoc()) {
                            echo "<option value='{$row['cost']}'>" . number_format($row['cost'], 2) . "</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-control search-input" data-column="3">
                    <option value="">Select Sample Type</option>
                    <?php 
                        // Populate Sample Type Dropdown
                        $sample_result = $conn->query("SELECT DISTINCT sample_type FROM test_types");
                        while($row = $sample_result->fetch_assoc()) {
                            echo "<option value='{$row['sample_type']}'>{$row['sample_type']}</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <!-- Test Types Table -->
        <div class="table-responsive">
            <table id="testTypeTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark text-light">
                    <tr>
                        <th>Test ID</th>
                        <th>Test Name</th>
                        <th>Price (LKR)</th>
                        <th>Sample Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='row-{$row['test_type_id']}'>";
                            echo "<td>{$row['test_type_id']}</td>";
                            echo "<td>{$row['test_name']}</td>";
                            echo "<td>" . number_format($row['cost'], 2) . "</td>";
                            echo "<td>{$row['sample_type']}</td>";
                            echo "<td>
                                    <a href='edit_test_type.php?id={$row['test_type_id']}' class='btn btn-sm btn-warning' title='Edit'><i class='bi bi-pencil'></i></a>
                                    <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['test_type_id']}' title='Delete'><i class='bi bi-trash'></i></button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-muted text-center'>No test types found.</td></tr>";
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
        <h5 class="modal-title">Delete Test Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to delete this test type?</div>
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
    $(".search-input").on("change", function () {
        var column = $(this).data("column");
        var value = $(this).val().toLowerCase().trim();

        $("#testTypeTable tbody tr").each(function () {
            var cellText = $(this).find("td").eq(column).text().toLowerCase().trim();
            $(this).toggle(cellText.includes(value));
        });
    });

    // Delete Button Click Handler
    $(".delete-btn").on("click", function () {
        var id = $(this).data("id");
        $("#confirmDelete").data("id", id);
        $("#deleteModal").modal("show");
    });

    // Confirm Delete Button in Modal
    $("#confirmDelete").on("click", function () {
        var id = $(this).data("id");

        // AJAX Request to Delete Test Type
        $.ajax({
            url: 'delete_test_type.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == "success") {
                    // Remove row from table
                    $("#row-" + id).remove();
                    $("#deleteModal").modal("hide");
                    $("#message").html("<div class='alert alert-success fixed-top w-100' style='z-index: 1050;'><strong>Success!</strong> Test type deleted successfully.</div>");
                    setTimeout(function() {
                        $("#message").fadeOut("slow");
                    }, 3000); // Fade out after 3 seconds
                } else {
                    $("#message").html("<div class='alert alert-danger fixed-top w-100' style='z-index: 1050;'><strong>Error!</strong> There was an error deleting the test type. Please try again.</div>");
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
