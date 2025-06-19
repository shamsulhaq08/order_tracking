<?php
session_start();
require 'db_config.php'; // Ensure $conn = new mysqli(...) is set up here

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// Optional: Mark all notifications as read
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND is_read = 0");

// Fetch all notifications
$sql = "SELECT n.id, n.message, n.created_at, n.is_read, u.username 
        FROM notifications n 
        LEFT JOIN users u ON n.changed_by = u.id 
        WHERE n.user_id = ? 
        ORDER BY n.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .notification {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .unread {
            background-color: #f5f7fb;
            border-left: 3px solid #007bff;
        }
        .read {
            background-color: #fff;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3 class="mb-4">All Notifications</h3>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification <?php echo $row['is_read'] ? 'read' : 'unread'; ?>">
                <div class="d-flex justify-content-between">
                    <strong><?php echo htmlspecialchars($row['username'] ?? 'System'); ?></strong>
                    <small class="text-muted"><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></small>
                </div>
                <p class="mb-0"><?php echo htmlspecialchars($row['message']); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No notifications found.</div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
