<?php
session_start();

// Include database connection
include 'db.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php");
    exit();
}

// Check if the appointment_id is provided
if (isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    
    // Prepare and execute the deletion query
    $query = "DELETE FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $appointment_id);
    
    if ($stmt->execute()) {
        echo "Appointment deleted successfully.";
    } else {
        echo "Error deleting appointment: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
