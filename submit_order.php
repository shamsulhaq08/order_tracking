<?php
session_start();
require 'db_config.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = (int)$_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    die("User not logged in.");
}

$created_by = $user_id;
$created_by_name = $username;

function idExists($conn, $table, $id) {
    $stmt = $conn->prepare("SELECT id FROM $table WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

// Sanitize and collect POST data
$order_date = $_POST['date'] ?? '';
$delivery_date = $_POST['delivery_date'] ?? '';
$order_time = $_POST['time'] ?? '';
$customer_name = $_POST['name'] ?? '';
$contact = $_POST['contact'] ?? '';
$customer_address = $_POST['customer_address'] ?? '';
$whatsapp_number = $_POST['whatsapp_number'] ?? '';
$order_maker_id = isset($_POST['order_maker_id']) ? (int)$_POST['order_maker_id'] : 0;

if (!idExists($conn, 'staff', $order_maker_id)) {
    die("Invalid order maker ID: {$order_maker_id} does not exist in staff.");
}

$order_source = '';
if (isset($_POST['source'])) {
    $order_source = htmlspecialchars($_POST['source'], ENT_QUOTES, 'UTF-8');
}

$payment = '';
if (isset($_POST['payment']) && is_array($_POST['payment'])) {
    $payment = implode(", ", array_map('htmlspecialchars', $_POST['payment']));
}

$card_detail = $_POST['card_detail'] ?? '';
$description = $_POST['description'] ?? '';
$ac_detail = $_POST['ac_detail'] ?? '';
$reason = $_POST['reason'] ?? '';


$transaction_id = $_POST['transaction_id'] ?? '';
$online_amount = isset($_POST['online_amount']) ? (string)$_POST['online_amount'] : '0';
$card_amount = isset($_POST['card_amount']) ? (string)$_POST['card_amount'] : '0';
$cash_payment = isset($_POST['cash_payment']) ? (float)$_POST['cash_payment'] : 0.0;
$source_other_text = htmlspecialchars($_POST['source_other_text'] ?? '', ENT_QUOTES, 'UTF-8');
$total = isset($_POST['total']) ? (float)$_POST['total'] : 0.0;
$advance = isset($_POST['advance']) ? (float)$_POST['advance'] : 0.0;
$remaining = isset($_POST['remaining']) ? (float)$_POST['remaining'] : 0.0;

$status = 'Processing';

// Bank account details
$bank_detail_id = $_POST['bank_detail'] ?? '';
$bank_detail = '';
if (!empty($bank_detail_id)) {
    $stmt = $conn->prepare("SELECT account_name FROM bank_accounts WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $bank_detail_id);
        $stmt->execute();
        $stmt->bind_result($bank_account_name);
        if ($stmt->fetch()) {
            $bank_detail = $bank_account_name;
        }
        $stmt->close();
    }
}

$pos_bank_detail_id = $_POST['pos_bank_detail'] ?? '';
$pos_bank_detail = '';
if (!empty($pos_bank_detail_id) && is_numeric($pos_bank_detail_id)) {
    $stmt = $conn->prepare("SELECT account_name FROM bank_accounts WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $pos_bank_detail_id);
        $stmt->execute();
        $stmt->bind_result($pos_bank_account_name);
        if ($stmt->fetch()) {
            $pos_bank_detail = $pos_bank_account_name;
        }
        $stmt->close();
    }
}

// Handle multiple file uploads
$file_media = '';
if (!empty($_FILES['file_media']) && isset($_FILES['file_media']['name']) && is_array($_FILES['file_media']['name'])) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_names = [];
    foreach ($_FILES['file_media']['name'] as $key => $name) {
        if ($_FILES['file_media']['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
            $tmp_name = $_FILES['file_media']['tmp_name'][$key];
            $unique_name = uniqid('media_', true) . '_' . basename($name);
            $target_path = $upload_dir . $unique_name;
            if (move_uploaded_file($tmp_name, $target_path)) {
                $file_names[] = $unique_name;
            }
        }
    }
    $file_media = implode(',', $file_names);
}

// Final SQL
$sql = "INSERT INTO orders (
    order_date, delivery_date, order_time, customer_name, contact, 
    payment, order_maker_id, order_source, description, 
    bank_detail, ac_detail, card_detail, 
    total, advance, remaining, source_other_text,
    created_by, created_by_name, transaction_id, online_amount, card_amount, file_media, status, cash_payment, whatsapp_number, customer_address, pos_bank_detail, reason
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "ssssssisssssdddsissssssdssss",
    $order_date,
    $delivery_date,
    $order_time,
    $customer_name,
    $contact,
    $payment,
    $order_maker_id,
    $order_source,
    $description,
    $bank_detail,
    $ac_detail,
    $card_detail,
    $total,
    $advance,
    $remaining,
    $source_other_text,
    $created_by,
    $created_by_name,
    $transaction_id,
    $online_amount,
    $card_amount,
    $file_media,
    $status,
    $cash_payment,
    $whatsapp_number,
    $customer_address,
    $pos_bank_detail,
    $reason
);

if ($stmt->execute()) {
    $inserted_id = $stmt->insert_id;
    header("Location: order_success.php?id=$inserted_id");
    exit;
} else {
    die("Insert failed: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
