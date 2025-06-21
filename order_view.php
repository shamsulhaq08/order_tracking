<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db_config.php';
?>



<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

    <head>
        <meta charset="utf-8" />
        <title>View Order</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Drezoc - Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="MyraStudio" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- third party css -->
        <link href="assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-select-bs5/css//select.bootstrap5.min.css" rel="stylesheet" type="text/css" />
        <!-- third party css end -->

        <!-- In your <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Before closing </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


		<!-- App css -->
		<link href="assets/css/style.min.css" rel="stylesheet" type="text/css">
		<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
		<script src="assets/js/config.js"></script>
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
                    <h4 style="color: white;">Tracking</h4>
                </a>
                </div>

               <?php include 'sidebar.php'; ?>

                
            </div>

            

            <!-- Start Page Content here -->
            <div class="page-content">

            <?php include 'topbar.php'; ?>

                <div class="px-3">
  <br>
                    <!-- Start Content-->
                    <div class="container-fluid">
                      
                        
 <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Order View</h4>
                <p class="text-muted font-size-13 mb-4"></p>

               <?php
// Database connection
$mysqli = new mysqli("localhost", "root", "12345", "order_tracking");
if ($mysqli->connect_errno) {
    echo "<div class='alert alert-danger'>Failed to connect to MySQL: " . $mysqli->connect_error . "</div>";
    exit();
}
?>

