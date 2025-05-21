<?php
include '../../../includes/header.php';
include '../../../includes/sidebar.php';
include '../../../config/db.php';

// Check if result_id is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>No test result ID provided.</div>";
    exit;
}

$result_id = $_GET['id'];

// Fetch test result details
$stmt = $conn->prepare("
    SELECT r.*, p.Fullname AS patient_name, tr.test_id, tt.test_name
    FROM test_results r
    JOIN test_requests tr ON r.test_id = tr.test_id
    JOIN test_types tt ON tr.test_type_id = tt.test_type_id
    JOIN patients p ON r.patient_nic = p.NIC
    WHERE r.result_id = ?
");
$stmt->bind_param("i", $result_id);
$stmt->execute();
$result = $stmt->get_result();
$testResult = $result->fetch_assoc();
$stmt->close();

if (!$testResult) {
    echo "<div class='alert alert-danger'>Test result not found.</div>";
    exit;
}

// Fetch attachments
$stmt = $conn->prepare("SELECT * FROM test_files WHERE result_id = ?");
$stmt->bind_param("i", $result_id);
$stmt->execute();
$filesResult = $stmt->get_result();
?>

<div class="container mt-4">
    <div class="card shadow p-4">
        <h2 class="text-primary text-center">Test Result Details</h2>

        <?php
$backUrl = 'test_result_management.php';
if (isset($_GET['from']) && $_GET['from'] === 'view_patient' && isset($testResult['patient_nic'])) {
    $backUrl = '../../patient_functions/view_patient.php?nic=' . urlencode($testResult['patient_nic']);
}
?>
<div class="d-flex justify-content-end">
    <a href="<?= $backUrl ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

        <div class="mb-3">
            <strong>Test ID:</strong> <?= htmlspecialchars($testResult['test_id']) ?><br>
            <strong>Test Type:</strong> <?= htmlspecialchars($testResult['test_name']) ?><br>
            <strong>Patient:</strong> <?= htmlspecialchars($testResult['patient_name']) ?><br>
            <strong>Patient NIC:</strong> <?= htmlspecialchars($testResult['patient_nic']) ?><br>
            <strong>Notes:</strong>
            <p><?= nl2br(htmlspecialchars($testResult['notes'])) ?></p>
        </div>

        <div class="mb-4">
            <h5 class="text-success">Attachments</h5>
            <?php if ($filesResult->num_rows > 0): ?>
                <div class="row">
                    <?php while ($file = $filesResult->fetch_assoc()): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <?php
                                $file_type = $file['file_type'];
                                $is_image = strpos($file_type, 'image') === 0;

                                // For direct preview of images
                                if ($is_image) {
                                    $base64 = base64_encode($file['file_data']);
                                    echo "<img src='data:$file_type;base64,$base64' class='card-img-top' style='max-height:200px; object-fit:contain;'>";
                                }
                                ?>
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($file['file_name']) ?></h6>
                                    <a href="download_file.php?id=<?= $file['file_id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-download"></i> Download</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No attachments found.</p>
            <?php endif; ?>
        </div>

       


            
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>





