<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    die("User not logged in.");
}

$order_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($order_id <= 0) die("Invalid order ID.");

// Fetch current order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$original_order = $result->fetch_assoc();
$stmt->close();

if (!$original_order) die("Order not found.");

// Handle media file upload as before
$file_media = $original_order['file_media'];
if (!empty($_FILES['file_media']['name'][0])) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $uploaded_files = [];
    foreach ($_FILES['file_media']['name'] as $key => $name) {
        if ($_FILES['file_media']['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
            $tmp_name = $_FILES['file_media']['tmp_name'][$key];
            $unique_name = uniqid('media_', true) . '_' . basename($name);
            $target_path = $upload_dir . $unique_name;
            if (move_uploaded_file($tmp_name, $target_path)) {
                $uploaded_files[] = $unique_name;
            }
        }
    }
    $file_media = implode(',', array_merge(array_filter(explode(',', $file_media)), $uploaded_files));
}

// Normalize helper function
function normalizeText($value) {
    $value = mb_convert_encoding((string)$value, 'UTF-8', 'UTF-8'); // ensure encoding
    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); // decode HTML entities
    $value = str_replace(["\r", "\n", "\t"], ' ', $value); // remove newlines/tabs
    $value = preg_replace('/\s+/', ' ', $value); // collapse multiple spaces
    return trim($value);
}

// Define all fields
$fields = [
    'order_date' => $_POST['date'] ?? '',
    'delivery_date' => $_POST['delivery_date'] ?? '',
    'order_time' => $_POST['time'] ?? '',
    'customer_name' => $_POST['name'] ?? '',
    'contact' => $_POST['contact'] ?? '',
    'customer_address' => $_POST['customer_address'] ?? '',
    'order_maker_id' => $_POST['order_maker_id'] ?? '',
    'order_source' => $_POST['source'] ?? '',
    'source_other_text' => $_POST['source_other_text'] ?? '',
    'payment' => isset($_POST['payment']) ? implode(', ', $_POST['payment']) : '',
    'bank_detail' => $_POST['bank_detail'] ?? '',
    'ac_detail' => $_POST['ac_detail'] ?? '',
    'card_detail' => $_POST['card_detail'] ?? '',
    'transaction_id' => $_POST['transaction_id'] ?? '',
    'online_amount' => $_POST['online_amount'] ?? 0.0,
    'card_amount' => $_POST['card_amount'] ?? 0.0,
    'cash_payment' => $_POST['cash_payment'] ?? 0.0,
    'total' => $_POST['total'] ?? 0.0,
    'advance' => $_POST['advance'] ?? 0.0,
    'remaining' => $_POST['remaining'] ?? 0.0,
    'description' => $_POST['description'] ?? '',
    'file_media' => $file_media,
    'pos_bank_detail' => $_POST['pos_bank_detail'] ?? '',
    'reason' => $_POST['reason'] ?? $_POST['reason_hidden'] ?? '',
];

// Add missing variables
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
$requested_by = $_SESSION['user_id'];
$change_count = 0;
$numeric_fields = ['online_amount', 'card_amount', 'cash_payment', 'total', 'advance', 'remaining'];

// Loop through each field to check for changes
foreach ($fields as $key => $new_value) {
    $old_value = $original_order[$key] ?? '';
    $changed = false;

    if (in_array($key, $numeric_fields)) {
        $changed = ((float)$new_value != (float)$old_value);
    } else {
        $changed = (normalizeText($new_value) !== normalizeText($old_value));
    }

    if ($changed) {
        if ($is_admin) {
            // Direct update for admin
            $update_stmt = $conn->prepare("UPDATE orders SET `$key` = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_value, $order_id);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Prepare values as variables (important for bind_param)
            $old_val_str = (string)$old_value;
            $new_val_str = (string)$new_value;

            // Request approval for non-admin
            $stmt = $conn->prepare("INSERT INTO order_update_requests (order_id, field_name, old_value, new_value, requested_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $order_id, $key, $old_val_str, $new_val_str, $requested_by);
            $stmt->execute();
            $stmt->close();
        }

        $change_count++;
    }
}

if ($change_count > 0) {
    if ($is_admin) {
        echo "<script>alert('✅ Order updated!'); window.location.href='order.php';</script>";
    } else {
        echo "<script>alert('✅ Update request sent to admin for approval!'); window.location.href='order.php';</script>";
    }
} else {
    echo "<script>alert('⚠️ No changes detected. Nothing to update.'); window.location.href='order.php';</script>";
}

$conn->close();
