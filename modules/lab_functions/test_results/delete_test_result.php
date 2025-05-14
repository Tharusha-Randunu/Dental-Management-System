<?php
include '../../../config/db.php';

if (isset($_GET['id'])) {
    $result_id = intval($_GET['id']);

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Delete related files first
        $deleteFiles = $conn->prepare("DELETE FROM test_files WHERE result_id = ?");
        $deleteFiles->bind_param("i", $result_id);
        $deleteFiles->execute();

        // Delete the test result
        $deleteResult = $conn->prepare("DELETE FROM test_results WHERE result_id = ?");
        $deleteResult->bind_param("i", $result_id);
        $deleteResult->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid ID."]);
}
?>
