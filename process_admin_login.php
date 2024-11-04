<?php
session_start();
include 'db.php'; // Include your database connection

// Get the admin email and password from the form
$admin_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
$admin_password = mysqli_real_escape_string($conn, $_POST['admin_password']);

// Example query to check admin credentials
// Assuming there's a table named `admins` with columns `email` and `password`
$sql = "SELECT * FROM admins WHERE email = '$admin_email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();

    // Verify the password
    if (password_verify($admin_password, $admin['password'])) {
        // Store admin information in session variables
        $_SESSION['admin_id'] = $admin['id']; // Assuming there's an `id` column
        $_SESSION['admin_email'] = $admin['email']; // Store admin email

        // Redirect to admin dashboard (you can change this URL as needed)
        header("Location: dashboard.php");
        exit();
    } else {
        // Incorrect password
        $_SESSION['login_error'] = "Incorrect password.";
        header("Location: adminlogin.php");
        exit();
    }
} else {
    // No account found with that email
    $_SESSION['login_error'] = "No account found with this email.";
    header("Location: adminlogin.php");
    exit();
}

$conn->close();
?>
