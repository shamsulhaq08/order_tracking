<?php
include 'db_config.php'; // Include your database configuration file
require_once 'auth_helper.php';
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


// (Optional) Check if current user is admin
// if ($_SESSION['role'] !== 'admin') die("Access denied");

$sql = "SELECT r.*, o.customer_name 
        FROM order_update_requests r 
        JOIN orders o ON r.order_id = o.id 
        ORDER BY r.requested_at DESC";
$result = $conn->query($sql);
?>
  
<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

<head>
    <meta charset="utf-8" />
    <title> Order Update Requests </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link href="assets/libs/morris.js/morris.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <script src="assets/js/config.js"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

</head>

<body>

    <!-- Begin page -->
    <div class="layout-wrapper">

        <!-- ========== Left Sidebar ========== -->
        <div class="main-menu">
            <!-- Brand Logo -->
            <div class="logo-box">
                <!-- Brand Logo Light -->
                  <a href="index.php" class="logo-light">
                    <h4 style="color: white;"> Tracking</h4>
                </a>

                
            </div>

     <?php include 'sidebar.php'; ?>
        </div>

        

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="page-content">

     <?php include 'topbar.php'; ?>

            <div class="px-3">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="py-3 py-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="page-title mb-0">Order Update Requests</h4>
                                <br>
                        
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">

                                    <?php
$grouped_orders = [];

while ($row = $result->fetch_assoc()) {
    $grouped_orders[$row['order_id']][] = $row;
}
?>
                                 <table class="table table-bordered table-hover align-middle mb-0">
    <thead class="table-dark">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>View Updates</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($grouped_orders as $order_id => $requests): ?>
            <tr>
                <td><?= $order_id ?></td>
                <td><?= htmlspecialchars($requests[0]['customer_name']) ?></td>
                <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_<?= $order_id ?>">View Changes</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

                                    </div>
                                </div>
                            </div>

                </div> <!-- container -->

            </div> <!-- content -->

            

        </div>

    <?php foreach ($grouped_orders as $order_id => $requests): ?>
    <!-- Modal for Order ID <?= $order_id ?> -->
    <div class="modal fade" id="modal_<?= $order_id ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                  
                    <h5 class="modal-title"> Order #<?= $order_id ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Approve All Button -->
                    <form action="approve_order_change.php" method="post" style="margin-bottom: 15px;">
                        <?php foreach ($requests as $row): ?>
                            <input type="hidden" name="request_ids[]" value="<?= $row['id'] ?>">
                        <?php endforeach; ?>
                         
                        <input type="hidden" name="order_id" value="<?= $order_id ?>">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0"> <?= htmlspecialchars($requests[0]['customer_name']) ?></h4>
                            <!-- <button type="submit" name="action" value="ApproveAll" class="btn btn-success waves-effect waves-light" onclick="return confirm('Approve ALL changes for this order?')">
                                <span class="btn-label"><i class="mdi mdi-check-all"></i></span> Approve All
                            </button> -->
                        </div>
                    </form>
                  <!-- Desktop Table -->
