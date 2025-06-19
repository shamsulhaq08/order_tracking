<?php
require 'db_config.php';
require 'fpdf/fpdf.php';

$order_id = $_POST['order_id'] ?? $_GET['order_id'] ?? null;
if (!$order_id) die("Missing order ID.");

$order_id = (int)$order_id;

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Order not found.");
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

class PDF extends FPDF {
    function Header() {
    $leftLogo = __DIR__ . '/assets/images/logo.jpg';
    $rightLogo = __DIR__ . '/assets/images/charagh_logo.jpg';

    $logoY = 10;
    $logoWidth = 30;

    // Left logo
    $this->Image($leftLogo, 10, $logoY, $logoWidth);

    // Right logo
    $this->Image($rightLogo, 170, $logoY, $logoWidth);

    // Center text (placed after logos are rendered)
    $this->SetXY(10, $logoY + 12); // Move cursor to avoid image
    $this->SetFont('Arial', '', 10);
    $this->SetTextColor(0);

    // Center within printable width (210 - 20 = 190, centered at 105)
    $this->SetX(0); // Reset X so Cell(0,...) spans the full page
    $this->Cell(210, 10, 'Elegant Events, Priceless Memories | Event Management & Party Supplies', 0, 0, 'C');

    $this->Ln(20); // Push content down after header

    }

    function Footer() {
        $this->SetY(-30);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(100);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        $this->MultiCell(0, 5,
            "Whatsapp: 061-6222637 | Website: www.chapter2.pk\n" .
            "Address: 18-City Mall MCB Bank Road Near Zavia School System Goal Bagh,\n" .
            "Gulgasht Colony, Multan.",
            0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 12, 'Order Details', 0, 1, 'C');
$pdf->Ln(3);

$orderDate = date('l d/F/Y', strtotime($order['order_date']));
$deliveryDate = date('l d/F/Y', strtotime($order['delivery_date']));
$deliveryTime = date('h:i A', strtotime($order['order_time']));

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'INVOICE NO:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(55, 10, $order['id'], 'B', 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'ORDER DATE:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(55, 10, $orderDate, 'B', 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'DELIVERY DATE:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(55, 10, $deliveryDate, 'B', 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'DELIVERY TIME:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(55, 10, $deliveryTime, 'B', 1);

$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 10, 'CUSTOMER NAME', 1, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(150, 10, $order['customer_name'], 1, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 10, 'ADDRESS', 1, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(150, 10, $order['customer_address'], 1, 1);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(40, 10, 'PHONE', 1, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(55, 10, $order['contact'], 1, 0);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(25, 10, 'WHATSAPP', 1, 0);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(70, 10, $order['whatsapp_number'], 1, 1);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, '  ORDER DESCRIPTION', 1, 1, 'C');
$pdf->SetFont('Arial', '', 11);

$descStartY = $pdf->GetY();
$descHeight = 88;
$pdf->MultiCell(190, 9, '  ' . strip_tags($order['description']), 0);
$descEndY = $pdf->GetY();
$actualHeight = $descEndY - $descStartY;

if ($actualHeight < $descHeight) {
    $pdf->SetY($descStartY + $actualHeight);
    $pdf->Cell(190, $descHeight - $actualHeight, '', 0, 1);
}

$pdf->Rect(10, $descStartY, 190, $descHeight);
$pdf->Ln(3);

// Set table widths
$leftTableWidth = 95;
$rightTableWidth = 95;
$rowHeight = 9;

// Starting X positions
$leftX = 10;
$rightX = $leftX + $leftTableWidth;

// Start Y position
$startY = $pdf->GetY();

// === Table Headers (No Fill Color) ===
$pdf->SetFont('Arial', 'B', 12);

// Price Detail Header
$pdf->SetXY($leftX, $startY);
$pdf->Cell($leftTableWidth, 10, '  Price Detail', 1, 0, 'L', false);

// Reference Image Header
$pdf->SetXY($rightX, $startY);
$pdf->Cell($rightTableWidth, 10, '  Reference Image', 1, 1, 'L', false);

// === Table Rows ===
$pdf->SetFont('Arial', '', 11);
$priceFields = [
    'Total'     => $order['total'],
    'Advance'   => $order['advance'],
    'Remaining' => $order['remaining'],
    
];

$currentY = $pdf->GetY(); // Start below header
foreach ($priceFields as $label => $value) {
    // Left table: Price rows
    $pdf->SetXY($leftX, $currentY);
    $pdf->Cell($leftTableWidth / 2, $rowHeight, '  ' . $label, 1, 0, 'L');
    $pdf->Cell($leftTableWidth / 2, $rowHeight, number_format($value, 2) . '  ', 1, 0, 'R');

    // Right table: Just maintain layout without borders or fill
    $pdf->SetXY($rightX, $currentY);
    $pdf->Cell($rightTableWidth, $rowHeight, '', 0, 1); // No border

    $currentY += $rowHeight;
}

// === Reference Image (No Box) ===
$imageY = $currentY - ($rowHeight * count($priceFields)); // Align image to start of rows
$imageX = $rightX + 5;
$imageW = 50;
$imageH = 30;

$pdf->Ln(3);

$mediaField = trim($order['file_media']);
$mediaFiles = array_filter(array_map('trim', explode(',', $mediaField)));
$imagePlaced = false;

if (!empty($mediaFiles)) {
    foreach ($mediaFiles as $media) {
        $mediaPath = __DIR__ . '/uploads/' . ltrim($media, '/\\');
        if (file_exists($mediaPath) && @getimagesize($mediaPath)) {
            $pdf->Image($mediaPath, $imageX, $imageY, $imageW, $imageH);
            $imagePlaced = true;
            break;
        }
    }
}

if (!$imagePlaced) {
    $pdf->SetXY($rightX, $imageY + 5);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->MultiCell($rightTableWidth, 8, 'No media file provided.', 0, 'C');
}


$pdf->Output("D", "Order_{$order['id']}.pdf");