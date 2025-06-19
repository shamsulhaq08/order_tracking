<?php
header('Content-Type: application/json');
ob_start(); // Start output buffering

require 'db_config.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
error_reporting(E_ALL);

// Get the order ID and validate
$orderId = $_GET['order_id'] ?? '';
if (empty($orderId)) {
    ob_end_clean(); // discard any output
    echo json_encode(['error' => 'No order ID provided']);
    exit;
}

if (!is_numeric($orderId)) {
    ob_end_clean();
    echo json_encode(['error' => 'Invalid order ID format']);
    exit;
}

try {
$stmt = $conn->prepare("SELECT 
    orp.payment_date, 
    orp.payment_type, 
    orp.amount, 
    ba.account_name AS bank_detail_name, 
    orp.ac_detail, 
    orp.card_detail, 
    orp.transaction_id 
FROM order_remaining_payments orp
LEFT JOIN bank_accounts ba ON orp.bank_detail = ba.id
WHERE orp.order_id = ?");

    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param('i', $orderId);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $payments = [];

    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    ob_end_clean(); // clear output buffer before sending JSON
    
    if (empty($payments)) {
        echo json_encode(['message' => 'No payments found for this order']);
    } else {
        echo json_encode($payments);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// No closing PHP tag to avoid trailing whitespace
