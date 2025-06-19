<?php
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ Handle Approve All
    if (isset($_POST['action']) && $_POST['action'] === 'ApproveAll' && !empty($_POST['request_ids']) && is_array($_POST['request_ids'])) {
        foreach ($_POST['request_ids'] as $id) {
            $id = (int)$id;

            // Get each request detail
            $stmt = $conn->prepare("SELECT * FROM order_update_requests WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $req = $result->fetch_assoc();
            $stmt->close();

            if ($req) {
                // Apply change
                $sql = "UPDATE orders SET {$req['field_name']} = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $req['new_value'], $req['order_id']);
                $stmt->execute();
                $stmt->close();

                // Delete request
                $stmt = $conn->prepare("DELETE FROM order_update_requests WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo "<script>alert('✅ All changes approved!'); window.location.href='admin_order_update_requests.php';</script>";
        exit;
    }

    // ✅ Handle Single Approve
    if (isset($_POST['request_id'])) {
        $id = (int)$_POST['request_id'];

        $stmt = $conn->prepare("SELECT * FROM order_update_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $req = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($req) {
            // Apply change
            $sql = "UPDATE orders SET {$req['field_name']} = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $req['new_value'], $req['order_id']);
            $stmt->execute();
            $stmt->close();

            // Delete request
            $stmt = $conn->prepare("DELETE FROM order_update_requests WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('✅ Change Approved and Applied!'); window.location.href='admin_order_update_requests.php';</script>";
        } else {
            echo "<script>alert('❌ Request not found.'); window.location.href='admin_order_update_requests.php';</script>";
        }
        exit;
    }
}
?>
