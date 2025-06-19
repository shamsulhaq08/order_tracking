<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

// DB Connection
$mysqli = new mysqli("localhost", "root", "12345", "order_tracking");
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $mysqli->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing ID or status']);
    exit;
}

$id = (int)$data['id'];
$status = $mysqli->real_escape_string($data['status']);

// Validate status
$allowed_statuses = ['Processing', 'Assigned', 'Completed', 'Delivered', 'Cancelled'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Helper to get current remaining
function getCurrentRemaining($mysqli, $id) {
    $stmt = $mysqli->prepare("SELECT remaining FROM orders WHERE id = ?");
    if (!$stmt) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($remaining);
    $res = $stmt->fetch() ? $remaining : null;
    $stmt->close();
    return $res;
}

// Calculate remaining
if ($status === 'Delivered' || $status === 'Completed') {
    $remaining = isset($data['remaining']) ? floatval($data['remaining']) : getCurrentRemaining($mysqli, $id);
    if ($remaining === null) $remaining = 0.0;
} else {
    $remaining = getCurrentRemaining($mysqli, $id);
    if ($remaining === null) $remaining = 0.0;
}

// Update order
$stmt = $mysqli->prepare("UPDATE orders SET status = ?, remaining = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $mysqli->error]);
    exit;
}
$stmt->bind_param("sdi", $status, $remaining, $id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
    $stmt->close();
    $mysqli->close();
    exit;
}
$stmt->close();

if (($status === 'Delivered' || $status === 'Completed') && !empty($data['payments']) && is_array($data['payments'])) {
    foreach ($data['payments'] as $payment) {
        $payment_type = $mysqli->real_escape_string($payment['payment_type'] ?? '');
        $cash_payment = isset($payment['cash_payment']) ? floatval($payment['cash_payment']) : 0.0;
        $card_amount = isset($payment['card_amount']) ? floatval($payment['card_amount']) : 0.0;
        $online_amount = isset($payment['amount']) ? floatval($payment['amount']) : 0.0; // You can also check specific fields if needed

        $bank_detail = $mysqli->real_escape_string($payment['bank_detail'] ?? '');
        $ac_detail = $mysqli->real_escape_string($payment['ac_detail'] ?? '');
        $card_detail = $mysqli->real_escape_string($payment['card_detail'] ?? '');
        $transaction_id = $mysqli->real_escape_string($payment['transaction_id'] ?? '');
        $pos_bank_detail = $mysqli->real_escape_string($payment['pos_bank_detail'] ?? '');

        $amount = $cash_payment + $card_amount + $online_amount;

        if ($amount > 0) {
            $stmt_payment = $mysqli->prepare("INSERT INTO order_remaining_payments 
                (order_id, payment_type, amount, bank_detail, ac_detail, card_detail, transaction_id, payment_date, card_amount, cash_payment, pos_bank_detail) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)");

            if (!$stmt_payment) {
                echo json_encode(['success' => false, 'message' => "Payment insert prepare failed: " . $mysqli->error]);
                $mysqli->close();
                exit;
            }

            $stmt_payment->bind_param(
                "issssssdds",
                $id,
                $payment_type,
                $amount,
                $bank_detail,
                $ac_detail,
                $card_detail,
                $transaction_id,
                $card_amount,
                $cash_payment,
                $pos_bank_detail
            );

            if (!$stmt_payment->execute()) {
                echo json_encode(['success' => false, 'message' => "Payment insert failed: " . $stmt_payment->error]);
                $stmt_payment->close();
                $mysqli->close();
                exit;
            }

            $stmt_payment->close();
        }
    }
}


$mysqli->close();
echo json_encode(['success' => true, 'remaining_used' => $remaining]);
