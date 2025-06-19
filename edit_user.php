<?php
include 'db_config.php';

$user_id = $_GET['user_id'] ?? null;
$message = '';

// Redirect if no user_id provided
if (!$user_id) {
    header("Location: register.php");
    exit;
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// Fetch staff list
$staff_list = [];
$res = $conn->query("SELECT id, first_name FROM staff");
while ($row = $res->fetch_assoc()) {
    $staff_list[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $staff_id = $_POST['staff_id'] ?? null;

    $allowed_roles = ['user', 'admin', 'super admin', 'staff'];
    if (!in_array($role, $allowed_roles)) {
        die("Invalid role selected.");
    }

    // If password is changed, hash it, else keep old
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, staff_id = ? WHERE user_id = ?");
        $stmt->bind_param("sssss", $username, $hashed_password, $role, $staff_id, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, staff_id = ? WHERE user_id = ?");
        $stmt->bind_param("ssss", $username, $role, $staff_id, $user_id);
    }

    if ($stmt->execute()) {
        $message = "User updated successfully!";
        header("Location: register.php");
        exit;
    } else {
        $message = "Update failed: " . $stmt->error;
    }
    $stmt->close();
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

                 
<div class="container mt-5">
    <h2>Edit User</h2>
    <form action="edit_user.php?user_id=<?= urlencode($user_id) ?>" method="post" class="mt-4">
        <div class="form-group mb-3">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <?php
                foreach (['user', 'admin', 'super admin', 'staff'] as $role_option):
                    $selected = ($user['role'] === $role_option) ? 'selected' : '';
                    echo "<option value=\"$role_option\" $selected>" . ucfirst($role_option) . "</option>";
                endforeach;
                ?>
            </select>
        </div>

        <div class="form-group mb-3" id="staffSelect" style="<?= $user['role'] === 'staff' ? '' : 'display:none;' ?>">
            <label for="staff_id">Assign Staff</label>
            <select name="staff_id" id="staff_id" class="form-control">
                <option value="">Select staff</option>
                <?php foreach ($staff_list as $staff): ?>
                    <option value="<?= $staff['id'] ?>" <?= $user['staff_id'] == $staff['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($staff['first_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input class="form-control" type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required style="font-size: small;">
        </div>

        <div class="form-group mb-3">
            <label for="password">Password (leave blank to keep current)</label>
            <input class="form-control" type="password" name="password" id="password">
        </div>

        <div class="form-group">
            <button class="btn btn-success" type="submit">Update User</button>
            <a href="register.php" class="btn btn-secondary">Cancel</a>
        </div>
        <?php if (!empty($message)): ?>
            <p class="text-success mt-3"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </form>
</div>

<script>
    // Show/hide staff select on role change
    document.getElementById('role').addEventListener('change', function () {
        const staffSelect = document.getElementById('staffSelect');
        staffSelect.style.display = this.value === 'staff' ? 'block' : 'none';
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