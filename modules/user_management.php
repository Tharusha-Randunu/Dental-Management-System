<?php
include '../includes/header.php';
include '../includes/sidebar.php'; 
include '../config/db.php';

// Fetch users from the database
$sql = "SELECT NIC,user_code, Fullname, Role, Address, Contact, Gender, Email, Username FROM users";
$result = $conn->query($sql);

// Store users in an array for filters
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">User Management</h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="../views/dashboard.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <a href="user_functions/add_user.php" class="btn btn-success"><i class="bi bi-person-plus"></i> Add User</a>
        </div>

        <!-- Search Dropdowns -->
        <div class="row mb-4">
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="0">
                    <option value="">Search NIC</option>
                    <?php foreach (array_unique(array_column($users, 'NIC')) as $nic): ?>
                        <option value="<?= $nic ?>"><?= $nic ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="2">
                    <option value="">Search Full Name</option>
                    <?php foreach (array_unique(array_column($users, 'Fullname')) as $name): ?>
                        <option value="<?= $name ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="3">
                    <option value="">Search Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Dentist">Dentist</option>
                    <option value="Receptionist">Receptionist</option>
                    <option value="Lab_Technician">Lab Technician</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="4">
                    <option value="">Search Address</option>
                    <?php foreach (array_unique(array_column($users, 'Address')) as $address): ?>
                        <option value="<?= $address ?>"><?= $address ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="5">
                    <option value="">Search Contact</option>
                    <?php foreach (array_unique(array_column($users, 'Contact')) as $contact): ?>
                        <option value="<?= $contact ?>"><?= $contact ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="6">
                    <option value="">Search Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="7">
                    <option value="">Search Email</option>
                    <?php foreach (array_unique(array_column($users, 'Email')) as $email): ?>
                        <option value="<?= $email ?>"><?= $email ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select class="form-control search-input text-muted" data-column="8">
                    <option value="">Search Username</option>
                    <?php foreach (array_unique(array_column($users, 'Username')) as $username): ?>
                        <option value="<?= $username ?>"><?= $username ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table id="userTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark text-light">
                    <tr>
                        <th>NIC</th>
                        <th>User Code</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php if (!empty($users)) {
                        foreach ($users as $row) {
                            echo "<tr>";
                            echo "<td>{$row['NIC']}</td>";
                            echo "<td>{$row['user_code']}</td>";
                            echo "<td>{$row['Fullname']}</td>";
                            echo "<td>{$row['Role']}</td>";
                            echo "<td>{$row['Address']}</td>";
                            echo "<td>{$row['Contact']}</td>";
                            echo "<td>{$row['Gender']}</td>";
                            echo "<td>{$row['Email']}</td>";
                            echo "<td>{$row['Username']}</td>";
                            echo "<td>
                                    <a href='user_functions/view_user.php?nic={$row['NIC']}' class='btn btn-sm btn-info' title='View'><i class='bi bi-eye'></i></a>
                                    <a href='user_functions/edit_user.php?nic={$row['NIC']}' class='btn btn-sm btn-warning mx-1' title='Edit'><i class='bi bi-pencil'></i></a>
                                    <a href='#' class='btn btn-sm btn-danger delete-btn' data-nic='{$row['NIC']}' title='Delete'><i class='bi bi-trash'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' class='text-center text-muted'>No users found.</td></tr>";
                    } ?>
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
                <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this user?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Dropdown search function
        $(".search-input").on("change", function () {
            var column = $(this).data("column");
            var searchValue = $(this).val().toLowerCase().trim();

            $("#userTable tbody tr").each(function () {
                var cellText = $(this).find("td").eq(column).text().toLowerCase().trim();

                if (searchValue === "" || cellText === searchValue) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Delete confirmation
        $(".delete-btn").on("click", function () {
            var nic = $(this).data("nic");
            $("#confirmDelete").attr("href", "user_functions/delete_user.php?nic=" + nic);
            $("#deleteModal").modal("show");
        });
    });
</script>

<?php if (isset($_GET['delete']) && $_GET['delete'] == 'success') { ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">User has been successfully deleted!</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    </script>
<?php } ?>
