<?php
include '../../config/db.php';

// Check if the appointment_id and appointment_date are passed in the URL
if (isset($_GET['id']) && isset($_GET['date'])) {
    $appointment_id = $_GET['id'];
    $appointment_date = $_GET['date'];

    // Prepare the SQL query to delete the appointment
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ? AND appointment_date = ?");
    $stmt->bind_param("ss", $appointment_id, $appointment_date);

    if ($stmt->execute()) {
        // Redirect to the appointments page with a success message
        header("Location: ../appointment_scheduling.php?delete=success");
        exit();
    } else {
        // Redirect to the appointments page with an error message
        header("Location: ../appointment_scheduling.php?delete=error");
        exit();
    }
} else {
    // If ID or date is not provided, redirect back to appointments page with an error message
    header("Location: ../appointment_scheduling.php?delete=invalid");
    exit();
}
?>
