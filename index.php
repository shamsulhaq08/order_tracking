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

?>
  
<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

<head>
    <meta charset="utf-8" />
    <title>Dashboard </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link href="assets/libs/morris.js/morris.css" rel="stylesheet" type="text/css" />

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
                            <div class="col-lg-6">
                              
                                <h4 class="page-title mb-0">Dashboard</h4>
                             
                            </div>
                            <div class="col-lg-6">
                               <div class="d-none d-lg-block">
                                <!-- <ol class="breadcrumb m-0 float-end">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Drezoc</a></li>
                                    <li class="breadcrumb-item active">Dashboard</li>
                                </ol> -->
                               </div>
                            </div>
                        </div>

                        <!-- Popup Welcome Modal with Blurred Background -->
                        <style>
                            user-select: none;
                            /* Blur effect for the background when modal is open */
                            body.modal-blur {
                                filter: blur(5px);
                                transition: filter 0.3s;
                                pointer-events: none;
                            }
                            /* Overlay for extra dim effect */
                            #welcomeModalBackdrop {
                                position: fixed;
                                top: 0; left: 0; right: 0; bottom: 0;
                                background: rgba(30, 34, 45, 0.45);
                                backdrop-filter: blur(5px);
                                z-index: 1040;
                                display: none;
                            }
                            #welcomeModal.show ~ #welcomeModalBackdrop {
                                display: block;
                            }
                            /* Modal content custom style */
                            #welcomeModal .modal-content {
                                background: rgba(34, 40, 49, 0.95);
                                border-radius: 1rem;
                                color: #fff;
                            }
                        </style>
<!-- 
                        <?php //if (isset($username)): ?>
                            <div class="alert alert-primary alert-dismissible fade show" role="alert" style="margin-top: 30px;">
                                <strong>Welcome<?php //echo ', ' . htmlspecialchars($username); ?>!</strong> 
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var alertEl = document.querySelector('.alert');
                                    if(alertEl){
                                        setTimeout(function() {
                                            var alert = new bootstrap.Alert(alertEl);
                                            alert.close();
                                        }, 10000); // 10 seconds
                                    }
                                });
                            </script>
                        <?php //endif; ?> -->


                    <div class="row">
                        <?php
                        // Database connection (adjust credentials as needed)
                        $conn = new mysqli('localhost', 'root', '12345', 'order_tracking');
                        $totalOrders = 0;
                        if ($conn->connect_error) {
                            $totalOrders = 'DB Error';
                        } else {
                            $result = $conn->query("SELECT COUNT(*) as cnt FROM orders");
                            if ($result && $row = $result->fetch_assoc()) {
                                $totalOrders = $row['cnt'];
                            }
                            $conn->close();
                        }
                        ?>
                        <div class="col-lg-6 col-xl-3">
                         <a href="order.php">   
                        <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Orders</h6>
                                            <span class="h3 mb-0"><?php echo $totalOrders; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge badge-soft-info">All Time</span>
                                        </div>
                                    </div> <!-- end row -->
                                    <div id="sparkline1" class="mt-3"></div>
                                </div> <!-- end card-body-->
                                  </a>
                            </div> <!-- end card-->
                        </div> <!-- end col-->
                      

                        <div class="col-lg-6 col-xl-3">
                            <?php
                            // Database connection (adjust credentials as needed)
                            $conn = new mysqli('localhost', 'root', '12345', 'order_tracking');
                            $totalStaff = 0;
                            if ($conn->connect_error) {
                                $totalStaff = 'DB Error';
                            } else {
                                $result = $conn->query("SELECT COUNT(*) as cnt FROM staff");
                                if ($result && $row = $result->fetch_assoc()) {
                                    $totalStaff = $row['cnt'];
                                }
                                $conn->close();
                            }
                            ?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Staff</h6>
                                            <span class="h3 mb-0"><?php echo $totalStaff; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge badge-soft-primary">All Time</span>
                                        </div>
                                    </div> <!-- end row -->
                                    <div id="sparkline2" class="mt-3"></div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

                        <div class="col-lg-6 col-xl-3">
                            <?php
                            // Database connection (adjust credentials as needed)
                            $conn = new mysqli('localhost', 'root', '12345', 'order_tracking');
                            $totalUsers = 0;
                            if ($conn->connect_error) {
                                $totalUsers = 'DB Error';
                            } else {
                                $result = $conn->query("SELECT COUNT(*) as cnt FROM users");
                                if ($result && $row = $result->fetch_assoc()) {
                                    $totalUsers = $row['cnt'];
                                }
                                $conn->close();
                            }
                            ?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Users</h6>
                                            <span class="h3 mb-0"><?php echo $totalUsers; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge badge-soft-success">All Time</span>
                                        </div>
                                    </div> <!-- end row -->
                                    <div id="sparkline3" class="mt-3"></div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->
                        <div class="col-lg-6 col-xl-3">
                            <?php
                            // Database connection (adjust credentials as needed)
                            $conn = new mysqli('localhost', 'root', '12345', 'order_tracking');
                            $totalOrderPrice = 0;
                            $today = date('Y-m-d');
                            if ($conn->connect_error) {
                                $totalOrderPrice = 'DB Error';
                            } else {
                                $stmt = $conn->prepare("SELECT SUM(total) as total_price FROM orders WHERE DATE(created_at) = ?");
                                $stmt->bind_param("s", $today);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($result && $row = $result->fetch_assoc()) {
                                    $totalOrderPrice = $row['total_price'] !== null ? number_format($row['total_price'], 2) : '0.00';
                                }
                                $stmt->close();
                                $conn->close();
                            }
                            ?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Order Price</h6>
                                            <span class="h3 mb-0"><?php echo $totalOrderPrice; ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge badge-soft-warning"><?php echo date('Y-m-d'); ?></span>
                                        </div>
                                    </div> <!-- end row -->
                                    <div id="sparkline4" class="mt-3"></div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

               

            

               

                </div> <!-- container -->

            </div> <!-- content -->

            

        </div>

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