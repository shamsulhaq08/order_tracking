<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db_config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
          
            $_SESSION['id'] = $user['id'];        
               $_SESSION['user_id'] = $user['user_id'];            // numeric
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit;
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }
}
?>




<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Drezoc - Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="MyraStudio" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <script src="assets/js/config.js"></script>
</head>

<body>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center min-vh-100">
                        <div class="w-100 d-block card shadow-lg rounded my-5 overflow-hidden">
                            <div class="row">
                                <div class="col-lg-5 d-none d-lg-block bg-login rounded-left"></div>
                                <div class="col-lg-7">
                                    <div class="p-5">
                                        <div class="text-center w-75 mx-auto auth-logo mb-4">
                                           
                                                <span class="logo-dark"><h1>Order Management System</h1></span>
                                         

                                               <span class="logo-light"><h1>Order Management System</h1></span>

                                        </div>


                                        <h1 class="h5 mb-1">Welcome Back!</h1>

                                        
                                        <form action="login.php" method="post" class="auth-form">

<div class="form-group mb-3">
    <label class="form-label" for="username">Username</label>
    <input class="form-control" type="text" id="username" name="username" required placeholder="Enter your username">
</div>

<div class="form-group mb-3">
    <label class="form-label" for="password">Password</label>
    <input class="form-control" type="password" id="password" name="password" required placeholder="Enter your password">
</div>

<div class="form-group mb-3">
    <div class="">
        <input class="form-check-input" type="checkbox" id="checkbox-signin" name="remember" checked>
        <label class="form-check-label ms-2" for="checkbox-signin">Remember me</label>
    </div>
</div>

<div class="form-group mb-0 text-center">
    <button class="btn btn-primary w-100" type="submit">Log In</button>
</div>

<!-- Optional error message display -->
<?php if (!empty($_GET['error'])): ?>
    <p class="text-danger text-center mt-2"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>

<div class="row mt-4">
                                        <div class="col-12 text-center">
                                            <p class="text-muted mb-0">Don't have an account? <a href="register.php"><b>Signup</b></a></p>
                                        </div>
                                    </div>

</form>



                                        <!-- <div class="text-center mt-4">
                                            <h5 class="text-muted font-size-16">Sign in using</h5>

                                            <ul class="list-inline mt-3 mb-0">
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);"
                                                        class="social-list-item border border-primary text-primary"><i
                                                            class="mdi mdi-facebook"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);"
                                                        class="social-list-item border border-danger text-danger"><i
                                                            class="mdi mdi-google"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);"
                                                        class="social-list-item border border-info text-info"><i
                                                            class="mdi mdi-twitter"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <a href="javascript: void(0);"
                                                        class="social-list-item border border-secondary text-secondary"><i
                                                            class="mdi mdi-github"></i></a>
                                                </li>
                                            </ul>
                                        </div> -->
<!-- 
                                        <div class="row mt-4">
                                            <div class="col-12 text-center">
                                                <p class="text-muted mb-2">
                                                    <a class="text-muted font-weight-medium ms-1"
                                                        href='pages-recoverpw.html'>Forgot your password?</a>
                                                </p>
                                                <p class="text-muted mb-0">Don't have an account?
                                                    <a class="text-muted font-weight-medium ms-1"
                                                        href='pages-register.html'><b>Sign Up</b></a>
                                                </p>
                                            </div> 
                                        </div> -->

                                        <!-- end row -->
                                    </div> <!-- end .padding-5 -->
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div> <!-- end .w-100 -->
                    </div> <!-- end .d-flex -->
                </div> <!-- end col-->
            </div> <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <!-- App js -->
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.js"></script>

</body>

</html>