<div class="mb-3">
<form id="filterForm" class="row g-2 align-items-center" method="get">

    <div class="col-auto">
        <label for="statusFilter" class="col-form-label">Order Status:</label>
    </div>
    <div class="col-auto">
        <select id="statusFilter" name="status" class="form-select form-select-sm">
            <option value="">All</option>
        
            <option value="Processing" <?= (isset($_GET['status']) && $_GET['status'] == 'Processing') ? 'selected' : '' ?>>Processing</option>
            <option value="Assigned" <?= (isset($_GET['status']) && $_GET['status'] == 'Assigned') ? 'selected' : '' ?>>Assigned</option>
            <option value="Completed" <?= (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : '' ?>>Completed</option>
            <option value="Delivered" <?= (isset($_GET['status']) && $_GET['status'] == 'Delivered') ? 'selected' : '' ?>>Delivered</option>
            <option value="Cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </div>
    <div class="col-auto">
        <label for="orderMakerFilter" class="col-form-label">Order Maker:</label>
    </div>
    <div class="col-auto">
        <select id="orderMakerFilter" name="order_maker_id" class="form-select form-select-sm">
            <option value="">All</option>
            <?php
            $staffResult = $mysqli->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM staff  ");
            if ($staffResult) {
                while ($staff = $staffResult->fetch_assoc()) {
                    $selected = (isset($_GET['order_maker_id']) && $_GET['order_maker_id'] == $staff['id']) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($staff['id']) . '" ' . $selected . '>' . htmlspecialchars($staff['name']) . '</option>';
                }
                $staffResult->free();
            }
            ?>
        </select>
    </div>
    <div class="col-auto">
        <label for="orderDateFrom" class="col-form-label">Order Date From:</label>
    </div>
    <div class="col-auto">
        <input type="date" id="orderDateFrom" name="order_date_from" class="form-control form-control-sm" onclick="this.showPicker()"
            value="<?= isset($_GET['order_date_from']) ? htmlspecialchars($_GET['order_date_from']) : '' ?>">
    </div>
    <div class="col-auto">
        <label for="orderDateTo" class="col-form-label">To:</label>
    </div>
    <div class="col-auto">
        <input type="date" id="orderDateTo" name="order_date_to" class="form-control form-control-sm" onclick="this.showPicker()"
            value="<?= isset($_GET['order_date_to']) ? htmlspecialchars($_GET['order_date_to']) : '' ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
    </div>
    <div class="col-auto">
        <a href="order_view.php" class="btn btn-sm btn-outline-secondary">Reset</a>

   
    </div>
</form>
</div>

<div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th class="d-none d-sm-table-cell">Order Date</th>
                    <th class="d-none d-sm-table-cell">Delivery Date</th>
                    <th class="d-none d-sm-table-cell">Deliver Time</th>
                    <th class="d-none d-sm-table-cell">Contact</th>
                 
                    <th class="d-none d-sm-table-cell">Order Maker</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php
// Pagination setup
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Build WHERE clause
$whereClauses = [];
$params = [];
$types = '';

// Status filter
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $whereClauses[] = "o.status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

// Order maker filter
if (isset($_GET['order_maker_id']) && $_GET['order_maker_id'] !== '') {
    $whereClauses[] = "o.order_maker_id = ?";
    $params[] = $_GET['order_maker_id'];
    $types .= 'i';
}

// Order date from
if (!empty($_GET['order_date_from'])) {
    $whereClauses[] = "o.order_date >= ?";
    $params[] = $_GET['order_date_from'];
    $types .= 's';
}

// Order date to
if (!empty($_GET['order_date_to'])) {
    $whereClauses[] = "o.order_date <= ?";
    $params[] = $_GET['order_date_to'];
    $types .= 's';
}

// Combine WHERE clauses
$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Count total records
$countSql = "SELECT COUNT(*) as total FROM orders o $whereSQL";
$countStmt = $mysqli->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'] ?? 0;
$countStmt->close();

$totalPages = ceil($totalRows / $perPage);

// Final SQL query
$sql = "SELECT o.*, CONCAT(sm.first_name, ' ', sm.last_name) AS order_maker_name
    FROM orders o
    LEFT JOIN staff sm ON o.order_maker_id = sm.id
    $whereSQL
    ORDER BY o.id DESC
    LIMIT ?, ?";
        
$stmt = $mysqli->prepare($sql);

if (!empty($params)) {
    $bindParams = $params;
    $bindTypes = $types . 'ii';
    $bindParams[] = $offset;
    $bindParams[] = $perPage;
    $stmt->bind_param($bindTypes, ...$bindParams);
} else {
    $stmt->bind_param('ii', $offset, $perPage);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0):
    while($row = $result->fetch_assoc()):
?>
                        <tr class="order-row"
                            data-id="<?= htmlspecialchars($row['id']) ?>"
                            data-customer_name="<?= htmlspecialchars($row['customer_name']) ?>"
                            data-order_date="<?= htmlspecialchars($row['order_date']) ?>"
                            data-delivery_date="<?= htmlspecialchars($row['delivery_date']) ?>"
                            data-order_time="<?php
                                $orderTime = $row['order_time'];
                                if ($orderTime) {
                                    $dt = DateTime::createFromFormat('H:i:s', $orderTime);
                                    if (!$dt) $dt = DateTime::createFromFormat('H:i', $orderTime);
                                    if ($dt) {
                                        echo htmlspecialchars($dt->format('g:i A'));
                                    } else {
                                        echo htmlspecialchars($orderTime);
                                    }
                                }
                            ?>"
                            data-created_by_name="<?= htmlspecialchars($row['created_by_name']) ?>"
                            data-contact="<?= htmlspecialchars($row['contact']) ?>"
                            data-order_maker_name="<?= htmlspecialchars($row['order_maker_name']) ?>"
                            data-order_source="<?= htmlspecialchars($row['order_source']) ?>"
                            data-source_other_text="<?= htmlspecialchars($row['source_other_text']) ?>"
                            data-description="<?= htmlspecialchars($row['description']) ?>"
                            data-payment="<?= htmlspecialchars($row['payment']) ?>"
                            data-bank_detail="<?= htmlspecialchars($row['bank_detail']) ?>"
                            data-ac_detail="<?= htmlspecialchars($row['ac_detail']) ?>"
                            data-card_detail="<?= htmlspecialchars($row['card_detail']) ?>"
                            data-transaction_id="<?= htmlspecialchars($row['transaction_id']) ?>"
                            data-online_amount="<?= htmlspecialchars($row['online_amount']) ?>"
                            data-card_amount="<?= htmlspecialchars($row['card_amount']) ?>"
                            data-total="<?= htmlspecialchars($row['total']) ?>"
                            data-advance="<?= htmlspecialchars($row['advance']) ?>"
                            data-remaining="<?= htmlspecialchars($row['remaining']) ?>"
                            data-created_at="<?= htmlspecialchars(date('d-m-Y h:i A', strtotime($row['created_at']))) ?>"
                            data-file_media="<?= htmlspecialchars($row['file_media']) ?>"
                            data-reason="<?= htmlspecialchars($row['reason']) ?>"
                            data-status="<?= htmlspecialchars($row['status']) ?>"
                            style="cursor:pointer">
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td class="d-none d-sm-table-cell"><?= htmlspecialchars($row['order_date']) ?></td>
                            <td class="d-none d-sm-table-cell"><?= htmlspecialchars($row['delivery_date']) ?></td>
                            <td class="d-none d-sm-table-cell">
                                <?php
                                    $orderTime = $row['order_time'];
                                    if ($orderTime) {
                                        // Try to parse and format as 12-hour with am/pm
                                        $dt = DateTime::createFromFormat('H:i:s', $orderTime);
                                        if (!$dt) $dt = DateTime::createFromFormat('H:i', $orderTime);
                                        if ($dt) {
                                            echo htmlspecialchars($dt->format('g:i A'));
                                        } else {
                                            echo htmlspecialchars($orderTime);
                                        }
                                    }
                                ?></td>
                            <td class="d-none d-sm-table-cell"><?= htmlspecialchars($row['contact']) ?></td>

                            <td class="d-none d-sm-table-cell"><?= htmlspecialchars($row['order_maker_name']) ?></td>
                          <td>
<button class="btn btn-sm btn-status" 
        style="background-color: <?= getStatusColor($row['status']) ?>; color: white;"
        data-id="<?= htmlspecialchars($row['id']) ?>"
        data-status="<?= htmlspecialchars($row['status']) ?>"
        data-total="<?= htmlspecialchars($row['total']) ?>"
        data-remaining="<?= htmlspecialchars($row['remaining']) ?>">
    <?= htmlspecialchars($row['status']) ?>
</button>
</td>
                    <td>
    <?php
    // Start session and include DB config if not already done
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require 'db_config.php';


    $user_id = $_SESSION['user_id'] ?? null;

 
    $can_edit = 0;

    $page_name = 'order_view.php'; 

    if ($user_id === 'user_00001') {

        $can_edit = 1;
    } elseif ($user_id !== null) {
  
        $stmt = $conn->prepare("SELECT can_edit FROM user_permissions WHERE user_id = ? AND page_name = ?");
        $stmt->bind_param("ss", $user_id, $page_name);
        $stmt->execute();
        $stmt->bind_result($can_edit_db);
        $stmt->fetch();
        $can_edit = $can_edit_db ?? 0;
        $stmt->close();
    }
    ?>

    <?php if ($can_edit): ?>

        <a href="order_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
    <?php else: ?>

        <button class="btn btn-sm btn-secondary" disabled>Edit</button>
    <?php endif; ?>
</td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td class="text-center" colspan="10">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php
            $queryString = $_GET;
            $adjacents = 2;
            $start = max(1, $page - $adjacents);
            $end = min($totalPages, $page + $adjacents);

            // First page
            if ($page > 1) {
                $queryString['page'] = 1;
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryString) . '">&laquo; First</a></li>';
            }

            // Previous page
            if ($page > 1) {
                $queryString['page'] = $page - 1;
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryString) . '">&lt;</a></li>';
            }

            // Page numbers
            for ($i = $start; $i <= $end; $i++) {
                $queryString['page'] = $i;
                $active = $i == $page ? 'active' : '';
                echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($queryString) . '">' . $i . '</a></li>';
            }

            // Next page
            if ($page < $totalPages) {
                $queryString['page'] = $page + 1;
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryString) . '">&gt;</a></li>';
            }

            // Last page
            if ($page < $totalPages) {
                $queryString['page'] = $totalPages;
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryString) . '">Last &raquo;</a></li>';
            }
            ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
