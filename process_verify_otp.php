<?php
session_start(); // Start the session

// Database connection
include 'db.php'; // Include the database connection

// Check if the user is logged in and email is set
if (!isset($_SESSION['email'])) {
    echo "Please log in to verify your OTP.";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the concatenated input OTP from the form
    $inputOtp = mysqli_real_escape_string($conn, $_POST['otp']); // This is the concatenated OTP string
    $email = $_SESSION['email']; // Retrieve email from the session

    // Verify the OTP
    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ? AND status = 'pending'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedOtp);
        $stmt->fetch();

        // Compare OTPs securely
        if (hash_equals($inputOtp, $storedOtp)) {
            // OTP is valid, update user status to approved
            $updateStmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE email = ?");
            $updateStmt->bind_param("s", $email);
            if ($updateStmt->execute()) {
                // Set success message
                $_SESSION['success_message'] = "OTP verified successfully! Your account is now active.";
                // Redirect to login page
                header("Location: login.php");
                exit(); // Stop further execution
            } else {
                echo "Error updating status: " . $updateStmt->error;
            }
            $updateStmt->close();
        } else {
            // Invalid OTP
            echo "Invalid OTP. Please try again.";
        }
    } else {
        echo "No user found with this email or OTP already verified!";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
