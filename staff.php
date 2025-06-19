

  
<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

<head>
    <meta charset="utf-8" />
    <title>Staff </title>
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
                    <h4 style="color: white;">Order Tracking</h4>
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

                    <?php
// DB connection (update with your credentials)
$host = 'localhost';
$dbname = 'order_tracking';
$username = 'root';
$password = '12345';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!-- Progress Bar -->
<div id="progress-container" style="display:none; margin-bottom:15px;">
    <div class="progress">
        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            0%
        </div>
    </div>
</div>
<br>
<form method="post" id="fetchStaffForm">
    <button type="submit" name="fetch_staff" id="fetchStaffBtn"  class="btn btn-info waves-effect waves-light"> <i class="mdi mdi-cloud-outline me-1"></i>Fetch Staff Data</button>
</form>

<script>
document.getElementById('fetchStaffForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('fetchStaffBtn');
    var progressContainer = document.getElementById('progress-container');
    var progressBar = document.getElementById('progress-bar');
    btn.disabled = true;
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressBar.setAttribute('aria-valuenow', 0);
    progressBar.textContent = '0%';

    // AJAX request to fetch staff
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.upload.onprogress = function(e) {
        // Not used, as upload is tiny
    };

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            progressBar.style.width = '100%';
            progressBar.setAttribute('aria-valuenow', 100);
            progressBar.textContent = '100%';
            btn.disabled = false;
            setTimeout(function() {
                progressContainer.style.display = 'none';
                location.reload();
            }, 800);
        }
    };

    // Simulate progress
    var percent = 0;
    var interval = setInterval(function() {
        if (percent < 90) {
            percent += 10;
            progressBar.style.width = percent + '%';
            progressBar.setAttribute('aria-valuenow', percent);
            progressBar.textContent = percent + '%';
        } else {
            clearInterval(interval);
        }
    }, 120);

    xhr.send('fetch_staff=1');
});
</script>

<?php
if (isset($_POST['fetch_staff'])) {
    // API URL
    $apiUrl = "http://localhost/staff_system/api/employee.php";

    // Fetch data from API
    $response = file_get_contents($apiUrl);

    if ($response === FALSE) {
        die("Error fetching data from API.");
    }

    $staffData = json_decode($response, true);

    if (!is_array($staffData)) {
        die("Invalid data format received from API.");
    }

    // Insert or update into database
    foreach ($staffData as $staff) {
        $id = $staff['id'];
        $firstName = $staff['first_name'];
        $email = $staff['email'];
        $lastName = $staff['last_name'];

        // Check if staff exists
        $stmt = $pdo->prepare("SELECT first_name, email, last_name FROM staff WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            // Insert new staff
            $insert = $pdo->prepare("INSERT INTO staff (id, first_name, email, last_name) VALUES (?, ?, ?, ?)");
            $insert->execute([$id, $firstName, $email, $lastName]);
        } else {
            // Check if any field has changed
            if (
                $existing['first_name'] !== $firstName ||
                $existing['email'] !== $email ||
                $existing['last_name'] !== $lastName
            ) {
                // Update changed fields
                $update = $pdo->prepare("UPDATE staff SET first_name = ?, email = ?, last_name = ? WHERE id = ?");
                $update->execute([$firstName, $email, $lastName, $id]);
            }
        }
    }
    // For AJAX: stop further output
    exit;
}
?>


<?php
// Fetch staff from database
$stmt = $pdo->query("SELECT id, first_name, email, last_name FROM staff ORDER BY id ASC");
$staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($staffList && count($staffList) > 0): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Staff List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                    
                            <th>Last Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staffList as $staff): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($staff['id']); ?></td>
                                <td><?php echo htmlspecialchars($staff['first_name']); ?></td>
                      
                                <td><?php echo htmlspecialchars($staff['last_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info mt-4">No staff data found.</div>
<?php endif; ?>

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