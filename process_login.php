<?php
session_start();
include 'db.php'; // Include database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the account status is pending
        if ($user['status'] == 'pending') {
            // Account is pending, do not allow login
            $_SESSION['login_error'] = "Your account is pending approval.";
            header("Location: login.php");
            exit();
        } elseif ($user['status'] == 'approved') {
            // Verify the password if the account is approved
            if (password_verify($password, $user['password'])) {
                // Store user information in session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];  // Email of user
                $_SESSION['profile_picture'] = $user['profile_picture']; // Profile picture
                $_SESSION['login_success'] = true; // Set success message

                // Redirect to index.php
                header("Location: index.php");
                exit();
            } else {
                // Incorrect password
                $_SESSION['login_error'] = "Incorrect password.";
                header("Location: login.php");
                exit();
            }
        }
    } else {
        // No account found with that email
        $_SESSION['login_error'] = "No account found with this email.";
        header("Location: login.php");
        exit();
    }

    $stmt->close(); // Close the prepared statement
}

$conn->close(); // Close the database connection
