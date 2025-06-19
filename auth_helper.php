<?php
function has_permission($conn, $page, $type = 'view') {
    if (!isset($_SESSION['user_id'])) {
        echo "❌ No user session<br>";
        return false;
    }

    $user_id = $_SESSION['user_id'];

    switch ($type) {
        case 'edit':   $column = 'can_edit'; break;
        case 'delete': $column = 'can_delete'; break;
        default:       $column = 'can_view';
    }

    // echo "<strong>🔍 Checking permission for:</strong> user_id=$user_id, page=$page, type=$type ($column)<br>";

    $stmt = $conn->prepare("SELECT $column FROM user_permissions WHERE user_id = ? AND page_name = ?");
    $stmt->bind_param("ss", $user_id, $page);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // echo "✅ DB Row found: "; print_r($row); echo "<br>";
        return (int)$row[$column] === 1;
    }

    // echo "❌ No row found in DB for this permission<br>";
    return false;
}

?>
