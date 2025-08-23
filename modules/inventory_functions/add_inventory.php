<?php
include '../../includes/header.php';
include '../../config/db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $reorder_level = $_POST['reorder_level'];
    $unit_price = $_POST['unit_price'];
    $supplier_name = $_POST['supplier_name'];
    $supplier_code = $_POST['supplier_code'];  
    $purchase_date = $_POST['purchase_date'];
    $expiry_date = $_POST['expiry_date'];
    $notes = $_POST['notes'];
    $total_value = $quantity * $unit_price;

    $sql = "INSERT INTO inventory (item_id, item_name, category, quantity, unit, reorder_level, unit_price, supplier_name, supplier_code, total_value, purchase_date, expiry_date, notes) 
            VALUES ('$item_id', '$item_name', '$category', '$quantity', '$unit', '$reorder_level', '$unit_price', '$supplier_name', '$supplier_code', '$total_value', '$purchase_date', '$expiry_date', '$notes')";

    if ($conn->query($sql) === TRUE) {
        $message = "Inventory item added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Dropdown options
$categories = ['Medicine', 'Dental Tools', 'Consumables', 'Equipment', 'Other'];
$units = ['Pieces', 'Boxes', 'Bottles', 'Liters', 'Kilograms', 'Packs'];
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add Inventory Item</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST" action="add_inventory.php">
            <div class="mb-3">
                <label for="item_id" class="form-label">Item ID</label>
                <input type="text" name="item_id" id="item_id" class="form-control" required maxlength="6">
            </div>
            <div class="mb-3">
                <label for="item_name" class="form-label">Item Name</label>
                <input type="text" name="item_name" id="item_name" class="form-control" required maxlength="150">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="" disabled selected>-- Select Category --</option>
                    <?php foreach ($categories as $cat) { ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="unit" class="form-label">Unit</label>
                <select name="unit" id="unit" class="form-control" required>
                    <option value="" disabled selected>-- Select Unit --</option>
                    <?php foreach ($units as $u) { ?>
                        <option value="<?php echo $u; ?>"><?php echo $u; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="reorder_level" class="form-label">Reorder Level</label>
                <input type="number" name="reorder_level" id="reorder_level" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="unit_price" class="form-label">Unit Price</label>
                <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="supplier_name" class="form-label">Supplier Name</label>
                <input type="text" name="supplier_name" id="supplier_name" class="form-control" required maxlength="150">
            </div>
            <div class="mb-3">
                <label for="supplier_code" class="form-label">Supplier Code</label> <!-- Added supplier code input -->
                <input type="text" name="supplier_code" id="supplier_code" class="form-control" required maxlength="6">
            </div>
            <div class="mb-3">
                <label for="purchase_date" class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" id="purchase_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" class="form-control">
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <a href="../inventory_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success ms-2"><i class="bi bi-check-lg"></i> Add Item</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
