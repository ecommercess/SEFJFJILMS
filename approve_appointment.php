<?php
include 'db.php'; // Database connection

if (isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // Calculate the next queue number
    $result = $conn->query("SELECT MAX(queue_number) AS max_queue FROM appointments WHERE status = 'approved'");
    $row = $result->fetch_assoc();
    $next_queue = $row['max_queue'] + 1;

    $stmt = $conn->prepare("UPDATE appointments SET status = 'approved', queue_number = ? WHERE id = ?");
    $stmt->bind_param("ii", $next_queue, $appointment_id);

    if ($stmt->execute()) {
        echo "Appointment approved!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
