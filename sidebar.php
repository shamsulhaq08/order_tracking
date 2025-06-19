<?php
require 'db_config.php';
require_once 'auth_helper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // ✅ Correct way
    session_regenerate_id(true); // Security: prevent session fixation
}

// Secure session validation
if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// // ✅ Display session values safely
// echo "Session id = " . ($_SESSION['id'] ?? 'Not set') . "<br>";
// echo "Session user_id = " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";

$user_id = $_SESSION['id'] ?? null;
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');

// Extra user check (optional)
if ($user_id) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE id = ? AND username = ?");
    $stmt->bind_param("is", $user_id, $_SESSION['username']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows !== 1) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} else {
    echo "User ID missing in session.";
    exit();
}

// // DEBUG: Show permission checks
// echo "<pre>DEBUG Permission Checks:\n";

// $debug_permissions = [
//     'order.php' => has_permission($conn, 'order.php', 'view'),
//     'order_view.php' => has_permission($conn, 'order_view.php', 'view'),
//     'staff.php' => has_permission($conn, 'staff.php', 'view'),
//     'bank_account.php' => has_permission($conn, 'bank_account.php', 'view'),
//     'register.php' => has_permission($conn, 'register.php', 'view'),
//     'manage_rights.php' => has_permission($conn, 'manage_rights.php', 'view')
// ];

// foreach ($debug_permissions as $file => $result) {
//     echo "$file => " . ($result ? '✅ allowed' : '❌ not allowed') . "\n";
// }
// echo "</pre>";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <!-- Your CSS and other head elements here -->
</head>
<body>
    <div data-simplebar>
        <ul class="app-menu">
            <li class="menu-title">Menu</li>

            <!-- Index page is always accessible -->
            <li class="menu-item">
                <a href="index.php" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="home"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            <?php if (has_permission($conn, 'order.php', 'view') || has_permission($conn, 'order_view.php', 'view')): ?>
            <li class="menu-item">
                <a href="#menuorder" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="box"></i></span>
                    <span class="menu-text">Order Management</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuorder">
                    <ul class="sub-menu">
                        <?php if (has_permission($conn, 'order.php', 'view')): ?>
                        <li class="menu-item">
                            <a href="order.php" class="menu-link">
                                <span class="menu-text">Create Order</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (has_permission($conn, 'order_view.php', 'view')): ?>
                        <li class="menu-item">
                            <a href="order_view.php" class="menu-link">
                                <span class="menu-text">View Orders</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <?php if (has_permission($conn, 'staff.php', 'view')): ?>
            <li class="menu-item">
                <a href="staff.php" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="users"></i></span>
                    <span class="menu-text">Staff</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (has_permission($conn, 'bank_account.php', 'view')): ?>
            <li class="menu-item">
                <a href="bank_account.php" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="building"></i></span>
                    <span class="menu-text">Bank Details</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (has_permission($conn, 'register.php', 'view') || has_permission($conn, 'manage_rights.php', 'view')): ?>
            <li class="menu-item">
                <a href="#menuExpages" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="user"></i></span>
                    <span class="menu-text">User Management</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuExpages">
                    <ul class="sub-menu">
                        <?php if (has_permission($conn, 'register.php', 'view')): ?>
                        <li class="menu-item">
                            <a href="register.php" class="menu-link">
                                <span class="menu-text">Register User</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (has_permission($conn, 'manage_rights.php', 'view')): ?>
                        <li class="menu-item">
                            <a href="manage_rights.php" class="menu-link">
                                <span class="menu-text">Manage Permissions</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>


            <?php if (has_permission($conn, 'admin_order_update_requests.php', 'view')): ?>
            <?php
                // Fetch count of pending order update requests
                $alert_count = 0;
                $stmt_alert = $conn->prepare("SELECT COUNT(*) FROM order_update_requests WHERE status = 'pending'");
                if ($stmt_alert) {
                    $stmt_alert->execute();
                    $stmt_alert->bind_result($alert_count);
                    $stmt_alert->fetch();
                    $stmt_alert->close();
                }
            ?>
            <li class="menu-item position-relative">
                <a href="admin_order_update_requests.php" class="menu-link waves-effect d-flex align-items-center">
                    <span class="menu-icon position-relative" style="display:inline-block;">
                        <i data-lucide="bell" id="alert-bell"></i>
                        <?php if ($alert_count > 0): ?>
                            <span class="alert-badge" id="alert-badge">
                                <?php echo $alert_count; ?>
                            </span>
                        <?php endif; ?>
                    </span>
                    <span class="menu-text ms-2">
                        Order Edit Request
                    </span>
                </a>
            </li>
            <style>
                .alert-badge {
                    position: absolute;
                    top: -7px;
                    right: -14px;
                    min-width: 14px;
                    height: 14px;
                    color: #fff;
                    background: #dc3545;
                    border-radius: 50%;
                    padding: 0 4px;
                    font-size: 0.75em;
                    font-weight: bold;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 0 0 2px #fff;
                    animation: alert-bounce 1s infinite;
                    z-index: 2;
                }
                @keyframes alert-bounce {
                    0%, 100% { transform: scale(1);}
                    20% { transform: scale(1.2);}
                    40% { transform: scale(0.95);}
                    60% { transform: scale(1.1);}
                    80% { transform: scale(0.98);}
                }
                /* Make bell icon larger */
                #alert-bell {
                   font-size: 14px !important;
                    width: 1.5em;
                    height: 1.5em;
                }
                /* Optional: highlight bell icon when alert is present */
                #alert-bell[data-has-alert="1"] {
                    color: #9b9b9b;
                    animation: bell-shake 0.7s infinite;
                }
                @keyframes bell-shake {
                    0% { transform: rotate(0);}
                    20% { transform: rotate(-15deg);}
                    40% { transform: rotate(10deg);}
                    60% { transform: rotate(-8deg);}
                    80% { transform: rotate(6deg);}
                    100% { transform: rotate(0);}
                }
            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var badge = document.getElementById('alert-badge');
                    var bell = document.getElementById('alert-bell');
                    if (badge && bell) {
                        bell.setAttribute('data-has-alert', '1');
                        // Optional: add a pulse effect on click
                        bell.addEventListener('click', function() {
                            badge.style.animation = 'none';
                            void badge.offsetWidth; // trigger reflow
                            badge.style.animation = 'alert-bounce 1s infinite';
                        });
                    }
                });
            </script>
            <?php endif; ?>
           
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Your JavaScript includes here -->
</body>
</html>