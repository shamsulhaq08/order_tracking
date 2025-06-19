<?php
require 'db_config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $id = (int)$_POST['request_id'];
    $stmt = $conn->prepare("DELETE FROM order_update_requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('❌ Request Rejected and Deleted!'); window.location.href='admin_order_update_requests.php';</script>";
    } else {
        echo "❌ Error rejecting request.";
    }
    $stmt->close();
}
?>
