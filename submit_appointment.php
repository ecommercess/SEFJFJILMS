<?php
session_start();
include 'db.php'; // Database connection


if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to make an appointment.";
    header("Location: appointment_error.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Get today's date
    $today = date('Y-m-d');

    // Check if the selected date is in the past
    if ($appointment_date < $today) {
        $_SESSION['error_message'] = "You cannot choose a date in the past. Please select a valid date.";
        header("Location: appointment_error.php");
        exit();
    }

    // Check if user already has an appointment (either pending or approved)
    $appointmentCheckQuery = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? AND (status = 'approved' OR status = 'pending')");
    $appointmentCheckQuery->bind_param("i", $user_id);
    $appointmentCheckQuery->execute();
    $appointmentResult = $appointmentCheckQuery->get_result();

    if ($appointmentResult->num_rows > 0) {
        $_SESSION['error_message'] = "You already have an appointment (pending or approved) and cannot make another one.";
        header("Location: appointment_error.php");
        exit();
    } else {
        // Count all approved appointments for the chosen date
        $totalCountQuery = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE appointment_date = ? AND status = 'approved'");
        $totalCountQuery->bind_param("s", $appointment_date);
        $totalCountQuery->execute();
        $totalCountResult = $totalCountQuery->get_result()->fetch_assoc();
        $totalApprovedCount = $totalCountResult['count'];

        // Count approved appointments for the chosen time slot
        $timeSlotCountQuery = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND status = 'approved'");
        $timeSlotCountQuery->bind_param("ss", $appointment_date, $appointment_time);
        $timeSlotCountQuery->execute();
        $timeSlotResult = $timeSlotCountQuery->get_result()->fetch_assoc();
        $timeSlotApprovedCount = $timeSlotResult['count'];

        // Limit: 10 total approved appointments per day and 5 per time slot
        if ($totalApprovedCount >= 10) {
            $_SESSION['error_message'] = "No more appointments available for the selected date. Please choose another day.";
            header("Location: appointment_error.php");
            exit();
        } elseif ($timeSlotApprovedCount >= 5) {
            $_SESSION['error_message'] = "No more appointments available for the selected time slot. Please choose a different time.";
            header("Location: appointment_error.php");
            exit();
        } else {
            // Set the queue number based on the current count of appointments for the day
            $pendingCountQuery = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE appointment_date = ?");
            $pendingCountQuery->bind_param("s", $appointment_date);
            $pendingCountQuery->execute();
            $pendingResult = $pendingCountQuery->get_result()->fetch_assoc();
            $queue_number = $pendingResult['count'] + 1;

            $stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, appointment_time, queue_number, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("issi", $user_id, $appointment_date, $appointment_time, $queue_number);

            if ($stmt->execute()) {
                // Set success message in session
                $_SESSION['success_message'] = "Appointment submitted successfully! Your queue number is $queue_number. Please wait for admin approval.";
                // Redirect to a success page
                header("Location: appointment_success.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Error: " . $stmt->error;
                header("Location: appointment_error.php");
                exit();
            }

            $stmt->close();
        }

        $totalCountQuery->close();
        $timeSlotCountQuery->close();
        $pendingCountQuery->close();
    }

    $appointmentCheckQuery->close(); // Closing the appointment check query
    $conn->close();
}
?>
