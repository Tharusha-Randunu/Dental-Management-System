<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Get test type ID from query string
if (!isset($_GET['id'])) {
    die("Test Type ID not provided.");
}
$id = $_GET['id'];

// Fetch existing data
$sql = "SELECT * FROM test_types WHERE test_type_id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
    die("Test Type not found.");
}

$row = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_name = $_POST['test_name'];
    $cost = $_POST['cost'];
    $sample_type = $_POST['sample_type'];

    $update_sql = "UPDATE test_types 
                   SET test_name = '$test_name', cost = '$cost', sample_type = '$sample_type' 
                   WHERE test_type_id = '$id'";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Test type updated successfully!";
        // Refresh the data after update
        $row = ['test_name' => $test_name, 'cost' => $cost, 'sample_type' => $sample_type];
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Edit Test Type</h2>

        <!-- Message -->
        <?php if (isset($message)) { ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php } ?>

        <!-- Edit Test Type Form -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="test_name" class="form-label">Test Name</label>
                <input type="text" name="test_name" id="test_name" class="form-control" 
                       value="<?php echo htmlspecialchars($row['test_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Price (LKR)</label>
                <input type="number" name="cost" id="cost" class="form-control" step="0.01"
                       value="<?php echo htmlspecialchars($row['cost']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="sample_type" class="form-label">Sample Type</label>
                <input type="text" name="sample_type" id="sample_type" class="form-control" 
                       value="<?php echo htmlspecialchars($row['sample_type']); ?>" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="test_types_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-pencil"></i> Update Test Type</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
