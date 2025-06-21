<?php
include 'db_config.php';
header('Content-Type: application/json');

// Sanitize input
$user_id = trim($_POST['user_id']);
$page_name = trim($_POST['page_name']);
$permission = trim($_POST['permission']);
$value = intval($_POST['value']);

// Map short names to DB column names
$columns = [
    'view' => 'can_view',
    'edit' => 'can_edit',
    'allow' => 'can_allow'
];

// Validate permission key
if (!isset($columns[$permission])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid permission']);
    exit;
}

$column = $columns[$permission];

// Check if record exists
$stmt = $conn->prepare("SELECT id FROM user_permissions WHERE user_id = ? AND page_name = ?");
$stmt->bind_param("ss", $user_id, $page_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update permission
    $sql = "UPDATE user_permissions SET $column = ? WHERE user_id = ? AND page_name = ?";
    $update = $conn->prepare($sql);
    $update->bind_param("iss", $value, $user_id, $page_name);
    $update->execute();
    $update->close();
} else {
    // Insert new permission
    $sql = "INSERT INTO user_permissions (user_id, page_name, $column) VALUES (?, ?, ?)";
    $insert = $conn->prepare($sql);
    $insert->bind_param("ssi", $user_id, $page_name, $value);
    $insert->execute();
    $insert->close();
}

$stmt->close();

echo json_encode([
    'status' => 'success',
    'user_id' => $user_id,
    'page' => $page_name,
    'permission' => $permission,
    'value' => $value
]);
?>
