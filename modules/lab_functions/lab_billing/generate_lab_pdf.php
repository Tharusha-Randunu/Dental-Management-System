<?php
require '../../../vendor/autoload.php';  
use Dompdf\Dompdf;
use Dompdf\Options;
include '../../../config/db.php';

// Check for bill ID in URL
if (!isset($_GET['id'])) {
    die("Lab Bill ID missing");
}
$bill_id = intval($_GET['id']);

// Fetch main lab bill with patient info
$sql = "
    SELECT b.*, p.Fullname 
    FROM lab_bills b
    JOIN patients p ON b.patient_nic = p.NIC
    WHERE b.bill_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Lab bill not found");
}
$bill = $result->fetch_assoc();

// Fetch test types
$test_sql = "
    SELECT t.test_name, t.cost 
    FROM lab_bill_items i
    JOIN test_types t ON i.test_type_id = t.test_type_id
    WHERE i.bill_id = ?
";
$test_stmt = $conn->prepare($test_sql);
$test_stmt->bind_param("i", $bill_id);
$test_stmt->execute();
$test_result = $test_stmt->get_result();

$test_list_html = '';
while ($row = $test_result->fetch_assoc()) {
    $test_list_html .= "<li>" . htmlspecialchars($row['test_name']) . " (Rs. " . number_format($row['cost'], 2) . ")</li>";
}
$test_stmt->close();

// Build PDF HTML content
$html = '
<h2 style="text-align: center; color: navy;">Dental Hub - Lab Bill</h2>
<table border="1" cellspacing="0" cellpadding="10" width="100%">
    <tr><th>Bill ID</th><td>' . $bill['bill_id'] . '</td></tr>
    <tr><th>Patient NIC</th><td>' . $bill['patient_nic'] . '</td></tr>
    <tr><th>Patient Name</th><td>' . htmlspecialchars($bill['Fullname']) . '</td></tr>
    <tr><th>Total Amount</th><td>Rs. ' . number_format($bill['total_amount'], 2) . '</td></tr>
    <tr><th>Discount</th><td>Rs. ' . number_format($bill['discount'], 2) . '</td></tr>
    <tr><th>Tax</th><td>Rs. ' . number_format($bill['tax'], 2) . '</td></tr>
    <tr><th>Grand Total</th><td>Rs. ' . number_format($bill['grand_total'], 2) . '</td></tr>
    <tr><th>Amount Paid</th><td>Rs. ' . number_format($bill['amount_paid'], 2) . '</td></tr>
    <tr><th>Amount Remaining</th><td>Rs. ' . number_format($bill['amount_remaining'], 2) . '</td></tr>
    <tr><th>Payment Status</th><td>' . htmlspecialchars($bill['payment_status']) . '</td></tr>
    <tr><th>Created At</th><td>' . $bill['created_at'] . '</td></tr>
    <tr><th>Test Types</th><td><ul>' . $test_list_html . '</ul></td></tr>
</table>
<p style="text-align:center; margin-top:30px;">Thank you for choosing our laboratory!</p>
';

// DOMPDF setup
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF to browser
$dompdf->stream("Lab_Bill_{$bill['bill_id']}.pdf", ["Attachment" => false]);
exit;
?>
