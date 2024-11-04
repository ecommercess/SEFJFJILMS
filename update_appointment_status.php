<?php
session_start();
include 'db.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    echo "Access denied. Please log in as an admin.";
    exit();
}

// Check if the appointment ID and status are set in the POST request
if (isset($_POST['appointment_id'], $_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];

    // Check if the new status is valid
    if (!in_array($new_status, ['approved', 'declined', 'pending'])) {
        echo "Invalid status.";
        exit();
    }

    // If the new status is "approved," set the queue number as the next available
    if ($new_status === 'approved') {
        // Get the current max queue number for the date of this appointment
        $queueQuery = $conn->prepare("SELECT appointment_date, MAX(queue_number) AS max_queue FROM appointments WHERE status = 'approved'");
        $queueQuery->execute();
        $result = $queueQuery->get_result()->fetch_assoc();
        $max_queue = $result['max_queue'];
        $appointment_date = $result['appointment_date'];
        
        $queue_number = $max_queue ? $max_queue + 1 : 1; // Set queue_number to 1 if no approved appointments yet

        // Update appointment status and queue number
        $stmt = $conn->prepare("UPDATE appointments SET status = ?, queue_number = ? WHERE id = ?");
        $stmt->bind_param("sii", $new_status, $queue_number, $appointment_id);
    } else {
        // For declined or pending, reset the queue number and update the status
        $stmt = $conn->prepare("UPDATE appointments SET status = ?, queue_number = NULL WHERE id = ?");
        $stmt->bind_param("si", $new_status, $appointment_id);
    }

    // Execute the update query
    if ($stmt->execute()) {
        echo "Appointment status updated successfully.";
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    // Close statements
    $stmt->close();
    $queueQuery->close();
} else {
    echo "Appointment ID and status are required.";
}

// Close database connection
$conn->close();
?>
