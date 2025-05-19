<?php
require '../../vendor/autoload.php'; // Adjust path if needed
use Dompdf\Dompdf;
use Dompdf\Options;
include '../../config/db.php';

// Check for bill ID
if (!isset($_GET['id'])) {
    die("Dental Bill ID missing");
}
$bill_id = intval($_GET['id']);

// Fetch bill with appointment info
$sql = "
    SELECT b.*, a.patient_nic, a.patient_name, a.dentist_code, a.dentist_name 
    FROM bills b
    JOIN appointments a ON b.appointment_id = a.appointment_id AND b.appointment_date = a.appointment_date
    WHERE b.bill_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Dental bill not found");
}
$bill = $result->fetch_assoc();
$stmt->close();

// Build HTML
$html = '
<h2 style="text-align: center; color: navy;">Dental Hub - Dental Bill</h2>
<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr><th>Bill ID</th><td>' . $bill['bill_id'] . '</td></tr>
    <tr><th>Appointment ID</th><td>' . $bill['appointment_id'] . '</td></tr>
    <tr><th>Appointment Date</th><td>' . htmlspecialchars($bill['appointment_date']) . '</td></tr>
    <tr><th>Patient NIC</th><td>' . htmlspecialchars($bill['patient_nic']) . '</td></tr>
    <tr><th>Patient Name</th><td>' . htmlspecialchars($bill['patient_name']) . '</td></tr>
    <tr><th>Dentist Code</th><td>' . htmlspecialchars($bill['dentist_code']) . '</td></tr>
    <tr><th>Dentist Name</th><td>' . htmlspecialchars($bill['dentist_name']) . '</td></tr>
    <tr><th>Notes</th><td>' . nl2br(htmlspecialchars($bill['notes'])) . '</td></tr>
    <tr><th>Total Amount</th><td>Rs. ' . number_format($bill['total_amount'], 2) . '</td></tr>
    <tr><th>Discount</th><td>Rs. ' . number_format($bill['discount'], 2) . '</td></tr>
    <tr><th>Tax</th><td>Rs. ' . number_format($bill['tax'], 2) . '</td></tr>
    <tr><th>Grand Total</th><td>Rs. ' . number_format($bill['grand_total'], 2) . '</td></tr>
    <tr><th>Amount Paid</th><td>Rs. ' . number_format($bill['amount_paid'], 2) . '</td></tr>
    <tr><th>Amount Remaining</th><td>Rs. ' . number_format($bill['amount_remaining'], 2) . '</td></tr>
    <tr><th>Payment Status</th><td>' . htmlspecialchars($bill['payment_status']) . '</td></tr>
    <tr><th>Created At</th><td>' . htmlspecialchars($bill['created_at']) . '</td></tr>
</table>
<p style="text-align:center; margin-top:30px;">Thank you for trusting Dental Hub!</p>
';

// Initialize DOMPDF
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output
$dompdf->stream("Dental_Bill_{$bill['bill_id']}.pdf", ["Attachment" => false]);
exit;
?>
