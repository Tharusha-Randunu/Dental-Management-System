<?php
include '../includes/header.php';
include '../includes/sidebar.php'; 
include '../config/db.php';

// Fetch patients from the database
$sql = "SELECT NIC, Fullname, Address, Contact, Gender, Email, Username FROM patients";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Patient Management</h2>

    
    
        <div class="d-flex justify-content-between mb-3">
        <!-- Back to Dashboard Button -->
        <a href="../views/dashboard.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>

    <!-- Add Patient Button -->
    <a href="patient_functions/add_patient.php" class="btn btn-success"><i class="bi bi-person-plus"></i> Add Patient</a>

    
        </div>
        

        <!-- Search Inputs -->
        <div class="row mb-4">
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="0" placeholder="Search NIC"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="1" placeholder="Search Name"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="2" placeholder="Search Address"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="3" placeholder="Search Contact"></div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="4">
                    <option value="">Search Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="5" placeholder="Search Email"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="6" placeholder="Search Username"></div>
        </div>

        <!-- Patients Table -->
        <div class="table-responsive">
            <table id="patientTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark text-light">
                    <tr>
                        <th>NIC</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['NIC']}</td>";
                            echo "<td>{$row['Fullname']}</td>";
                            echo "<td>{$row['Address']}</td>";
                            echo "<td>{$row['Contact']}</td>";
                            echo "<td>{$row['Gender']}</td>";
                            echo "<td>{$row['Email']}</td>";
                            echo "<td>{$row['Username']}</td>";
                            echo "<td>
                                    <a href='patient_functions/view_patient.php?nic={$row['NIC']}' class='btn btn-sm btn-info' title='View'><i class='bi bi-eye'></i></a>
                                    <a href='patient_functions/edit_patient.php?nic={$row['NIC']}' class='btn btn-sm btn-warning mx-1' title='Edit'><i class='bi bi-pencil'></i></a>
                                    <a href='#' class='btn btn-sm btn-danger delete-btn' data-nic='{$row['NIC']}' title='Delete'><i class='bi bi-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center text-muted'>No patients found.</td></tr>";
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
                <h5 class="modal-title" id="deleteModalLabel">Delete Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this patient?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>



<!-- JavaScript for Searching and Deleting -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Search function
        $(".search-input").on("keyup change", function () {
            var column = $(this).data("column"); // Get column index
            var searchValue = $(this).val().toLowerCase().trim();

            $("#patientTable tbody tr").each(function () {
                var cellText = $(this).find("td").eq(column).text().toLowerCase().trim();

                if (column === 4) { // Gender column (Exact Match)
                    if (searchValue === "" || cellText === searchValue) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                } else { // Other columns (Partial Match)
                    $(this).toggle(cellText.includes(searchValue));
                }
            });
        });

        // Delete confirmation
        $(".delete-btn").on("click", function () {
            var nic = $(this).data("nic");
            $("#confirmDelete").attr("href", "patient_functions/delete_patient.php?nic=" + nic);
            $("#deleteModal").modal("show");
        });
    });
</script>


<?php if (isset($_GET['delete']) && $_GET['delete'] == 'success') { ?>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Patient has been successfully deleted!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show the modal after the page loads
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    </script>
<?php } ?>
<?php include '../../../includes/footer.php'; ?>