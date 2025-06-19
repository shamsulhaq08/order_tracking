<?php
require 'db_config.php';
require 'fpdf/fpdf.php';

if (!isset($_POST['order_id'])) {
    die("Missing order ID.");
}

$order_id = (int)$_POST['order_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Order not found.");
}
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Order Receipt');

// Add order details...
$pdf->SetFont('Arial','',12);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Order ID: ' . $order['id'], 0, 1);
// ... more fields as you want

// Define file path to save PDF
$save_path = __DIR__ . "/uploads/order_" . $order['id'] . ".pdf";

// Save PDF to server
$pdf->Output('F', $save_path);

echo "PDF saved successfully.";
?>
