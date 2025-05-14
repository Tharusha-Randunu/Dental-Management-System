<?php
require '../../vendor/autoload.php'; // Adjust path if needed
use Dompdf\Dompdf;
use Dompdf\Options;
include '../../config/db.php';

// Get the bill ID from URL
if (!isset($_GET['id'])) {
    die("Bill ID missing");
}
$bill_id = $_GET['id'];

// Fetch the bill details
$sql = "SELECT * FROM bills WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Bill not found");
}
$bill = $result->fetch_assoc();

// HTML content for the PDF
$html = '
<h2 style="text-align: center; color: navy;">Dental Center Bill</h2>
<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr><th>Bill ID</th><td>' . $bill['bill_id'] . '</td></tr>
    <tr><th>NIC</th><td>' . $bill['NIC'] . '</td></tr>
    <tr><th>Total Amount</th><td>Rs. ' . number_format($bill['total_amount'], 2) . '</td></tr>
    <tr><th>Discount</th><td>Rs. ' . number_format($bill['discount'], 2) . '</td></tr>
    <tr><th>Tax</th><td>Rs. ' . number_format($bill['tax'], 2) . '</td></tr>
    <tr><th>Grand Total</th><td>Rs. ' . number_format($bill['grand_total'], 2) . '</td></tr>
    <tr><th>Amount Paid</th><td>Rs. ' . number_format($bill['amount_paid'], 2) . '</td></tr>
    <tr><th>Amount Remaining</th><td>Rs. ' . number_format($bill['amount_remaining'], 2) . '</td></tr>
    <tr><th>Payment Status</th><td>' . $bill['payment_status'] . '</td></tr>
    <tr><th>Date</th><td>' . $bill['created_at'] . '</td></tr>
</table>
<p style="text-align:center; margin-top:30px;">Thank you for choosing our services!</p>
';

// Setup DOMPDF
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render and stream PDF
$dompdf->render();
$dompdf->stream("Bill_{$bill['bill_id']}.pdf", ["Attachment" => false]); // Show in browser
exit;
?>
