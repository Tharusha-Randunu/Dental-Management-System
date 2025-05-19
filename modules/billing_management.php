<?php
include '../includes/header.php';
include '../includes/sidebar.php';
include '../config/db.php';

// Fetch bills with patient and dentist details
$sql = "
    SELECT b.*, a.appointment_date, p.Fullname AS patient_name, p.NIC AS patient_nic, u.user_code AS dentist_code, u.Fullname AS dentist_name
    FROM bills b
    JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
    JOIN patients p ON a.patient_nic = p.NIC
    JOIN users u ON a.dentist_code = u.user_code
    ORDER BY b.bill_id DESC
";
$result = $conn->query($sql);
?>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Bill added successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Failed to add the bill. Please try again.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Bill updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['update_error']) && $_GET['update_error'] == 1): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Failed to update the bill. Please try again.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>




<div class="container mt-4">
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Bill added successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['edit_success']) && $_GET['edit_success'] == '1'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Bill updated successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-lg p-4">
    <h2 class="text-center text-primary">Billing Management</h2>

    <div class="d-flex justify-content-between mb-3">
        <a href="../views/dashboard.php" class="btn btn-danger">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
        <a href="billing_functions/add_bill.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Bill
        </a>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
    <!-- Bill ID -->
    <div class="col-md-3 mb-2">
        <select class="form-control search-input" data-column="0">
            <option value="">Select Bill ID</option>
            <?php
                $bill_ids = $conn->query("SELECT DISTINCT bill_id FROM bills ORDER BY bill_id");
                while ($bill = $bill_ids->fetch_assoc()):
            ?>
                <option value="<?= $bill['bill_id'] ?>"><?= $bill['bill_id'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>

   <!-- Appointment ID -->
<div class="col-md-3 mb-2">
    <select class="form-control search-input" data-column="1">
        <option value="">Select Appointment ID</option>
        <?php
            $appt_ids = $conn->query("SELECT DISTINCT appointment_id FROM bills ORDER BY appointment_id");
            while ($row = $appt_ids->fetch_assoc()):
        ?>
            <option value="<?= htmlspecialchars($row['appointment_id']) ?>"><?= htmlspecialchars($row['appointment_id']) ?></option>
        <?php endwhile; ?>
    </select>
</div>

<!-- Appointment Date -->
<div class="col-md-3 mb-2">
    <select class="form-control search-input" data-column="2">
        <option value="">Select Appointment Date</option>
        <?php
            $appt_dates = $conn->query("SELECT DISTINCT appointment_date FROM bills ORDER BY appointment_date");
            while ($row = $appt_dates->fetch_assoc()):
        ?>
            <option value="<?= htmlspecialchars($row['appointment_date']) ?>"><?= htmlspecialchars($row['appointment_date']) ?></option>
        <?php endwhile; ?>
    </select>
</div>


    <!-- Patient Name -->
    <div class="col-md-3 mb-2">
        <select class="form-control search-input" data-column="3">
            <option value="">Select Patient Name</option>
            <?php
                $names = $conn->query("
                    SELECT DISTINCT p.Fullname 
                    FROM bills b
                    JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
                    JOIN patients p ON a.patient_nic = p.NIC
                    ORDER BY p.Fullname
                ");
                while ($row = $names->fetch_assoc()):
            ?>
                <option value="<?= htmlspecialchars($row['Fullname']) ?>"><?= htmlspecialchars($row['Fullname']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Patient NIC -->
    <div class="col-md-3 mb-2">
        <select class="form-control search-input" data-column="4">
            <option value="">Select Patient NIC</option>
            <?php
                $nics = $conn->query("
                    SELECT DISTINCT p.NIC 
                    FROM bills b
                    JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
                    JOIN patients p ON a.patient_nic = p.NIC
                    ORDER BY p.NIC
                ");
                while ($row = $nics->fetch_assoc()):
            ?>
                <option value="<?= $row['NIC'] ?>"><?= $row['NIC'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Dentist Code -->
    <div class="col-md-3 mb-2">
        <select class="form-control search-input" data-column="5">
            <option value="">Select Dentist Code</option>
            <?php
                $codes = $conn->query("
                    SELECT DISTINCT u.user_code 
                    FROM bills b
                    JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
                    JOIN users u ON a.dentist_code = u.user_code
                    ORDER BY u.user_code
                ");
                while ($row = $codes->fetch_assoc()):
            ?>
                <option value="<?= $row['user_code'] ?>"><?= $row['user_code'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Dentist Name -->
    <div class="col-md-3 mb-2">
        <select class="form-control search-input" data-column="6">
            <option value="">Select Dentist Name</option>
            <?php
                $dentists = $conn->query("
                    SELECT DISTINCT u.Fullname 
                    FROM bills b
                    JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
                    JOIN users u ON a.dentist_code = u.user_code
                    ORDER BY u.Fullname
                ");
                while ($row = $dentists->fetch_assoc()):
            ?>
                <option value="<?= htmlspecialchars($row['Fullname']) ?>"><?= htmlspecialchars($row['Fullname']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- Status -->
    <div class="col-md-3 mb-2">
        <select class="form-control search-input" data-column="9">
            <option value="">Select Status</option>
            <option value="Paid">Paid</option>
            <option value="Partially Paid">Partially Paid</option>
            <option value="Unpaid">Unpaid</option>
        </select>
    </div>
</div>

    <!-- Table -->
<div class="table-responsive">
    <table id="billingTable" class="table table-bordered table-hover text-center">
        <thead class="table-dark text-light">
            <tr>
                <th>Bill ID</th>
                <th>Appointment ID</th>
                <th>Appointment Date</th>
                <th>Patient Name</th>
                <th>Patient NIC</th>
                <th>Dentist Code</th>
                <th>Dentist Name</th>
                <th>Grand Total</th>
                <th>Remaining</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="row-<?= $row['bill_id'] ?>">
                        <td><?= $row['bill_id'] ?></td>
                        <td><?= $row['appointment_id'] ?></td>
                        <td><?= $row['appointment_date'] ?></td>
                        <td><?= htmlspecialchars($row['patient_name']) ?></td>
                        <td><?= $row['patient_nic'] ?></td>
                        <td><?= $row['dentist_code'] ?></td>
                        <td><?= htmlspecialchars($row['dentist_name']) ?></td>
                        
                        <td><?= number_format($row['grand_total'], 2) ?></td>
                        <td><?= number_format($row['amount_remaining'], 2) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $row['payment_status'] === 'Paid' ? 'success' : 
                                ($row['payment_status'] === 'Unpaid' ? 'danger' : 
                                ($row['payment_status'] === 'Partially Paid' ? 'warning' : 'secondary')) 
                            ?>">
                                <?= $row['payment_status']; ?>
                            </span>
                        </td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="billing_functions/view_bill.php?id=<?= $row['bill_id'] ?>" class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                            <a href="billing_functions/edit_bill.php?id=<?= $row['bill_id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['bill_id'] ?>" title="Delete"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="12" class="text-center text-muted">No bills found.</td></tr>
            <?php endif; ?>
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
        <h5 class="modal-title">Delete Bill</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to delete this bill?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Feedback Message -->
<div id="message"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".search-input").on("change", function () {
        var column = $(this).data("column");
        var value = $(this).val().toLowerCase().trim();
        $("#billingTable tbody tr").each(function () {
            var cell = $(this).find("td").eq(column).text().toLowerCase().trim();
            $(this).toggle(cell === value || value === "");
        });
    });

    $(".delete-btn").on("click", function () {
        var billId = $(this).data("id");
        $("#confirmDelete").data("id", billId);
        $("#deleteModal").modal("show");
    });

    $("#confirmDelete").on("click", function () {
        var billId = $(this).data("id");
        $.ajax({
            url: 'delete_bill.php',
            type: 'GET',
            data: { id: billId },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === "success") {
                    $("#row-" + billId).remove();
                    $("#deleteModal").modal("hide");
                    $("#message").html("<div class='alert alert-success fixed-top w-100 text-center'>Bill deleted successfully.</div>").fadeIn();
                } else {
                    $("#message").html("<div class='alert alert-danger fixed-top w-100 text-center'>Error deleting bill.</div>").fadeIn();
                }
                setTimeout(() => $("#message").fadeOut("slow"), 3000);
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
