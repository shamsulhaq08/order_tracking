<?php
include 'db_config.php';

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM users");
?>  
<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

<head>
    <meta charset="utf-8" />
   <title>Manage User Rights</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Drezoc - Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="MyraStudio" name="author" />
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
                    <h4 style="color: white;">Tracking</h4>
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
              
  <br>
<!-- User Dropdown -->
<div class="mb-3">
    <label for="userSelect" class="form-label fw-bold">Select User:</label>
    <select id="userSelect" class="form-select" style="max-width: 350px;">
        <option value="">-- Select User --</option>
        <?php while($user = mysqli_fetch_assoc($users)) { ?>
            <option value="<?= $user['user_id'] ?>">
                <?= htmlspecialchars($user['username']) ?>
            </option>
        <?php } ?>
    </select>
</div>

<!-- Permissions Table will load here -->
<div id="permissionsTable" style="margin-top: 20px;"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Restore selected user from localStorage
    var savedUserId = localStorage.getItem('selectedUserId');
    if(savedUserId) {
        $('#userSelect').val(savedUserId);
        if(savedUserId != "") {
            $.post('load_permissions.php', { user_id: savedUserId }, function(response){
                $('#permissionsTable').html(response);
            });
        }
    }

    $('#userSelect').on('change', function(){
        var userId = $(this).val();
        localStorage.setItem('selectedUserId', userId);
        if(userId != "") {
            $.post('load_permissions.php', { user_id: userId }, function(response){
                $('#permissionsTable').html(response);
            });
        } else {
            $('#permissionsTable').html('');
        }
    });
});
</script>

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