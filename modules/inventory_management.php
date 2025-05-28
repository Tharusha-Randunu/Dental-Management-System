<?php

include '../includes/header.php';
include '../includes/sidebar.php'; 
include '../config/db.php';



// Ensure 'role' is set in session
$role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : '';



// Fetch inventory items from the database
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
    
        <h2 class="text-center text-primary">Inventory Management</h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="../views/dashboard.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            <?php if ($role !== 'dentist') { ?>
            <a href="inventory_functions/add_inventory.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Item</a>
        <?php } ?>
        </div>

        <!-- Search Inputs as Dropdowns -->
        <div class="row mb-4">
            <div class="col-md-3 mb-2">
                <select class="form-select search-input" data-column="0">
                    <option value="">Select Item ID</option>
                    <?php
                    // Fetch distinct item IDs for the dropdown
                    $sql_ids = "SELECT DISTINCT item_id FROM inventory";
                    $result_ids = $conn->query($sql_ids);
                    while ($row = $result_ids->fetch_assoc()) {
                        echo "<option value='{$row['item_id']}'>{$row['item_id']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-select search-input" data-column="1">
                    <option value="">Select Item Name</option>
                    <?php
                    // Fetch distinct item names for the dropdown
                    $sql_names = "SELECT DISTINCT item_name FROM inventory";
                    $result_names = $conn->query($sql_names);
                    while ($row = $result_names->fetch_assoc()) {
                        echo "<option value='{$row['item_name']}'>{$row['item_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select class="form-select search-input" data-column="2">
                    <option value="">Select Category</option>
                    <?php
                    // Fetch distinct categories for the dropdown
                    $sql_categories = "SELECT DISTINCT category FROM inventory";
                    $result_categories = $conn->query($sql_categories);
                    while ($row = $result_categories->fetch_assoc()) {
                        echo "<option value='{$row['category']}'>{$row['category']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
    <select class="form-select search-input" data-column="8">
        <option value="">Select Supplier Code</option>
        <?php
        // Fetch distinct supplier codes for the dropdown
        $sql_codes = "SELECT DISTINCT supplier_code FROM inventory";
        $result_codes = $conn->query($sql_codes);
        while ($row = $result_codes->fetch_assoc()) {
            echo "<option value='{$row['supplier_code']}'>{$row['supplier_code']}</option>";
        }
        ?>
    </select>
</div>

            <div class="col-md-3 mb-2">
                <select class="form-select search-input" data-column="7">
                    <option value="">Select Supplier</option>
                    <?php
                    // Fetch distinct suppliers for the dropdown
                    $sql_suppliers = "SELECT DISTINCT supplier_name FROM inventory";
                    $result_suppliers = $conn->query($sql_suppliers);
                    while ($row = $result_suppliers->fetch_assoc()) {
                        echo "<option value='{$row['supplier_name']}'>{$row['supplier_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="table-responsive">
            <table id="inventoryTable" class="table table-bordered table-hover text-center">
                <thead class="table-dark text-light">
                    <tr>
                        <th>Item Id</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Reorder Level</th>
                        <th>Unit Price</th>
                        <th>Supplier</th>
                        <th>Supplier Code</th>
                        <th>Total Value</th>
                        <th>Purchase Date</th>
                        <th>Expiry Date</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['item_id']}</td>";
                            echo "<td>{$row['item_name']}</td>";
                            echo "<td>{$row['category']}</td>";
                            echo "<td>{$row['quantity']}</td>";
                            echo "<td>{$row['unit']}</td>";
                            echo "<td>{$row['reorder_level']}</td>";
                            echo "<td>{$row['unit_price']}</td>";
                            echo "<td>{$row['supplier_name']}</td>";
                            echo "<td>{$row['supplier_code']}</td>";
                            echo "<td>{$row['total_value']}</td>";
                            echo "<td>{$row['purchase_date']}</td>";
                            echo "<td>{$row['expiry_date']}</td>";
                            echo "<td>{$row['notes']}</td>";
                            echo "<td>
    <a href='inventory_functions/view_inventory.php?id={$row['item_id']}&supplier_code={$row['supplier_code']}' class='btn btn-sm btn-info' title='View'><i class='bi bi-eye'></i></a>
    <a href='inventory_functions/edit_inventory.php?id={$row['item_id']}&supplier_code={$row['supplier_code']}' class='btn btn-sm btn-warning mx-1' title='Edit'><i class='bi bi-pencil'></i></a>";
    
    if ($role !== 'dentist') {
        echo "<a href='#' class='btn btn-sm btn-danger delete-btn' data-id='{$row['item_id']}' title='Delete'><i class='bi bi-trash'></i></a>";
    }

echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='14' class='text-center text-muted'>No inventory items found.</td></tr>";
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
                <h5 class="modal-title" id="deleteModalLabel">Delete Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- JS for Searching and Deleting -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".search-input").on("change", function () {
            var column = $(this).data("column");
            var value = $(this).val().toLowerCase().trim();

            $("#inventoryTable tbody tr").each(function () {
                var cell = $(this).find("td").eq(column).text().toLowerCase().trim();
                $(this).toggle(cell.includes(value));
            });
        });

        $(".delete-btn").on("click", function () {
            var id = $(this).data("id");
            $("#confirmDelete").attr("href", "inventory_functions/delete_inventory.php?id=" + id);
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
                <div class="modal-body">
                    Inventory item has been successfully deleted!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    </script>
<?php } ?>
<?php include '../includes/footer.php'; ?>
