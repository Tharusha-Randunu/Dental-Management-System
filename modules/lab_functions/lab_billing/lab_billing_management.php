<?php
include '../../../includes/header.php';
include '../../../includes/notifications.php';

include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Fetch lab bills with patient names and NICs
$sql = "
    SELECT b.*, p.Fullname AS patient_name, p.NIC AS patient_nic
    FROM lab_bills b
    JOIN patients p ON b.patient_nic = p.NIC
    ORDER BY b.bill_id DESC
";
$result = $conn->query($sql);
?>

<div class="container mt-4">

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
    <div id="successMessage" style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
        Lab bill inserted successfully.
    </div>

    <script>
         document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                var msg = document.getElementById("successMessage");
                if (msg) {
                    msg.style.transition = "opacity 0.5s ease";
                    msg.style.opacity = 0;
                    setTimeout(() => msg.remove(), 500);  
                }
            }, 3000); // 3 seconds
        });
    </script>
<?php endif; ?>

<?php if (isset($_GET['edit_success']) && $_GET['edit_success'] == '1'): ?>
    <div id="successMessage" style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
        Lab bill updated successfully.
    </div>

    <script>
         document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                var msg = document.getElementById("successMessage");
                if (msg) {
                    msg.style.transition = "opacity 0.5s ease";
                    msg.style.opacity = 0;
                    setTimeout(() => msg.remove(), 500);  
                }
            }, 3000); // 3 seconds
        });
    </script>
<?php endif; ?>

<div class="card shadow-lg p-4">
    <h2 class="text-center text-primary">Lab Billing Management</h2>

    <div class="d-flex justify-content-between mb-3">
        <a href="../../laboratory_management.php" class="btn btn-danger">
            <i class="bi bi-arrow-left"></i> Back to Laboratory
        </a>
        <a href="add_lab_bill.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Bill
        </a>
    </div>

    <!-- Search Filters as Dropdowns -->
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <select class="form-control search-input" data-column="0">
                <option value="">Select Bill ID</option>
                <?php
                    $sql_bills = "SELECT DISTINCT bill_id FROM lab_bills ORDER BY bill_id";
                    $result_bills = $conn->query($sql_bills);
                    while ($bill = $result_bills->fetch_assoc()):
                ?>
                    <option value="<?= $bill['bill_id'] ?>"><?= $bill['bill_id'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-control search-input" data-column="1">
                <option value="">Select Patient</option>
                <?php
                    $sql_patients = "SELECT DISTINCT p.Fullname FROM lab_bills b JOIN patients p ON b.patient_nic = p.NIC ORDER BY p.Fullname";
                    $result_patients = $conn->query($sql_patients);
                    while ($patient = $result_patients->fetch_assoc()):
                ?>
                    <option value="<?= htmlspecialchars($patient['Fullname']) ?>"><?= htmlspecialchars($patient['Fullname']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-control search-input" data-column="2">
                <option value="">Select Patient NIC</option>
                <?php
                    $sql_nics = "SELECT DISTINCT p.NIC FROM lab_bills b JOIN patients p ON b.patient_nic = p.NIC ORDER BY p.NIC";
                    $result_nics = $conn->query($sql_nics);
                    while ($nic = $result_nics->fetch_assoc()):
                ?>
                    <option value="<?= $nic['NIC'] ?>"><?= $nic['NIC'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-control search-input" data-column="9">
                <option value="">Select Status</option>
                <option value="Paid">Paid</option>
                <option value="Partially Paid">Partially Paid</option>
                <option value="Unpaid">Unpaid</option>
            </select>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="table-responsive">
        <table id="billingTable" class="table table-bordered table-hover text-center">
            <thead class="table-dark text-light">
                <tr>
                    <th>Bill ID</th>
                    <th>Patient</th>
                    <th>Patient NIC</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Grand Total</th>
                    <th>Paid</th>
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
                            <td><?= htmlspecialchars($row['patient_name']) ?></td>
                            <td><?= htmlspecialchars($row['patient_nic']) ?></td>
                            <td><?= number_format($row['total_amount'], 2) ?></td>
                            <td><?= number_format($row['discount'], 2) ?></td>
                            <td><?= number_format($row['tax'], 2) ?></td>
                            <td><?= number_format($row['grand_total'], 2) ?></td>
                            <td><?= number_format($row['amount_paid'], 2) ?></td>
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
                                <a href="view_lab_bill.php?id=<?= $row['bill_id'] ?>" class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                                <a href="edit_lab_bill.php?id=<?= $row['bill_id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['bill_id'] ?>" title="Delete"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="12" class="text-center text-muted">No lab bills found.</td></tr>
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
        <h5 class="modal-title">Delete Lab Bill</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to delete this lab bill?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Feedback Message -->
<div id="message"></div>

<!-- Scripts -->
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
            url: 'delete_lab_bill.php',
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

<?php include '../../../includes/footer.php'; ?>