<div class="d-none d-md-block">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Field</th>
                <th>Old Value</th>
                <th>New Value</th>
                <th>User</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>


        <tbody>
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
        ?>

        <?php foreach ($requests as $row): ?>
            <tr>
                <td style="text-transform: capitalize;">
                    <?= htmlspecialchars($field_labels[$row['field_name']] ?? $row['field_name']) ?>
                </td>

                <?php
                $old_value_display = htmlspecialchars($row['old_value']);
                $new_value_display = htmlspecialchars($row['new_value']);

                // If the field is 'order_maker_id', fetch staff names
                if ($row['field_name'] === 'order_maker_id') {
                    $old_name = $row['old_value'];
                    $new_name = $row['new_value'];
                    $old_staff_name = '';
                    $new_staff_name = '';

                    // Fetch old staff name
                    if (!empty($old_name) && ctype_digit($old_name)) {
                        $staff_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE id = ?";
                        $stmt = $conn->prepare($staff_sql);
                        $stmt->bind_param("i", $old_name);
                        $stmt->execute();
                        $stmt->bind_result($full_name);
                        if ($stmt->fetch()) {
                            $old_staff_name = $full_name;
                        }
                        $stmt->close();
                    }

                    // Fetch new staff name
                    if (!empty($new_name) && ctype_digit($new_name)) {
                        $staff_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE id = ?";
                        $stmt = $conn->prepare($staff_sql);
                        $stmt->bind_param("i", $new_name);
                        $stmt->execute();
                        $stmt->bind_result($full_name);
                        if ($stmt->fetch()) {
                            $new_staff_name = $full_name;
                        }
                        $stmt->close();
                    }

                    $old_value_display = htmlspecialchars($old_staff_name !== '' ? $old_staff_name : ($old_name !== '' ? $old_name : 'N/A'));
                    $new_value_display = htmlspecialchars($new_staff_name !== '' ? $new_staff_name : ($new_name !== '' ? $new_name : 'N/A'));
                }
                ?>

                <td style="text-transform: capitalize;"><?= $old_value_display ?></td>
                <td style="text-transform: capitalize;"><strong class="text-success"><?= $new_value_display ?></strong></td>

                <td>
                    <?php
                    // Fetch username logic
                    $user_name = 'Unknown';
                    if (!empty($row['requested_by'])) {
                        $requested_by_user_id = $conn->real_escape_string($row['requested_by']);
                        $user_sql = "SELECT username FROM users WHERE user_id = '$requested_by_user_id' LIMIT 1";
                        $user_result = $conn->query($user_sql);
                        if ($user_result && $user_row = $user_result->fetch_assoc()) {
                            $user_name = htmlspecialchars($user_row['username']);
                        } else {
                            $user_name = $row['requested_by'];
                        }
                    }
                    echo $user_name;
                    ?>
                </td>

                <td><?= date('j F Y', strtotime($row['requested_at'])) ?></td>

                <td>
                    <form action="approve_order_change.php" method="post" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                        <input type="submit" name="action" value="Approve" class="btn btn-success btn-sm" onclick="return confirm('Approve this change?')">
                    </form>
                    <form action="reject_order_change.php" method="post" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                        <input type="submit" name="action" value="Reject" class="btn btn-danger btn-sm" onclick="return confirm('Reject this change?')">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
        </div>

    <!-- Mobile Card View -->
    <div class="d-block d-md-none">
        <?php foreach ($requests as $row): ?>
        <div class="card mb-3">
            <div class="card-body p-2">
            <?php
            // Field label mapping (same as desktop)
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
            ?>
            <p><strong>Field:</strong> <?= htmlspecialchars($field_labels[$row['field_name']] ?? $row['field_name']) ?></p>

            <?php
            // If the field is 'order_maker_id', fetch the staff name for old and new values
            if ($row['field_name'] === 'order_maker_id') {
                $old_name = $row['old_value'];
                $new_name = $row['new_value'];
                $old_staff_name = '';
                $new_staff_name = '';

                // Get old staff name
                if (!empty($old_name) && ctype_digit($old_name)) {
                $staff_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE id = ?";
                $stmt = $conn->prepare($staff_sql);
                $stmt->bind_param("i", $old_name);
                $stmt->execute();
                $stmt->bind_result($full_name);
                if ($stmt->fetch()) {
                    $old_staff_name = $full_name;
                }
                $stmt->close();
                }

                // Get new staff name
                if (!empty($new_name) && ctype_digit($new_name)) {
                $staff_sql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE id = ?";
                $stmt = $conn->prepare($staff_sql);
                $stmt->bind_param("i", $new_name);
                $stmt->execute();
                $stmt->bind_result($full_name);
                if ($stmt->fetch()) {
                    $new_staff_name = $full_name;
                }
                $stmt->close();
                }
                ?>
                <p><strong>Old Value:</strong> <?= htmlspecialchars($old_staff_name !== '' ? $old_staff_name : ($old_name !== '' ? $old_name : 'N/A')) ?></p>
                <p><strong>New Value:</strong> <span class="text-success"><?= htmlspecialchars($new_staff_name !== '' ? $new_staff_name : ($new_name !== '' ? $new_name : 'N/A')) ?></span></p>
                <?php
            } else {
                ?>
                <p><strong>Old Value:</strong> <?= htmlspecialchars($row['old_value']) ?></p>
                <p><strong>New Value:</strong> <span class="text-success"><?= htmlspecialchars($row['new_value']) ?></span></p>
                <?php
            }
            ?>

            <p>
                <strong>Requested By:</strong>
                <?php
                $user_name = 'Unknown';
                if (!empty($row['requested_by'])) {
                $requested_by_user_id = $conn->real_escape_string($row['requested_by']);
                $user_sql = "SELECT username FROM users WHERE user_id = '$requested_by_user_id' LIMIT 1";
                $user_result = $conn->query($user_sql);
                if ($user_result && $user_row = $user_result->fetch_assoc()) {
                    $user_name = htmlspecialchars($user_row['username']);
                } else {
                    $user_name = $row['requested_by'];
                }
                }
                echo $user_name;
                ?>
            </p>
            <p><strong>Requested At:</strong> <?= date('g:i A, j F Y', strtotime($row['requested_at'])) ?></p>

            <div class="d-flex gap-2">
                <form action="approve_order_change.php" method="post" style="display:inline;">
                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                <input type="submit" name="action" value="Approve" class="btn btn-success btn-sm" onclick="return confirm('Approve this change?')">
                </form>
                <form action="reject_order_change.php" method="post" style="display:inline;">
                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                <input type="submit" name="action" value="Reject" class="btn btn-danger btn-sm" onclick="return confirm('Reject this change?')">
                </form>
            </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll('.order-row');
    rows.forEach(row => {
        row.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id');
            fetch('fetch_order_change_details.php?order_id=' + orderId)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('modalOrderId').textContent = orderId;
                    document.getElementById('modalBodyContent').innerHTML = html;
                    var myModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                    myModal.show();
                });
        });
    });
});
</script>


        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- App js -->
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.js"></script>

    <!-- Jquery Sparkline Chart  -->
    <script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>

    <!-- Jquery-knob Chart Js-->
    <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>


    <!-- Morris Chart Js-->
    <script src="assets/libs/morris.js/morris.min.js"></script>

    <script src="assets/libs/raphael/raphael.min.js"></script>

    <!-- Dashboard init-->
    <script src="assets/js/pages/dashboard.js"></script>

</body>

</html>




