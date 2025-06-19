<?php
include 'db_config.php';

$user_id = $_POST['user_id'];
$page_name = $_POST['page_name'];
$permission = $_POST['permission'];
$value = $_POST['value'];

// Map short names to DB column names
$columns = [
    'view' => 'can_view',
    'edit' => 'can_edit',
    'delete' => 'can_delete'
];

// Validate permission type
if (!isset($columns[$permission])) {
    die("Invalid permission");
}

$column = $columns[$permission];

// Check if record exists
$stmt = $conn->prepare("SELECT id FROM user_permissions WHERE user_id = ? AND page_name = ?");
$stmt->bind_param("ss", $user_id, $page_name); // both strings now
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Record exists: update the specific permission
    $sql = "UPDATE user_permissions SET $column = ? WHERE user_id = ? AND page_name = ?";
    $update = $conn->prepare($sql);
    $update->bind_param("sss", $value, $user_id, $page_name);
    $update->execute();
    $update->close();
} else {
    // Record does not exist: insert new
    $sql = "INSERT INTO user_permissions (user_id, page_name, $column) VALUES (?, ?, ?)";
    $insert = $conn->prepare($sql);
    $insert->bind_param("sss", $user_id, $page_name, $value);
    $insert->execute();
    $insert->close();
}

$stmt->close();
echo "Done";

?>
