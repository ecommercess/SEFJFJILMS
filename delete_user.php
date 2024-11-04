<?php
session_start();

// Include database connection
include 'db.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php"); // Redirect to admin login if not logged in
    exit();
}

// Check if the user ID is provided in POST request
if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']); // Sanitize user ID input

    // Prepare the SQL statement to delete the user
    $sql = "DELETE FROM users WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind the user ID as a parameter to the SQL query
        $stmt->bind_param("i", $user_id);

        // Execute the statement
        if ($stmt->execute()) {
            // User deleted successfully
            $_SESSION['message'] = "User successfully deleted.";
        } else {
            // If deletion failed
            $_SESSION['error'] = "Error deleting user.";
        }

        // Close the statement
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing the SQL statement.";
    }
} else {
    // If no user ID was provided
    $_SESSION['error'] = "No user ID provided.";
}

// Redirect back to the users page after deletion
header("Location: users.php");
exit();

// Close the database connection
$conn->close();
?>
