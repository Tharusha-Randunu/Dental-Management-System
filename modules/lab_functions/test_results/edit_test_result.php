<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>No test result ID provided.</div>";
    exit;
}

$result_id = intval($_GET['id']);

// Fetch existing test result
$stmt = $conn->prepare("SELECT * FROM test_results WHERE result_id = ?");
$stmt->bind_param("i", $result_id);
$stmt->execute();
$result = $stmt->get_result();
$testResult = $result->fetch_assoc();
$stmt->close();

if (!$testResult) {
    echo "<div class='alert alert-danger'>Test result not found.</div>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("UPDATE test_results SET notes = ? WHERE result_id = ?");
    $stmt->bind_param("si", $notes, $result_id);
    $stmt->execute();
    $stmt->close();

    // Handle file uploads
    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['tmp_name'] as $index => $tmpName) {
            $fileName = $_FILES['attachments']['name'][$index];
            $fileType = $_FILES['attachments']['type'][$index];
            $fileData = file_get_contents($tmpName);

            $stmt = $conn->prepare("INSERT INTO test_files (result_id, file_name, file_type, file_data) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $result_id, $fileName, $fileType, $fileData);
            $stmt->send_long_data(3, $fileData);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "
<!-- Success Modal -->
<div class='modal fade show' id='successModal' tabindex='-1' style='display:block; background:rgba(0,0,0,0.5);' aria-modal='true' role='dialog'>
  <div class='modal-dialog modal-dialog-centered'>
    <div class='modal-content'>
      <div class='modal-header bg-success text-white'>
        <h5 class='modal-title'>Success</h5>
      </div>
      <div class='modal-body'>
        <p>Test result updated successfully!</p>
        <p class='text-muted small'>Redirecting in 3 seconds...</p>
      </div>
      <div class='modal-footer'>
        <a href='view_test_result.php?id=$result_id' class='btn btn-primary'>Ok</a>
      </div>
    </div>
  </div>
</div>

<!-- Auto Redirect -->
<script>
    setTimeout(function() {
        window.location.href = 'view_test_result.php?id=$result_id';
    }, 3000);
</script>
";
exit;

}
?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h2 class="text-center text-primary">Edit Test Result</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="5" required><?= htmlspecialchars($testResult['notes']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="attachments" class="form-label">Upload Additional Files</label>
                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
            </div>

            <div class="d-flex justify-content-end">
                <a href="view_test_result.php?id=<?= $result_id ?>" class="btn btn-secondary me-2"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
