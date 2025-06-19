<?php
include 'db_config.php';

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    header("Location: register.php");
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: register.php?msg=User+not+found");
    exit;
}
$stmt->close();

// Delete related bank_accounts first
$stmt = $conn->prepare("DELETE FROM bank_accounts WHERE created_by = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Then delete from user_permissions
$stmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->close();


// 👇 Then delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: register.php?msg=User+deleted+successfully");
    exit;
} else {
    $error = $stmt->error ?: $conn->error;
    $stmt->close();
    die("Error deleting user: " . $error);
}
?>
