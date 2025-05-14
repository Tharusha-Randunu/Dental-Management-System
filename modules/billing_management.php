<?php
include '../includes/header.php';
include '../includes/sidebar.php';
include '../config/db.php';

$message = ""; // Initialize message variable

// Handle form submission for adding a new bill
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_bill'])) {
    $nic = $_POST['nic'];
    $total_amount = $_POST['total_amount'];
    $discount = $_POST['discount'];
    $tax = $_POST['tax'];
    $grand_total = $_POST['grand_total'];
    $amount_paid = $_POST['amount_paid'];
    $amount_remaining = $_POST['amount_remaining'];
    $payment_status = $_POST['payment_status'];

    $sql = "INSERT INTO bills (NIC, total_amount, discount, tax, grand_total, amount_paid, amount_remaining, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdddddds", $nic, $total_amount, $discount, $tax, $grand_total, $amount_paid, $amount_remaining, $payment_status);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Bill added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error adding bill. Please try again.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Billing Management</h2>

        <?= $message; ?> <!-- Display success or error message -->

        <!-- Button to Open Modal for Adding New Bill -->
        <div class="d-flex justify-content-between mb-3">
            <a href="../views/dashboard.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <a href="billing_functions/add_bill.php" class="btn btn-success"><i class="bi bi-calendar-plus"></i> Create New Bill</a>
        </div>

        <!-- Search Inputs -->
        <div class="row mb-4">
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="0" placeholder="Search Bill ID"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="1" placeholder="Search NIC"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="2" placeholder="Search Total Amount"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="3" placeholder="Search Discount"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="4" placeholder="Search Tax"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="5" placeholder="Search Grand Total"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="6" placeholder="Search Amount Paid"></div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="7" placeholder="Search Remaining"></div>
            <div class="col-md-2 mb-2">
                <select class="form-control" id="paymentStatusSearch">
                    <option value="">Search Payment Status</option>
                    <option value="Paid">Paid</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Partially Paid">Partially Paid</option>
                </select>
            </div>
            <div class="col-md-2 mb-2"><input type="text" class="form-control search-input" data-column="9" placeholder="Search Created At"></div>
        </div>

        <!-- Table to Display Bills -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="billingTable">
                <thead class="table-dark">
                    <tr>
                        <th>Bill ID</th>
                        <th>NIC</th>
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Tax</th>
                        <th>Grand Total</th>
                        <th>Amount Paid</th>
                        <th>Amount Remaining</th>
                        <th>Payment Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM bills ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['bill_id']}</td>
                                    <td>{$row['NIC']}</td>
                                    <td>Rs. " . number_format($row['total_amount'], 2) . "</td>
                                    <td>Rs. " . number_format($row['discount'], 2) . "</td>
                                    <td>Rs. " . number_format($row['tax'], 2) . "</td>
                                    <td>Rs. " . number_format($row['grand_total'], 2) . "</td>
                                    <td>Rs. " . number_format($row['amount_paid'], 2) . "</td>
                                    <td>Rs. " . number_format($row['amount_remaining'], 2) . "</td>
                                    <td><span class='badge bg-" . 
                                        ($row['payment_status'] == 'Paid' ? "success" : 
                                        ($row['payment_status'] == 'Unpaid' ? "danger" : 
                                        ($row['payment_status'] == 'Partially Paid' ? "warning" : "secondary"))) . 
                                        "'>{$row['payment_status']}</span></td>
                                    <td>{$row['created_at']}</td>
                                    <td>
                                        <a href='billing_functions/view_bill.php?id={$row['bill_id']}' class='btn btn-info btn-sm'><i class='bi bi-eye'></i></a>
                                        <a href='billing_functions/edit_bill.php?id={$row['bill_id']}' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a>
                                        <a href='#' class='btn btn-danger btn-sm delete-btn' data-id='{$row['bill_id']}' onclick='return false;'><i class='bi bi-trash'></i></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center'>No bills found</td></tr>";
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
                <h5 class="modal-title" id="deleteModalLabel">Delete Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this bill?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<!-- JavaScript for Delete Confirmation and Search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Search by text input
        $(".search-input").on("keyup change", function () {
            var column = $(this).data("column");
            var searchValue = $(this).val().toLowerCase().trim();

            $("#billingTable tbody tr").each(function () {
                var cellText = $(this).find("td").eq(column).text().toLowerCase().trim();
                $(this).toggle(cellText.includes(searchValue));
            });
        });

        // Filter by payment status
        $("#paymentStatusSearch").on("change", function () {
            var selectedStatus = $(this).val().toLowerCase().trim();

            $("#billingTable tbody tr").each(function () {
                var cellStatus = $(this).find("td").eq(8).text().toLowerCase().trim();
                $(this).toggle(selectedStatus === "" || cellStatus === selectedStatus);
            });
        });

        // Delete confirmation modal
        $(".delete-btn").on("click", function () {
            var billId = $(this).data("id");
            $("#confirmDelete").attr("href", "billing_functions/delete_bill.php?id=" + billId);
            $("#deleteModal").modal("show");
        });
    });
</script>
