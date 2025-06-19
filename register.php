<?php
include 'db_config.php';


$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $staff_id = $_POST['staff_id'] ?? null;

    $allowed_roles = ['user', 'admin', 'super admin', 'staff'];

    if (!in_array($role, $allowed_roles)) {
        $message = "Invalid role selected.";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['cnt'] > 0) {
            $message = "Username already exists. Please choose another.";
        } else {
            // Generate unique user_id
            $prefix = strtolower(str_replace(' ', '_', $role));
            $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM users WHERE role = ?");
            $stmt->bind_param("s", $role);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$result) {
                $message = "Database query failed: " . $conn->error;
            } else {
                $row = $result->fetch_assoc();
                $stmt->close();
                $user_id = $prefix . '_' . str_pad($row['cnt'] + 1, 3, '0', STR_PAD_LEFT);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (user_id, username, password, role, staff_id) VALUES (?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    $message = "Prepare failed: " . htmlspecialchars($conn->error);
                } else {
                    $stmt->bind_param("sssss", $user_id, $username, $hashed_password, $role, $staff_id);

                    if ($stmt->execute()) {
                        $message = "User registered successfully!";
                    } else {
                        $message = "Error: " . htmlspecialchars($stmt->error);
                    }
                    $stmt->close();
                }
            }
        }
    }
}
if (!empty($message)) {
    echo "<script>alert('" . addslashes($message) . "');</script>";
}


// Fetch staff for dropdown
$staff_list = [];
$res = $conn->query("SELECT id, first_name FROM staff");

if ($res === false) {
    die("Database error fetching staff: " . $conn->error);
}

while ($row = $res->fetch_assoc()) {
    $staff_list[] = $row;
}


?>


  
<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

<head>
    <meta charset="utf-8" />
    <title>Staff </title>
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

                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-12">
                            <!-- Registration Form -->
                            <div >
                                <div class="p-5">
                                    <h1 class="h5 mb-1">Create an Account!</h1>
                                    <p class="text-muted mb-4">Fill the form below to register a new user.</p>
                                    <form action="register.php" method="post" class="auth-form">
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="role">Select Role</label>
                                            <select name="role" id="role" class="form-control" required>
                                                <option value="user">User</option>
                                                <option value="admin">Admin</option>
                                                <option value="super admin">Super Admin</option>
                                                <option value="staff">Staff</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3" id="staffSelect" style="display:none;">
                                            <label class="form-label" for="staff_id">Assign Staff</label>
                                            <select name="staff_id" id="staff_id" class="form-control">
                                                <option value="">Select staff</option>
                                                <?php foreach ($staff_list as $staff): ?>
                                                    <option value="<?= htmlspecialchars($staff['id']) ?>" data-name="<?= htmlspecialchars($staff['first_name']) ?>">
                                                        <?= htmlspecialchars($staff['first_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="username">Name</label>
                                            <input class="form-control" type="text" id="username" name="username" required placeholder="Enter your name" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase();">
                                        </div>
                                        <script>
                                            // Show/hide staff select if role is staff
                                            document.getElementById('role').addEventListener('change', function () {
                                                const staffDiv = document.getElementById('staffSelect');
                                                staffDiv.style.display = this.value === 'staff' ? 'block' : 'none';
                                                if (this.value !== 'staff') {
                                                    document.getElementById('username').value = '';
                                                    document.getElementById('staff_id').selectedIndex = 0;
                                                }
                                            });

                                            // Auto-fill username when staff is selected
                                            document.getElementById('staff_id').addEventListener('change', function () {
                                                const selected = this.options[this.selectedIndex];
                                                const name = selected.getAttribute('data-name') || '';
                                                document.getElementById('username').value = name;
                                            });
                                        </script>
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <input class="form-control" type="password" id="password" name="password" required placeholder="Enter your password">
                                        </div>
                                        <div class="form-group mb-0 text-center">
                                            <button class="btn btn-primary w-100" type="submit">Sign Up</button>
                                        </div>
                                        <?php if (!empty($message)): ?>
                                            <p class="text-center text-success mt-3"><?= htmlspecialchars($message) ?></p>
                                        <?php endif; ?>
                                    </form>
                                </div> <!-- end .p-5 -->
                            </div> <!-- end card -->
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-10 col-12">
                            <!-- User Table -->
                            <div class="card shadow-lg rounded my-5 overflow-hidden">
                                <div class="p-5">
                                    <h1 class="h5 mb-3">Registered Users</h1>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>User ID</th>
                                                    <th>Name</th>
                                                    <th>Role</th>
                                                    <th>Staff</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch users and staff names
                                                $user_sql = "SELECT users.user_id, users.username, users.role, users.staff_id, staff.first_name AS staff_name
                                                         FROM users
                                                         LEFT JOIN staff ON users.staff_id = staff.id
                                                         ORDER BY users.user_id DESC";
                                                $user_res = $conn->query($user_sql);
                                                if ($user_res && $user_res->num_rows > 0):
                                                    while ($user = $user_res->fetch_assoc()):
                                                        // Remove "Assign Rights" button for all users
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                                        <td><?= htmlspecialchars(ucwords($user['role'])) ?></td>
                                                        <td><?= $user['role'] === 'staff' ? htmlspecialchars($user['staff_name']) : '-' ?></td>
                                                        <td>
                                                            <a href="edit_user.php?user_id=<?= urlencode($user['user_id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                                            <a href="delete_user.php?user_id=<?= urlencode($user['user_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    endwhile;
                                                else:
                                                ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No users found.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> <!-- end .p-5 -->
                            </div> <!-- end card -->
                        </div>
                    </div>
                </div>

    <!-- App js -->
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        // Show/hide staff select if role is staff
        document.getElementById('role').addEventListener('change', function () {
            const staffDiv = document.getElementById('staffSelect');
            staffDiv.style.display = this.value === 'staff' ? 'block' : 'none';
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