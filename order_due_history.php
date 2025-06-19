<?php
session_start();

// Check if order_id is passed via GET
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID");
}

$orderId = (int)$_GET['order_id'];

// DB connection
$mysqli = new mysqli("localhost", "root", "12345", "order_tracking");
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}

// Prepare and execute query
$stmt = $mysqli->prepare("SELECT cleared_amount, cleared_at, cleared_by FROM order_due_history WHERE order_id = ? ORDER BY cleared_at DESC");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Due History - Order #<?= htmlspecialchars($orderId) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
    <div class="container">
        <h2>Due Payment History for Order #<?= htmlspecialchars($orderId) ?></h2>
        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info mt-4">No due</div>
        <?php else: ?>
            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                        <th>Cleared Amount</th>
                        <th>Cleared At</th>
                        <th>Cleared By (User ID)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= number_format($row['cleared_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['cleared_at']) ?></td>
                        <td><?= $row['cleared_by'] !== null ? (int)$row['cleared_by'] : 'Unknown' ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="orders_list.php" class="btn btn-secondary mt-3">Back to Orders</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