<?php

if (!isset($bankdetails)) {
    $bankdetails = $mysqli->query("SELECT id, account_name FROM bank_accounts");
}
?>

<div class="modal fade" id="remainingModal" tabindex="-1" role="dialog" aria-labelledby="remainingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="remainingForm">
            <div class="modal-content">
                <div class="modal-header">

                   

                    <h5 class="modal-title" id="remainingModalLabel">Remaining & Payment</h5>

                    <br>
                    
                </div>
                <div class="modal-body">
                    <input type="hidden" id="order_id" name="order_id">
                    <input type="hidden" id="new_status" name="new_status">
                    <input type="hidden" id="total_amount" name="total_amount">

               <div class="form-group mb-3">
    <label for="remaining_amount">Remaining Amount</label>
    
    <input 
        type="number" 
        step="0.01" 
        class="form-control" 
        id="remaining_amount" 
        name="remaining_amount" 
        min="0" 
        required 
        readonly
        style="border:none; background:transparent; font-weight:bold; font-size:large; box-shadow:none; outline:none;">
</div>


                    <div class="form-group mb-3">
                        <label>Payment Method:</label>
                        <div class="checkbox-group" id="payment-method-group">
                            <label><input type="checkbox" name="payment[]" value="Cash" id="payment_cash"> Cash</label>
                            <label><input type="checkbox" name="payment[]" value="Bank" id="payment_bank"> Bank</label>
                            <br>
                            <span id="more-payment-methods" style="display:none;">
                                <label><input type="checkbox" name="payment[]" value="Online" id="payment_online"> Online</label>
                                <label><input type="checkbox" name="payment[]" value="Card" id="payment_card"> POS/Card</label>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-3" id="cash_payment_group" style="display:none;">
                        <label for="cash_payment">Cash Amount:</label>
                        <input type="number" step="0.01" class="form-control" name="cash_payment" id="cash_payment" placeholder="Enter cash Amount" min="0">
                    </div>
                    
                    <div class="form-group mb-3" id="online_amount_group" style="display:none;">
                        <label for="online_amount">Online Amount:</label>
                        <input type="number" step="0.01" class="form-control" name="online_amount" id="online_amount" placeholder="Enter Online Amount" min="0">
                    </div>
                    
                    <div class="form-group mb-3" id="bank_detail_group" style="display:none;">
                        <label for="bank_detail">Online Bank Details:</label>
                        <select class="form-control" name="bank_detail" id="bank_detail">
                            <option value="">Select bank account</option>
                            <?php if ($bankdetails) { while ($row = $bankdetails->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($row['id']) ?>">
                                    <?= htmlspecialchars($row['account_name']) ?>
                                </option>
                            <?php endwhile; } ?>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="ac_detail_group" style="display:none;">
                        <label for="ac_detail">Customer A/C Detail:</label>
                        <input type="text" class="form-control" name="ac_detail" id="ac_detail" placeholder="Enter account details">
                    </div>

                    <div class="form-group mb-3" id="transaction_id_group" style="display:none;">
                        <label for="transaction_id">Transaction ID :</label>
                        <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="Enter Transaction ID">
                    </div>
                    
                    <div class="form-group mb-3" id="card_amount_group" style="display:none;">
                        <label for="card_amount">POS / Card Amount :</label>
                        <input type="number" step="0.01" class="form-control" name="card_amount" id="card_amount" placeholder="Enter Card Amount" min="0">
                    </div>
                    
                    <div class="form-group mb-3" id="pos_bank_detail_group" style="display:none;">
                        <label for="pos_bank_detail">POS Bank Details:</label>
                        <select class="form-control" name="pos_bank_detail" id="pos_bank_detail">
                            <option value="">Select bank account</option>
                            <?php
                            $bankdetails2 = $mysqli->query("SELECT id, account_name, account_number FROM bank_accounts");
                            if ($bankdetails2) {
                                while ($row = $bankdetails2->fetch_assoc()):
                                    $value = (int)$row['id'];
                                    $label = htmlspecialchars($row['account_name'] . ' - ' . $row['account_number']);
                            ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endwhile; } ?>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="card_detail_group" style="display:none;">
                        <label for="card_detail">Card Detail (Last 4 Digits):</label>
                        <input type="text" class="form-control" name="card_detail" id="card_detail" placeholder="1234" maxlength="4">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-primary rounded-pill waves-effect waves-light">Update Status & Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>



                <!-- Order Detail Modal -->
                <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="orderDetailModalLabel">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <table class="table-hover mb-0">
                            <tbody id="order-detail-body">
                                <!-- Details will be injected here -->
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Status Change Modal -->
                <div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="statusChangeModalLabel">Change Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" id="status-order-id">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-status-option" data-status="Processing" style="background-color: <?= getStatusColor('Processing') ?>; color: white;">Processing</button>
                            <button type="button" class="btn btn-status-option" data-status="Assigned" style="background-color: <?= getStatusColor('Assigned') ?>; color: white;">Assigned</button>
                            <button type="button" class="btn btn-status-option" data-status="Completed" style="background-color: <?= getStatusColor('Completed') ?>; color: white;">Completed</button>
                            <button type="button" class="btn btn-status-option" data-status="Delivered" style="background-color: <?= getStatusColor('Delivered') ?>; color: white;">Delivered</button>
                            <button type="button" class="btn btn-status-option" data-status="Cancelled" style="background-color: <?= getStatusColor('Cancelled') ?>; color: white;">Cancelled</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Image Zoom Modal -->
                <div class="modal fade" id="imageZoomModal" tabindex="-1" aria-labelledby="imageZoomModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content bg-transparent border-0">
                      <div class="modal-body text-center p-0">
                        <img id="zoomedImage" src="" alt="Zoomed File Media" style="max-width: 98vw; max-height: 90vh; border-radius: 8px; box-shadow: 0 0 10px #0008;">
                      </div>
                    </div>
                  </div>
                </div>

                <?php
                if ($result) $result->free();
                $mysqli->close();
                
                // Function to get color for status
                function getStatusColor($status) {
                    $colors = [
                        'Processing' => '#17a2b8',
                        'Assigned' => '#6610f2',
                        'Completed' => '#28a745',
                        'Delivered' => '#FFA500',
                        'Cancelled' => '#dc3545'
                    ];
                    return $colors[$status] ?? '#6c757d';
                }
                ?>
                <style>
               #order-detail-body {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
}

