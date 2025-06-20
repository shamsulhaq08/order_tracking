       <?php
include 'db_config.php'; // Include your database configuration file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get session values safely
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = (int)$_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
    exit();
}
?>
       
       <!-- ========== Topbar Start ========== -->
            <div class="navbar-custom">
                <div class="topbar">
                    <div class="topbar-menu d-flex align-items-center gap-lg-2 gap-1">

                        <!-- Brand Logo -->
                        <div class="logo-box">
                <!-- Brand Logo Light -->
                  <a href="index.php" class="logo-light">
                    <h4 style="color: white;">Tracking</h4>
                </a>
            </div>

                        <!-- Sidebar Menu Toggle Button -->
                        <button class="button-toggle-menu waves-effect waves-dark rounded-circle">
                            <i class="mdi mdi-menu"></i>
                        </button>
                    </div>

                                    <ul class="topbar-menu d-flex align-items-center gap-2">

                                         <?php
                                        $current_page = basename($_SERVER['PHP_SELF']);
                                        $hide_notifications = ($current_page === 'admin_order_update_requests.php' || $current_page === 'order_view.php' || $current_page === 'order.php' || $current_page === 'staff.php' || $current_page === 'bank_account.php' || $current_page === 'register.php' || $current_page === 'bank_account_edit.php' || $current_page === 'manage_rights.php' || $current_page === 'order_edit.php');
                                        ?>

                                        <li class="d-none d-md-inline-block">
                                           <a class="nav-link waves-effect waves-dark" href="#" data-bs-toggle="fullscreen">
                                              <i class="mdi mdi-fullscreen font-size-24"></i>
                                           </a>
                                        </li>

                                        <?php if (!$hide_notifications): ?>
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
                                        <style>
                                        /* Bell shake animation */
                                        @keyframes bell-shake {
                                            0% { transform: rotate(0deg); }
                                            15% { transform: rotate(-15deg); }
                                            30% { transform: rotate(10deg); }
                                            45% { transform: rotate(-10deg); }
                                            60% { transform: rotate(6deg); }
                                            75% { transform: rotate(-4deg); }
                                            100% { transform: rotate(0deg); }
                                        }
                                        .bell-shake {
                                            animation: bell-shake 0.8s cubic-bezier(.36,.07,.19,.97) both;
                                        }
                                        </style>
                                        <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            var bell = document.querySelector('.notification-list .mdi-bell');
                                            <?php if ($alert_count > 0): ?>
                                            if (bell) {
                                                function shakeBell() {
                                                    bell.classList.remove('bell-shake');
                                                    void bell.offsetWidth; // trigger reflow
                                                    bell.classList.add('bell-shake');
                                                }
                                                setInterval(shakeBell, 1000); // shake every second
                                                shakeBell(); // initial shake
                                            }
                                            <?php endif; ?>
                                        });
                                        </script>       <li class="dropdown notification-list">
                                           <a class="nav-link dropdown-toggle waves-effect waves-dark arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                              <i class="mdi mdi-bell font-size-24"></i>
                                            <?php if ($alert_count > 0): ?>
                                                <span class="badge bg-danger rounded-circle noti-icon-badge"><?php echo $alert_count; ?></span>
                                                <audio id="alert-audio" src="http://localhost/order_tracking/assets/beep-07.mp3" preload="auto"></audio>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        // Play sound only if there are new alerts
                                                        var audio = document.getElementById('alert-audio');
                                                        if (audio) {
                                                            audio.play().catch(function(){});
                                                        }
                                                    });
                                                </script>
                                            <?php endif; ?> </a>
                                           <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0" style="min-width: 400px;">
                                              <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                                                 <div class="row align-items-center">
                                                    <div class="col">
                                                        <h6 class="m-0 font-size-16 fw-semibold">Order Update Requests</h6>
                                                    </div>
                                                 </div>
                                              </div>


                                              <div class="px-1" style="max-height: 350px;" data-simplebar>
    <?php
        // Field label mapping
        $field_labels = [
            'order_date'        => 'Order Date',
            'delivery_date'     => 'Delivery Date',
            'order_time'        => 'Order Time',
            'customer_name'     => 'Customer Name',
            'contact'           => 'Contact',
            'customer_address'  => 'Customer Address',
            'order_maker_id'    => 'Order Maker',
            'order_source'      => 'Order Source',
            'source_other_text' => 'Source (Other)',
            'payment'           => 'Payment',
            'bank_detail'       => 'Bank Detail',
            'ac_detail'         => 'Account Detail',
            'card_detail'       => 'Card Detail',
            'transaction_id'    => 'Transaction ID',
            'online_amount'     => 'Online Amount',
            'card_amount'       => 'Card Amount',
            'cash_payment'      => 'Cash Payment',
            'total'             => 'Total',
            'advance'           => 'Advance',
            'remaining'         => 'Remaining',
            'description'       => 'Description',
            'file_media'        => 'File/Media',
            'pos_bank_detail'   => 'POS Bank Detail',
            'reason'            => 'Reason'
        ];

        // Fetch latest pending order update requests
        $stmt = $conn->prepare("SELECT r.*, u.username, o.customer_name FROM order_update_requests r LEFT JOIN users u ON r.requested_by = u.user_id LEFT JOIN orders o ON r.order_id = o.id WHERE r.status = 'pending' ORDER BY r.requested_at DESC");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $field_key = $row['field_name'];
                    $field = isset($field_labels[$field_key]) ? $field_labels[$field_key] : htmlspecialchars($field_key);
                    $old = htmlspecialchars($row['old_value']);
                    $new = htmlspecialchars($row['new_value']);
                    $user = htmlspecialchars($row['username'] ?? $row['requested_by']);
                    $customer = htmlspecialchars($row['customer_name'] ?? '');
                    $date = date('g:i A, j M Y', strtotime($row['requested_at']));

                    // If field is order_maker_id, fetch full names
                    if ($field_key === 'order_maker_id') {
                        $old_name = $row['old_value'];
                        $new_name = $row['new_value'];
                        $old_staff_name = '';
                        $new_staff_name = '';

                        // Fetch old staff name
                        if (!empty($old_name) && ctype_digit($old_name)) {
                            $staff_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE id = ?";
                            $sstmt = $conn->prepare($staff_sql);
                            $sstmt->bind_param("i", $old_name);
                            $sstmt->execute();
                            $sstmt->bind_result($full_name);
                            if ($sstmt->fetch()) {
                                $old_staff_name = $full_name;
                            }
                            $sstmt->close();
                        }

                        // Fetch new staff name
                        if (!empty($new_name) && ctype_digit($new_name)) {
                            $staff_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE id = ?";
                            $sstmt = $conn->prepare($staff_sql);
                            $sstmt->bind_param("i", $new_name);
                            $sstmt->execute();
                            $sstmt->bind_result($full_name);
                            if ($sstmt->fetch()) {
                                $new_staff_name = $full_name;
                            }
                            $sstmt->close();
                        }

                        $old = htmlspecialchars($old_staff_name !== '' ? $old_staff_name : ($old_name !== '' ? $old_name : 'N/A'));
                        $new = htmlspecialchars($new_staff_name !== '' ? $new_staff_name : ($new_name !== '' ? $new_name : 'N/A'));
                    }

                    echo '<div class="dropdown-item notify-item">';
                    echo '<div><strong>Order #'.$row['order_id'].'</strong> ('.$customer.')</div>';
                    echo '<div>Field: <code>'.$field.'</code></div>';
                    echo '<div>From: <code>'.$old.'</code> &rarr; <code>'.$new.'</code></div>';
                    echo '<div>By: <code>'.$user.'</code> <span class="text-muted small">('.$date.')</span></div>';

                    // Show approve/reject only for admin
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        echo '<div class="mt-1">';
                        echo '<form action="approve_order_change.php" method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="'.(int)$row['id'].'">
                                <input type="submit" name="action" value="Approve" class="btn btn-success btn-sm" onclick="return confirm(\'Approve this change?\')">
                              </form>
                              <form action="reject_order_change.php" method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="'.(int)$row['id'].'">
                                <input type="submit" name="action" value="Reject" class="btn btn-danger btn-sm" onclick="return confirm(\'Reject this change?\')">
                              </form>';
                        echo '</div>';
                    }

                    echo '<hr class="my-1">';
                    echo '</div>';
                }
            } else {
                echo '<div class="dropdown-item text-muted">No pending requests.</div>';
            }
            $stmt->close();
        } else {
            echo '<div class="dropdown-item text-danger">Error loading notifications.</div>';
        }
    ?>
    </div>   
    
    
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <a href="admin_order_update_requests.php" class="dropdown-item text-center text-primary notify-item border-top border-light py-2">
                                                View All
                                            </a>
                                            <?php endif; ?>       </div>
                                        </li>
                                         <?php endif; ?>       
                         
                            
                            <li class="nav-link waves-effect waves-dark" id="theme-mode">
                                <i class="bx bx-moon font-size-24"></i>
                            </li>

                        <li class="nav-item d-flex align-items-center">
                            <i class="mdi mdi-account-circle font-size-24 me-1"></i>
                            <span class="d-none d-md-inline-block"><?php echo htmlspecialchars($username); ?></span>
                            <a class="nav-link ms-2" href="logout.php" title="Logout">
                                 <i data-lucide="log-out" class="me-1"></i> Logout
                            </a>
                        </li>
                        </ul>       </div>
                                </div>     
                                
                                
<!-- ========== Topbar End ========== -->
