<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_name = $_POST['test_name'];
    $cost = $_POST['cost'];
    $sample_type = $_POST['sample_type'];

    $sql = "INSERT INTO test_types (test_name, cost, sample_type) 
            VALUES ('$test_name', '$cost', '$sample_type')";

    if ($conn->query($sql) === TRUE) {
        $message = "Test type added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<div class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="text-center text-primary">Add New Test Type</h2>

        <!-- Message -->
        <?php if (isset($message)) { ?>
            <div class="alert alert-info">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <!-- Test Type Add Form -->
        <form method="POST" action="add_test_type.php">
            <div class="mb-3">
                <label for="test_name" class="form-label">Test Name</label>
                <input type="text" name="test_name" id="test_name" class="form-control" required maxlength="100">
            </div>
            <div class="mb-3">
                <label for="cost" class="form-label">Price (LKR)</label>
                <input type="number" name="cost" id="cost" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="sample_type" class="form-label">Sample Type</label>
                <input type="text" name="sample_type" id="sample_type" class="form-control" required maxlength="100">
            </div>

            <div class="d-flex justify-content-between">
                <a href="test_types_management.php" class="btn btn-danger"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Add Test Type</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