/* Each detail block */
.detail-cell {
    display: flex;
    flex-wrap: wrap;
    border: 1px solid #dee2e6;
    width: 100%;
    box-sizing: border-box;
}

/* Label on the left (desktop) */
.detail-label {
    flex: 0 0 30%;
    max-width: 30%;
    padding: 0.75rem;
    font-weight: 600;
 
    border-right: 1px solid #dee2e6;
    box-sizing: border-box;
}

/* Value on the right (desktop) */
.detail-value {
    flex: 0 0 70%;
    max-width: 70%;
    padding: 0.75rem;
    box-sizing: border-box;
}

@media (max-width: 575.98px) {
    .detail-cell {
        display: flex;
        flex-wrap: wrap;
        width: 100%;
        margin-bottom: 4px; /* Optional: spacing between rows */
    }

    .detail-label,
    .detail-value {
        flex: 0 0 50%; /* Forces 50% width for both */
        max-width: 50%; /* Ensures they don't exceed half */
        padding: 6px 4px; /* Adjust padding for better spacing */
        box-sizing: border-box; /* Includes padding in width calculation */
    }

    .detail-label {
        font-weight: 600; /* Bold labels for better distinction */
        
        border-right: 1px solid #dee2e6; /* Optional: divider */
    }

    .detail-value {
        word-break: break-word; /* Prevents text overflow */
    }
}

                </style>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let currentOrderId = null;
                    let currentOrderStatus = null;

                    const statusOrder = [
                        'Processing',
                        'Assigned',
                        'Completed',
                        'Delivered',
                        'Cancelled'
                    ];

                    document.querySelectorAll('.order-row').forEach(function(row) {
                        row.addEventListener('click', function(e) {
                            if (e.target.classList.contains('btn-status')) {
                                return;
                            }
                            // Define fields for display
                            const fields = [
                                {label: ' Invoice ID', key: 'id'},
                                {label: 'Order Date', key: 'order_date'},
                                {label: 'Delivery Date', key: 'delivery_date'},
                                {label: 'Deliver Time', key: 'order_time'},
                                {label: 'Customer Name', key: 'customer_name'},
                                // Only show if value is not empty/null/0
                                ...(row.getAttribute('data-contact') ? [{label: 'Contact', key: 'contact'}] : []),
                                ...(row.getAttribute('data-created_by_name') ? [{label: 'Order Taker', key: 'created_by_name'}] : []),
                                ...(row.getAttribute('data-order_maker_name') ? [{label: 'Order Maker', key: 'order_maker_name'}] : []),
                                ...(row.getAttribute('data-reason') ? [{label: 'Staff Change Reason', key: 'reason'}] : []),
                                ...(row.getAttribute('data-order_source') ? [{label: 'Order Source', key: 'order_source'}] : []),
                                ...(row.getAttribute('data-source_other_text') ? [{label: 'Source Other', key: 'source_other_text'}] : []),
                                ...(row.getAttribute('data-description') ? [{label: 'Description', key: 'description'}] : []),
                                ...(row.getAttribute('data-payment') ? [{label: 'Payment Method', key: 'payment'}] : []),
                                ...(row.getAttribute('data-bank_detail') ? [{label: 'Bank Detail', key: 'bank_detail'}] : []),
                                ...(row.getAttribute('data-ac_detail') ? [{label: 'AC Detail', key: 'ac_detail'}] : []),
                                ...(row.getAttribute('data-card_detail') ? [{label: 'Card Detail', key: 'card_detail'}] : []),
                                ...(row.getAttribute('data-transaction_id') ? [{label: 'Transaction ID', key: 'transaction_id'}] : []),
                                // Only show if value is not 0 or null/empty
                                ...(parseFloat(row.getAttribute('data-online_amount')) > 0
                                    ? [{label: 'Online Amount', key: 'online_amount'}] : []),
                                ...(parseFloat(row.getAttribute('data-card_amount')) > 0
                                    ? [{label: 'Card Amount', key: 'card_amount'}] : []),
                                ...(parseFloat(row.getAttribute('data-total')) > 0
                                    ? [{label: 'Total', key: 'total'}] : []),
                                ...(parseFloat(row.getAttribute('data-advance')) > 0
                                    ? [{label: 'Advance', key: 'advance'}] : []),
                                ...(parseFloat(row.getAttribute('data-remaining')) > 0
                                    ? [{label: 'Remaining', key: 'remaining'}] : []),
                                {label: 'Created At', key: 'created_at'},
                                {label: 'File Media', key: 'file_media', isFile: true},
                                {label: 'Status', key: 'status', isStatus: true},

                                {label: '', key: 'id', isRemainingPayments: true, colspan: 2, hideLabel: true }
                            ];

                            let html = '';
                            let cells = [];
                            fields.forEach(function(field, idx) {
                                let cellHtml = '';
                                if (field.isFile) {
                                    let fileUrls = row.getAttribute('data-' + field.key) ?? '';
                                    let imagesHtml = '';
                                    let linksHtml = '';
                                    if (fileUrls) {
                                        let files = fileUrls.split(',').map(f => f.trim()).filter(f => f.length > 0);
                                        files.forEach(function(fileUrl) {
                                            let displayUrl = fileUrl;
                                            if (!/^https?:\/\//i.test(displayUrl) && !displayUrl.startsWith('uploads/')) {
                                                displayUrl = 'uploads/' + displayUrl;
                                            }
                                            if (/\.(jpe?g|png|gif|bmp|webp|svg)$/i.test(displayUrl)) {
                                                imagesHtml += `<img src="${displayUrl}" alt="File Media" class="img-thumbnail file-media-thumb" style="max-width:80px;max-height:80px;cursor:zoom-in;margin-right:5px;margin-bottom:5px;" data-img="${displayUrl}" onerror="this.onerror=null;this.src='assets/images/image-missing.png';">`;
                                            } else {
                                                linksHtml += `<a href="${displayUrl}" target="_blank" style="display:block;"><i class="mdi mdi-file-document-outline"></i> ${displayUrl.split('/').pop()}</a>`;
                                            }
                                        });
                                    }
                                    cellHtml = `
                                        <div class="detail-cell">
                                            <span class="detail-label">${field.label}</span>
                                            <span class="detail-value">${imagesHtml}${linksHtml || '<span class="text-muted">No file</span>'}</span>
                                        </div>
                                    `;
                                } else if (field.isStatus) {
                                    let value = row.getAttribute('data-' + field.key) ?? '';
                                    let color = '';
                                    switch (value) {
                                        case 'Processing': color = '#17a2b8'; break;
                                        case 'Assigned': color = '#6610f2'; break;
                                        case 'Completed': color = '#28a745'; break;
                                        case 'Delivered': color = '#FFA500'; break;
                                        case 'Cancelled': color = '#dc3545'; break;
                                        default: color = '#6c757d';
                                    }
                                    cellHtml = `
                                        <div class="detail-cell">
                                            <span class="detail-label">${field.label}</span>
                                            <span class="detail-value">
                                                <span class="badge" style="background:${color};color:#fff;padding:0.5em 1em;font-size:1em;border-radius:1em;">
                                                    ${value}
                                                </span>
                                            </span>
                                        </div>
                                    `;
                                } else if (field.isRemainingPayments) {
                                    cellHtml = `
                                       <br><br> <div style="    font-weight: bold;
    margin: auto;
    font-size: 16px;">Remaining Payment</div><br><br>
                                        <div class="detail-cell remaining-payments-cell" id="remaining-payments-cell-${row.getAttribute('data-id')}">
                                            <span class="detail-value" style="flex: 0 0 100%; max-width: 100%; padding: 0.75rem; box-sizing: border-box;"><span class="text-muted">Loading...</span></span>
                                        </div>
                                    `;
                                } else {
                                    let value = row.getAttribute('data-' + field.key) ?? '';
                                    if (field.key === 'total' || field.key === 'advance' || field.key === 'remaining' || field.key === 'online_amount' || field.key === 'card_amount') {
                                        value = value ? `<span class="fw-bold text-primary">₨ ${parseFloat(value).toLocaleString()}</span>` : '';
                                    }
                                    cellHtml = `
                                        <div class="detail-cell">
                                            <span class="detail-label">${field.label}</span>
                                            <span class="detail-value">${value || '<span class="text-muted">-</span>'}</span>
                                        </div>
                                    `;
                                }
                                cells.push(cellHtml);
                            });

                            html = cells.join('');

                            const detailBody = document.getElementById('order-detail-body');
                            if (!detailBody) {
                                console.error('#order-detail-body element not found!');
                                return;
                            }

                            detailBody.innerHTML = html;

                            // Fetch remaining payments data
                            let orderId = row.getAttribute('data-id');
                            fetch('get_order_remaining_payments.php?order_id=' + encodeURIComponent(orderId))
                                .then(res => {
                                    if (!res.ok) throw new Error('Network response was not ok');
                                    return res.json();
                                })
                                .then(data => {
                                    let cell = document.getElementById('remaining-payments-cell-' + orderId);
                                    if (!cell) return;
                                    let payments = [];
                                    if (Array.isArray(data)) {
                                        payments = data;
                                    } else if (data && Array.isArray(data.payments)) {
                                        payments = data.payments;
                                    }
                                    if (payments.length > 0) {
                                        let paymentsHtml = `
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0 remaining-payments-table">
                                                <thead>
                                                    <tr>
                                                        
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Bank</th>
                                                        <th>AC Detail</th>
                                                        <th>Card Detail</th>
                                                        <th>Transaction ID</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                        `;
                                        let totalAmount = 0;
                                        payments.forEach(function(payment, idx) {
                                            totalAmount += payment.amount ? parseFloat(payment.amount) : 0;
                                            paymentsHtml += `
                                                <tr>
                                                   
                                                    <td><span class="badge bg-info">${payment.payment_type ? payment.payment_type : ''}</span></td>
                                                    <td><span class="fw-bold text-success">₨ ${payment.amount ? parseFloat(payment.amount).toLocaleString() : ''}</span></td>
                                                    <td>${payment.bank_detail_name ? payment.bank_detail_name : ''}</td>
                                                    <td>${payment.ac_detail ? payment.ac_detail : ''}</td>
                                                    <td>${payment.card_detail ? payment.card_detail : ''}</td>
                                                    <td>${payment.transaction_id ? payment.transaction_id : ''}</td>
                                                </tr>
                                            `;
                                        });
                                        paymentsHtml += `
                                           <div class="d-flex flex-wrap align-items-center justify-content-between py-2" style=" border-top: 1px solid #dee2e6;">
                                                <div class="fw-bold text-end" style="flex: 1 1 40%; min-width: 120px;">
                                                  Past Remaining Payment
                                                </div>
                                                <div class="fw-bold " style="flex: 1 1 20%; min-width: 100px;">
                                                   &nbsp; ₨ ${totalAmount.toLocaleString()}
                                                </div>
                                        `;
                                        paymentsHtml += `
                                                </tbody>
                                            </table>
                                              
                                                <div class="fw-bold text-primary" style="text-align: center;
    margin-top: 9px;
    color:rgb(255, 68, 68) !important;
    font-size: 18px;">
                                                    Paid  Amount&nbsp; ₨ ${totalAmount.toLocaleString()}
                                                </div>
                                            </div>
                                        </div>
                                        <style>
                                        /* Desktop styles */
                                        .remaining-payments-table th, .remaining-payments-table td {
                                            vertical-align: middle;
                                            text-align: center;
                                            font-size: 15px;
                                            padding: 0.5rem 0.4rem;
                                            border: 1px solid #dee2e6;
                                        }
                                        .remaining-payments-table th {
                                         
                                            font-weight: 600;
                                           
                                        }
                                        .remaining-payments-table .table-total-row {
                                       
                                        }
                                        /* Mobile styles */
                                        @media (max-width: 575.98px) {
                                            .remaining-payments-table, .remaining-payments-table thead, .remaining-payments-table tbody, .remaining-payments-table tr, .remaining-payments-table th, .remaining-payments-table td {
                                                display: block !important;
                                                width: 100% !important;
                                            }
                                            .remaining-payments-table thead {
                                                display: none !important;
                                            }
                                            .remaining-payments-table tr {
                                                margin-bottom: 1rem;
                                                border-bottom: 2px solid #e9ecef;
                                              
                                            }
                                            .remaining-payments-table td {
                                                text-align: left;
                                                padding-left: 40%;
                                                position: relative;
                                                min-height: 38px;
                                                font-size: 14px;
                                                border: none;
                                                border-bottom: 1px solid #f1f1f1;
                                            }
                                            .remaining-payments-table td:before {
                                                position: absolute;
                                                left: 0.75rem;
                                                top: 0.5rem;
                                                width: 38%;
                                                white-space: nowrap;
                                                font-weight: 600;
                                                color: #888;
                                                font-size: 13px;
                                                content: attr(data-label);
                                            }
                                            .remaining-payments-table .table-total-row td {
                                              
                                                font-weight: bold;
                                           
                                            }
                                        }
                                        </style>
                                        `;

                                        // Add data-label attributes for mobile
                                        setTimeout(() => {
                                            const table = document.querySelector(`#remaining-payments-cell-${orderId} .remaining-payments-table`);
                                            if (table) {
                                                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
                                                table.querySelectorAll('tbody tr').forEach(tr => {
                                                    tr.querySelectorAll('td').forEach((td, idx) => {
                                                        td.setAttribute('data-label', headers[idx] || '');
                                                    });
                                                });
                                            }
                                        }, 100);

                                        cell.querySelector('.detail-value').innerHTML = paymentsHtml;
                                    } else {
                                        cell.querySelector('.detail-value').innerHTML = '<span class="text-muted">No payments found for this order</span>';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching payments:', error);
                                    let cell = document.getElementById('remaining-payments-cell-' + orderId);
                                    if (cell) cell.querySelector('.detail-value').innerHTML = `<span class="text-danger">Error loading payments: ${error.message}</span>`;
                                });

                            var modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                            modal.show();
                            

                            setTimeout(function() {
                                document.querySelectorAll('.file-media-thumb').forEach(function(img) {
                                    img.onclick = function(e) {
                                        e.stopPropagation();
                                        document.getElementById('zoomedImage').src = img.getAttribute('data-img');
                                        var zoomModal = new bootstrap.Modal(document.getElementById('imageZoomModal'));
                                        zoomModal.show();
                                    };
                                });
                            }, 100);
                        });
                    });

                    document.querySelectorAll('.btn-status').forEach(function(btn) {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            currentOrderId = this.getAttribute('data-id');
                            currentOrderStatus = this.getAttribute('data-status');

                            // --- MODIFIED LOGIC START ---
                            // Only for Completed status, check remaining
                            if (currentOrderStatus === 'Completed') {
                                // Find the row and get remaining value
                                let row = this.closest('tr');
                                let remaining = 0;
                                if (row && row.hasAttribute('data-remaining')) {
                                    remaining = parseFloat(row.getAttribute('data-remaining')) || 0;
                                }
                                if (remaining === 0) {
                                    // If remaining is 0, directly show status change modal for Delivered
                                    // Simulate click on Delivered status option
                                    document.getElementById('status-order-id').value = currentOrderId;
                                    document.querySelectorAll('.btn-status-option').forEach(function(option) {
                                        option.classList.remove('active');
                                        option.disabled = false;
                                        if (option.getAttribute('data-status') === 'Delivered') {
                                            option.classList.add('active');
                                        }
                                        
                                    });
                                    var modalElement = document.getElementById('statusChangeModal');
                                    if (typeof bootstrap !== "undefined" && bootstrap.Modal.getInstance(modalElement)) {
                                        bootstrap.Modal.getInstance(modalElement).hide();
                                    }
                                    setTimeout(function() {
                                        var modal = new bootstrap.Modal(modalElement);
                                        modal.show();
                                          
                                    }, 50);
                                    return;
                                } else {
                                    // If remaining > 0, show price popup (remainingModal)
                                    const total = parseFloat(this.getAttribute('data-total')) || 0;
                                    document.getElementById('order_id').value = currentOrderId;
                                    document.getElementById('new_status').value = 'Delivered';
                                    document.getElementById('total_amount').value = total;
                                    document.getElementById('remaining_amount').value = remaining.toFixed(2);

                                    // Reset payment fields and show modal
                                    if (typeof resetFields === 'function') resetFields();
                                    if (typeof updateVisibility === 'function') updateVisibility();
                                    var priceModal = new bootstrap.Modal(document.getElementById('remainingModal'));
                                    priceModal.show();
                                  
                                    return;
                                }
                            }
                            // --- MODIFIED LOGIC END ---

                            document.getElementById('status-order-id').value = currentOrderId;

                            document.querySelectorAll('.btn-status-option').forEach(function(option) {
                                option.classList.remove('active');
                                option.disabled = false;
                                const optionStatus = option.getAttribute('data-status');
                                const currentIdx = statusOrder.indexOf(currentOrderStatus);
                                const optionIdx = statusOrder.indexOf(optionStatus);

                                if (
                                    (optionStatus === 'Cancelled') ||
                                    (optionIdx === currentIdx + 1) ||
                                    (optionStatus === currentOrderStatus)
                                ) {
                                    option.disabled = false;
                                } else {
                                    option.disabled = true;
                                }

                                if (optionStatus === currentOrderStatus) {
                                    option.classList.add('active');
                                    option.innerHTML = `<i class="mdi mdi-check"></i> ${currentOrderStatus}`;
                                } else {
                                    option.innerHTML = optionStatus;
                                }
                            });

                            var modalElement = document.getElementById('statusChangeModal');
                            if (typeof bootstrap !== "undefined" && bootstrap.Modal.getInstance(modalElement)) {
                                bootstrap.Modal.getInstance(modalElement).hide();
                            }
                            setTimeout(function() {
                                var modal = new bootstrap.Modal(modalElement);
                                modal.show();
                                
                            }, 50);
                        });
                    });

                    document.querySelectorAll('.btn-status-option').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            if (btn.disabled) return;
                            const newStatus = this.getAttribute('data-status');
                            const orderId = document.getElementById('status-order-id').value;
                            if (!orderId) return;

                            // Keep the clicked button reference for color
                            const clickedBtn = this;

                            fetch('update_order_status.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ id: orderId, status: newStatus })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    let row = document.querySelector('.order-row[data-id="' + orderId + '"]');
                                    if (row) {
                                        row.setAttribute('data-status', newStatus);
                                        let statusBtn = row.querySelector('.btn-status');
                                        if (statusBtn) {
                                            statusBtn.textContent = newStatus;
                                            statusBtn.setAttribute('data-status', newStatus);
                                            // Update background color to clicked button's background color
                                            statusBtn.style.backgroundColor = getComputedStyle(clickedBtn).backgroundColor;
                                        }
                                    }

                                    var modal = bootstrap.Modal.getInstance(document.getElementById('statusChangeModal'));
                                    if (modal) modal.hide();

                                    alert('Status updated successfully!');
                                } else {
                                    alert('Failed to update status.');
                                }
                            })
                            .catch(() => alert('Error updating status.'));
                        });
                    });

                    document.getElementById('imageZoomModal').addEventListener('click', function() {
                        var zoomModal = bootstrap.Modal.getInstance(document.getElementById('imageZoomModal'));
                        if (zoomModal) zoomModal.hide();
                    });
                });
                </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentOrderId = null;
    let originalRemaining = 0; // Store initial remaining amount

    const modalElement = document.getElementById('remainingModal');
    const bootstrapModal = new bootstrap.Modal(modalElement);
    const remainingForm = document.getElementById('remainingForm');

    const cashCheckbox = document.getElementById('payment_cash');
    const bankCheckbox = document.getElementById('payment_bank');
    const onlineCheckbox = document.getElementById('payment_online');
    const cardCheckbox = document.getElementById('payment_card');

    const cashAmountInput = document.getElementById('cash_payment');
    const onlineAmountInput = document.getElementById('online_amount');
    const cardAmountInput = document.getElementById('card_amount');

    const totalAmountInput = document.getElementById('total_amount');
    const remainingAmountInput = document.getElementById('remaining_amount');

    const fieldMap = {
        bank_detail: document.getElementById('bank_detail'),
        ac_detail: document.getElementById('ac_detail'),
        card_detail: document.getElementById('card_detail'),
        transaction_id: document.getElementById('transaction_id'),
        pos_bank_detail: document.getElementById('pos_bank_detail')
    };

    function resetFields() {
        cashCheckbox.checked = false;
        bankCheckbox.checked = false;
        onlineCheckbox.checked = false;
        cardCheckbox.checked = false;

        cashAmountInput.value = '';
        onlineAmountInput.value = '';
        cardAmountInput.value = '';
        Object.values(fieldMap).forEach(el => el.value = '');

        document.querySelectorAll('.payment-group').forEach(el => el.style.display = 'none');

        originalRemaining = 0;
    }

    function updateVisibility() {
        document.getElementById('cash_payment_group').style.display = cashCheckbox.checked ? 'block' : 'none';
        document.getElementById('more-payment-methods').style.display = bankCheckbox.checked ? 'block' : 'none';

        const onlineVisible = bankCheckbox.checked && onlineCheckbox.checked;
        const cardVisible = bankCheckbox.checked && cardCheckbox.checked;

        document.getElementById('online_amount_group').style.display = onlineVisible ? 'block' : 'none';
        document.getElementById('transaction_id_group').style.display = onlineVisible ? 'block' : 'none';
        document.getElementById('bank_detail_group').style.display = onlineVisible ? 'block' : 'none';
        document.getElementById('ac_detail_group').style.display = onlineVisible ? 'block' : 'none';

        document.getElementById('card_amount_group').style.display = cardVisible ? 'block' : 'none';
        document.getElementById('card_detail_group').style.display = cardVisible ? 'block' : 'none';
        document.getElementById('pos_bank_detail_group').style.display = cardVisible ? 'block' : 'none';
    }

 function calculateRemaining() {
    const total = originalRemaining;
    let paid = 0;

    const cash = parseFloat(cashAmountInput.value) || 0;
    const online = parseFloat(onlineAmountInput.value) || 0;
    const card = parseFloat(cardAmountInput.value) || 0;

    paid = cash + online + card;

    const submitBtn = remainingForm.querySelector('button[type="submit"]');

    // Overpaid
    if (paid > total) {
        alert(`You can only pay up to ${total}.`);
        if (cashAmountInput === document.activeElement) {
            cashAmountInput.value = '';
            cashAmountInput.dispatchEvent(new Event('input'));
        }
        if (onlineAmountInput === document.activeElement) {
            onlineAmountInput.value = '';
            onlineAmountInput.dispatchEvent(new Event('input'));
        }
        if (cardAmountInput === document.activeElement) {
            cardAmountInput.value = '';
            cardAmountInput.dispatchEvent(new Event('input'));
        }

        if (submitBtn) submitBtn.disabled = true;
        return;
    }

    // Underpaid
    if (paid < total) {
        if (submitBtn) submitBtn.disabled = true;
        remainingAmountInput.value = (total - paid).toFixed(2);
        return;
    }

    // Exact amount paid
    if (submitBtn) submitBtn.disabled = false;
    remainingAmountInput.value = '0.00';
}


    // Save originalRemaining when modal is shown
    modalElement.addEventListener('show.bs.modal', function () {
        originalRemaining = parseFloat(remainingAmountInput.value) || 0;
    });

    // Event listeners for checkboxes:
    cashCheckbox.addEventListener('change', () => {
        updateVisibility();
        handleCheckboxAmounts();
    });

    [bankCheckbox, onlineCheckbox, cardCheckbox].forEach(cb => {
        cb.addEventListener('change', () => {
            updateVisibility();
            handleCheckboxAmounts();
        });
    });

    // Inputs trigger recalculation on change
    [cashAmountInput, onlineAmountInput, cardAmountInput].forEach(input => {
        input.addEventListener('input', calculateRemaining);
    });

    fieldMap.card_detail.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 4);
    });



    document.querySelectorAll('.btn-status').forEach(btn => {
        btn.addEventListener('click', function () {
            const status = this.dataset.status;
            if (status !== 'Completed') return;

            currentOrderId = this.dataset.id;
            const total = parseFloat(this.dataset.total) || 0;
            const remaining = parseFloat(this.closest('tr').dataset.remaining) || 0;

            document.getElementById('order_id').value = currentOrderId;
            document.getElementById('new_status').value = 'Delivered';
            totalAmountInput.value = total;
            remainingAmountInput.value = remaining.toFixed(2);

            resetFields();
            updateVisibility();
            bootstrapModal.show();
        });
    });

    remainingForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const payments = [];

        if (cashCheckbox.checked) {
    const amount = parseFloat(cashAmountInput.value);
    if (!amount || amount <= 0) return alert('Enter valid cash amount.');
    payments.push({
        payment_type: 'Cash',
        amount: amount
    });
}

