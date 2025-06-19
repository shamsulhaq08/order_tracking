
<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "12345"; // Change to empty if no password
$dbname = "order_tracking";

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    // Log the error instead of displaying it
    error_log("Connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}
?>
