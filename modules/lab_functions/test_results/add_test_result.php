<?php
include '../../../includes/header.php';
include '../../../config/db.php';

// Fetch test requests to populate the dropdown
$testRequests = $conn->query("
    SELECT tr.test_id, p.Fullname AS patient_name
    FROM test_requests tr
    JOIN patients p ON tr.patient_nic = p.NIC
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $test_id = $_POST['test_id'];
    $notes = $_POST['notes'];

    // Get patient NIC from test_requests
    $stmt = $conn->prepare("SELECT patient_nic FROM test_requests WHERE test_id = ?");
    $stmt->bind_param("i", $test_id);
    $stmt->execute();
    $stmt->bind_result($patient_nic);
    $stmt->fetch();
    $stmt->close();

    // Insert into test_results
    $stmt = $conn->prepare("INSERT INTO test_results (test_id, patient_nic, notes) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $test_id, $patient_nic, $notes);
    $stmt->execute();
    $result_id = $stmt->insert_id;
    $stmt->close();

    // Handle file uploads
    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['attachments']['name'][$key];
            $file_type = $_FILES['attachments']['type'][$key];
            $file_data = file_get_contents($tmp_name);

            $stmt = $conn->prepare("INSERT INTO test_files (result_id, file_name, file_type, file_data) VALUES (?, ?, ?, ?)");
            $null = NULL;
            $stmt->bind_param("issb", $result_id, $file_name, $file_type, $null);
            $stmt->send_long_data(3, $file_data);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "<div class='alert alert-success'>Test result added successfully!</div>";
}
?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h2 class="text-primary text-center">Add Test Result</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="test_id" class="form-label">Select Test</label>
                <select name="test_id" id="test_id" class="form-select" required>
                    <option value="">-- Select Test --</option>
                    <?php while ($row = $testRequests->fetch_assoc()): ?>
                        <option value="<?= $row['test_id'] ?>">Test ID: <?= $row['test_id'] ?> - <?= htmlspecialchars($row['patient_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="4" required ></textarea>
            </div>

            <div class="mb-3">
                <label for="attachments" class="form-label">Attachments (Images/Files)</label>
                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                <div class="form-text">You can select multiple files. Allowed: PDF, JPG, PNG, DOCX, etc.</div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="test_result_management.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Result</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>