if (bankCheckbox.checked && onlineCheckbox.checked) {
    const amount = parseFloat(onlineAmountInput.value);
    if (!amount || amount <= 0) return alert('Enter valid online amount.');
    if (!fieldMap.bank_detail.value) return alert('Select bank for online payment.');
    payments.push({
        payment_type: 'Online',
        amount: amount,
        bank_detail: fieldMap.bank_detail.value,
        ac_detail: fieldMap.ac_detail.value,
        transaction_id: fieldMap.transaction_id.value
    });
}

if (bankCheckbox.checked && cardCheckbox.checked) {
    const amount = parseFloat(cardAmountInput.value);
    if (!amount || amount <= 0) return alert('Enter valid card amount.');
    if (!fieldMap.pos_bank_detail.value) return alert('Select POS bank.');
    if (fieldMap.card_detail.value.length !== 4) return alert('Enter last 4 digits of card.');
    payments.push({
        payment_type: 'Card',
        amount: amount,
        card_detail: fieldMap.card_detail.value,
        pos_bank_detail: fieldMap.pos_bank_detail.value,
        bank_detail: fieldMap.pos_bank_detail.value // reuse field
    });
}


        if (payments.length === 0) return alert('Select at least one payment method.');

        const payload = {
            id: currentOrderId,
            status: document.getElementById('new_status').value,
            remaining: parseFloat(remainingAmountInput.value) || 0,
            payments: payments
        };

        fetch('update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Order updated!');
                bootstrapModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Update failed.');
            }
        })
        .catch(() => alert('Server error.'));
    });
});
</script>

                <style>
                @media (max-width: 576px) {
                    #orderDetailModal .modal-dialog,
                    #statusChangeModal .modal-dialog {
                        max-width: 98vw;
                        margin: 0.5rem auto;
                    }
                    #orderDetailModal .modal-content,
                    #statusChangeModal .modal-content {
                        border-radius: 0.5rem;
                    }
                    #order-detail-body th, #order-detail-body td {
                        font-size: 14px;
                        /* padding: 0.5rem; */
                        word-break: break-word;
                    }
                    #order-detail-body th {
                        width: 40%;
                    }
                    #order-detail-body td {
                        width: 60%;
                    }
                    .btn-status-option {
                        margin-bottom: 5px;
                    }
                    .btn-status-option.active {
                        border: 2px solid #000;
                        font-weight: bold;
                    }
                }

                
                </style>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->

                                    </div>
        </div>
        <!-- END wrapper -->
        
        <!-- App js -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.js"></script>

        <!-- third party js -->
        <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
        <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
        <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <!-- third party js ends -->

        <!-- Datatables js -->
        <script src="assets/js/pages/datatables.js"></script>

        
    </body>
</html>

