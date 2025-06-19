<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect POST data

    $account_number = $_POST['account_number'];
    $account_name = $_POST['account_name'];
    $branch_name = $_POST['branch_name'];
    $ifsc_code = $_POST['ifsc_code'];
    $remarks = $_POST['remarks'];
    $created_by = $_SESSION['user_id'] ?? 1; // fallback or get from session
    $status = 'Active';

 $stmt = $conn->prepare("INSERT INTO bank_accounts (
     account_number, account_name, branch_name, ifsc_code, remarks, created_by, status
) VALUES (?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("sssssis", $account_number, $account_name, $branch_name, $ifsc_code, $remarks, $created_by, $status);


    // Execute
    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Record Updated Successfully.'
                });
            });
        </script>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

    <head>
        <meta charset="utf-8" />
        <title>Bank Details</title>
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
                    <h4 style="color: white;">Order Tracking</h4>
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
                <h4 class="header-title">Bank Account</h4>

                <!-- Bank Account Form -->
                <?php
                // Check if editing
                $isEdit = false;
                $editData = [
                    'id' => '',
                    'account_number' => '',
                    'account_name' => '',
                    'branch_name' => '',
                    'ifsc_code' => '',
                    'remarks' => ''
                ];

                if (isset($_GET['edit_id'])) {
                    $isEdit = true;
                    $edit_id = intval($_GET['edit_id']);
                    $stmt = $conn->prepare("SELECT id, account_number, account_name, branch_name, ifsc_code, remarks FROM bank_accounts WHERE id = ?");
                    $stmt->bind_param("i", $edit_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $editData = $row;
                    }
                    $stmt->close();
                }

                // Handle update
                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
                    $edit_id = intval($_POST['edit_id']);
                    $account_number = $_POST['account_number'];
                    $account_name = $_POST['account_name'];
                    $branch_name = $_POST['branch_name'];
                    $ifsc_code = $_POST['ifsc_code'];
                    $remarks = $_POST['remarks'];

                    $stmt = $conn->prepare("UPDATE bank_accounts SET account_number=?, account_name=?, branch_name=?, ifsc_code=?, remarks=? WHERE id=?");
                    if ($stmt === false) {
                        die("Prepare failed: " . htmlspecialchars($conn->error));
                    }
                    $stmt->bind_param("sssssi", $account_number, $account_name, $branch_name, $ifsc_code, $remarks, $edit_id);

                    if ($stmt->execute()) {
                        echo "<p style='color:green;'>Bank account updated successfully.</p>";
                        // Refresh edit data
                        $editData = [
                            'id' => $edit_id,
                            'account_number' => $account_number,
                            'account_name' => $account_name,
                            'branch_name' => $branch_name,
                            'ifsc_code' => $ifsc_code,
                            'remarks' => $remarks
                        ];
                    } else {
                        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                }
                ?>

                <form method="POST" action="bank_account.php<?php echo $isEdit ? '?edit_id=' . htmlspecialchars($editData['id']) : ''; ?>">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($editData['id']); ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_number">Account Number:</label>
                                <input type="text" class="form-control" name="account_number" id="account_number" required value="<?php echo htmlspecialchars($editData['account_number']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_name">Account Name:</label>
                                <input type="text" class="form-control" name="account_name" id="account_name" required value="<?php echo htmlspecialchars($editData['account_name']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="branch_name">Bank Name:</label>
                                <input type="text" class="form-control" name="branch_name" id="branch_name" value="<?php echo htmlspecialchars($editData['branch_name']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ifsc_code">IBAN Code:</label>
                                <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" value="<?php echo htmlspecialchars($editData['ifsc_code']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks:</label>
                        <textarea class="form-control" name="remarks" id="remarks"><?php echo htmlspecialchars($editData['remarks']); ?></textarea>
                    </div>
<br>
                    <input type="submit" class="btn btn-primary" value="<?php echo $isEdit ? 'Update Bank Account' : 'Insert Bank Account'; ?>">
                <?php
                // Handle delete request
                if (isset($_GET['delete_id'])) {
                    $delete_id = intval($_GET['delete_id']);
                    $stmt = $conn->prepare("DELETE FROM bank_accounts WHERE id = ?");
                    if ($stmt) {
                        $stmt->bind_param("i", $delete_id);
                        if ($stmt->execute()) {
                            echo "<p style='color:green;'>Bank account deleted successfully.</p>";
                        } else {
                            echo "<p style='color:red;'>Error deleting bank account: " . $stmt->error . "</p>";
                        }
                        $stmt->close();
                    } else {
                        echo "<p style='color:red;'>Prepare failed: " . htmlspecialchars($conn->error) . "</p>";
                    }
                }

                // Fetch all bank accounts to display in a table
                $result = $conn->query("SELECT id, account_number, account_name, branch_name, ifsc_code, remarks, status FROM bank_accounts ORDER BY id DESC");
                ?>

                <h4 class="header-title mt-5">Bank Account List</h4>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Account Number</th>
                                <th>Account Name</th>
                                <th>Branch Name</th>
                                <th>IBAN Code</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['account_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['account_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ifsc_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <a href="?edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this bank account?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>



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
                <!-- App js -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.js"></script>

        <!-- Sweet Alerts js -->
        <script src="assets/libs/sweetalert2/sweetalert2.all.min.js"></script>

        <!-- Sweet alert Demo js-->
        <script src="assets/js/pages/sweet-alerts.js"></script>

        
    </body>
</html>

