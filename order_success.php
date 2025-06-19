<?php
require 'db_config.php';  // Your DB connection file

if (!isset($_GET['id'])) {
    die("Missing order ID.");
}
$order_id = (int)$_GET['id'];

// Fetch order from DB
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

// Your local IP or public IP/domain for generating URLs
$host_ip = "192.168.1.6";  // CHANGE THIS to your machine IP

// Absolute path for checking if PDF file exists on server
$pdf_file = __DIR__ . "/uploads/order_" . $order['id'] . ".pdf";

// URL to access the PDF via browser
$pdf_url = "http://" . $host_ip . "/order_tracking/uploads/order_" . $order['id'] . ".pdf";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Submitted</title>
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .container {
            background: #fff;
            max-width: 480px;
            margin: 40px auto;
            border-radius: 16px;
            box-shadow: 0 6px 32px rgba(0,0,0,0.12);
            padding: 36px 32px 28px 32px;
            border: 2px solid #e3f2fd;
        }
        h2 {
            color: #43a047;
            margin-bottom: 18px;
            font-size: 2em;
            letter-spacing: 1px;
            text-align: center;
            text-shadow: 0 2px 8px #e0f7fa;
        }
        p {
            margin: 10px 0;
            font-size: 1.12em;
            text-align: center;
        }
        strong {
            color: #1976d2;
        }
        .form-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 28px;
            gap: 18px;
        }
        .btn-main {
            background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 1.08em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            box-shadow: 0 4px 16px rgba(0, 123, 255, 0.13);
            margin-bottom: 0;
        }
        .btn-main:hover {
            background: linear-gradient(90deg, #0056b3 0%, #00b8d4 100%);
            transform: translateY(-2px) scale(1.04);
        }
        .btn-back {
            background: linear-gradient(90deg, #ff9800 0%, #ffc107 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-size: 1.08em;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            box-shadow: 0 4px 16px rgba(255, 193, 7, 0.13);
        }
        .btn-back:hover {
            background: linear-gradient(90deg, #f57c00 0%, #ffb300 100%);
            transform: translateY(-2px) scale(1.04);
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>✅ Order Submitted Successfully</h2>
    <p><strong>Order Invoice ID:</strong> <?= htmlspecialchars($order['id']) ?></p>
    <p>
        <?php if (!empty($order['whatsapp_number'])): 
            $wa_number = preg_replace('/\D/', '', $order['whatsapp_number']); // Remove non-digits
            // Remove leading 0 if present and add +92
            if (strpos($wa_number, '0') === 0) {
                $wa_number = substr($wa_number, 1);
            }
            $wa_number = '92' . $wa_number;
            // Custom message for WhatsApp
            $wa_message = urlencode("Thank you for your order (Chapter 2)! Your order Invoice No# is " . $order['id'] . ".");
            $wa_link = "https://wa.me/" . $wa_number . "?text=" . $wa_message;
        ?>
            <a href="<?= htmlspecialchars($wa_link) ?>" target="_blank" style="margin-left:10px;">
                <button type="button" class="btn-main" style="padding:6px 18px;font-size:0.98em;">
                    💬 Chat on WhatsApp
                </button>
            </a>
        <?php endif; ?>
    </p>

    <div class="form-center">
        <form method="post" action="generate_pdf.php" target="pdfFrame" id="pdfForm">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
            <button type="submit" class="btn-main">📄 Generate & Download PDF</button>
        </form>
        <a href="order.php">
            <button type="button" class="btn-back">⬅️ Back</button>
        </a>
    </div>
    <iframe name="pdfFrame" style="display:none;"></iframe>
    <?php if (file_exists($pdf_file)): ?>
        <script>
            document.getElementById('pdfForm').addEventListener('submit', function(e) {
                // Wait for the PDF to be generated and downloaded, then go back
                setTimeout(function() {
                    history.back();
                }, 2000); // Wait 2 seconds for download to start
            });
        </script>
    <?php endif; ?>
</div>
</body>
</html>
