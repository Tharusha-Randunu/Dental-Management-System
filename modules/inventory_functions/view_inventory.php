<?php
include '../../includes/header.php';
include '../../config/db.php';

// Get the inventory item ID and supplier code from the URL
$item_id = $_GET['id'] ?? null;
$supplier_code = $_GET['supplier_code'] ?? null;

if (!$item_id || !$supplier_code) {
    $message = "Item ID or Supplier Code not specified.";
} else {
    // Fetch item details from the database using both item_id and supplier_code
    $sql = "SELECT item_id, item_name, category, quantity, unit, reorder_level, unit_price, supplier_name, supplier_code, total_value, purchase_date, expiry_date, notes 
            FROM inventory 
            WHERE item_id = '$item_id' AND supplier_code = '$supplier_code'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        $message = "Inventory item not found!";
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
    
        <h2 class="text-center text-primary">Inventory Item Details</h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-warning"><?php echo $message; ?></div>
        <?php } ?>

        <?php if (isset($item)) { ?>
            <!-- Back Button -->
            <div class="d-flex justify-content-start mb-3">
                <a href="../inventory_management.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </div>

            <!-- Inventory Item Details Table -->
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Item ID</th>
                        <td><?php echo htmlspecialchars($item['item_id']); ?></td>
                    </tr>
                    <tr>
                        <th>Item Name</th>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                    </tr>
                    <tr>
                        <th>Quantity</th>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    </tr>
                    <tr>
                        <th>Unit</th>
                        <td><?php echo htmlspecialchars($item['unit']); ?></td>
                    </tr>
                    <tr>
                        <th>Reorder Level</th>
                        <td><?php echo htmlspecialchars($item['reorder_level']); ?></td>
                    </tr>
                    <tr>
                        <th>Unit Price</th>
                        <td><?php echo htmlspecialchars($item['unit_price']); ?></td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td><?php echo htmlspecialchars($item['supplier_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Supplier Code</th>
                        <td><?php echo htmlspecialchars($item['supplier_code']); ?></td>
                    </tr>
                    <tr>
                        <th>Total Value</th>
                        <td><?php echo htmlspecialchars($item['total_value']); ?></td>
                    </tr>
                    <tr>
                        <th>Purchase Date</th>
                        <td><?php echo htmlspecialchars($item['purchase_date']); ?></td>
                    </tr>
                    <tr>
                        <th>Expiry Date</th>
                        <td><?php echo htmlspecialchars($item['expiry_date']); ?></td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td><?php echo htmlspecialchars($item['notes']); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
