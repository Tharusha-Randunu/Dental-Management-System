<?php
include '../../../config/db.php';

if (!isset($_GET['id'])) {
    die("No file ID specified.");
}

$file_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT file_name, file_type, file_data FROM test_files WHERE file_id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $type, $data);

if ($stmt->num_rows > 0) {
    $stmt->fetch();
    header("Content-Type: " . $type);
    header("Content-Disposition: attachment; filename=\"" . $name . "\"");
    echo $data;
} else {
    echo "File not found.";
}
$stmt->close();
?>
