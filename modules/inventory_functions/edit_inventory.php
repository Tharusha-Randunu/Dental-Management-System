<?php
include '../../includes/header.php';
include '../../includes/sidebar.php';
include '../../config/db.php';

// Get the composite key from the URL
if (!isset($_GET['id']) || !isset($_GET['supplier_code']) || empty($_GET['id']) || empty($_GET['supplier_code'])) {
    die("Error: Item ID or Supplier Code not specified.");
}

$item_id = $_GET['id'];
$supplier_code = $_GET['supplier_code'];

// Fetch item details from the database
$sql = "SELECT * FROM inventory WHERE item_id = '$item_id' AND supplier_code = '$supplier_code'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
} else {
    $message = "Item not found!";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $reorder_level = $_POST['reorder_level'];
    $unit_price = $_POST['unit_price'];
    $supplier_name = $_POST['supplier_name'];
    $purchase_date = $_POST['purchase_date'];
    $expiry_date = $_POST['expiry_date'];
    $notes = $_POST['notes'];
    $total_value = $quantity * $unit_price;

    $update_sql = "UPDATE inventory 
                   SET item_name = '$item_name', 
                       category = '$category', 
                       quantity = '$quantity', 
                       unit = '$unit', 
                       reorder_level = '$reorder_level', 
                       unit_price = '$unit_price', 
                       supplier_name = '$supplier_name', 
                       purchase_date = '$purchase_date', 
                       expiry_date = '$expiry_date', 
                       notes = '$notes', 
                       total_value = '$total_value' 
                   WHERE item_id = '$item_id' AND supplier_code = '$supplier_code'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                });
              </script>";
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit Inventory Item</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning"><?php echo $message; ?></div>
        <?php } ?>

        <?php if ($result->num_rows > 0) { ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Item ID</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($item['item_id']); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Supplier Code</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($item['supplier_code']); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-control" value="<?php echo htmlspecialchars($item['item_name']); ?>" required maxlength="150">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($item['category']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" id="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" required oninput="calculateTotalValue();">
                </div>

                <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($item['unit']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reorder Level</label>
                    <input type="number" name="reorder_level" class="form-control" value="<?php echo htmlspecialchars($item['reorder_level']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Unit Price</label>
                    <input type="number" name="unit_price" class="form-control" id="unit_price" value="<?php echo htmlspecialchars($item['unit_price']); ?>" step="0.01" required oninput="calculateTotalValue();">
                </div>

                <div class="mb-3">
                    <label class="form-label">Supplier Name</label>
                    <input type="text" name="supplier_name" class="form-control" value="<?php echo htmlspecialchars($item['supplier_name']); ?>" required maxlength="150">
                </div>

                <div class="mb-3">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control" value="<?php echo htmlspecialchars($item['purchase_date']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" value="<?php echo htmlspecialchars($item['expiry_date']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" required><?php echo htmlspecialchars($item['notes']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Total Value</label>
                    <input type="text" class="form-control" id="total_value" name="total_value" value="<?php echo htmlspecialchars($item['total_value']); ?>" readonly>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../inventory_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Changes have been successfully saved!</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateTotalValue() {
        var quantity = parseFloat(document.getElementById("quantity").value) || 0;
        var unitPrice = parseFloat(document.getElementById("unit_price").value) || 0;
        var totalValue = quantity * unitPrice;
        document.getElementById("total_value").value = totalValue.toFixed(2);
    }
</script>

<?php include '../../includes/footer.php'; ?>
