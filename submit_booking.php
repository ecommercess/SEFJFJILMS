<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $package_id = intval($_POST['package_id']);
    $event_location = htmlspecialchars($_POST['event_location']);
    $custom_location = isset($_POST['custom_location']) ? htmlspecialchars($_POST['custom_location']) : '';
    $event_date = $_POST['event_date'];

    // Use custom location if specified
    if ($event_location === 'Other') {
        $event_location = $custom_location;
    }

    // Check if the user has any pending or approved bookings
    $pending_query = "SELECT * FROM bookings WHERE user_id = ? AND (status = 'pending' OR status = 'approved')";
    $stmt_pending = $conn->prepare($pending_query);
    $stmt_pending->bind_param("i", $user_id);
    $stmt_pending->execute();
    $result_pending = $stmt_pending->get_result();

    if ($result_pending->num_rows > 0) {
        // User has a pending or approved booking
        // Redirect back to booking form with an error message
        header("Location: booking_form.php?id=$package_id&error=1");
        exit();
    }

    $stmt_pending->close();

    // Handle file upload
    $receipt_photo = $_FILES['receipt_photo'];
    $upload_dir = 'uploads/'; // Directory for uploaded files
    $upload_file = $upload_dir . basename($receipt_photo['name']);

    // Check if the upload directory exists, if not create it
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Move uploaded file to the designated directory
    if (move_uploaded_file($receipt_photo['tmp_name'], $upload_file)) {
        // Prepare SQL query to insert booking
        $stmt_insert = $conn->prepare("INSERT INTO bookings (user_id, package_id, event_location, event_date, receipt_photo, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt_insert->bind_param("iisss", $user_id, $package_id, $event_location, $event_date, $upload_file);

        if ($stmt_insert->execute()) {
            // Booking successful, redirect to profile page
            header("Location: profile.php");
            exit();
        } else {
            // Error inserting booking, redirect back to booking form with an error message
            header("Location: booking_form.php?id=$package_id&error=2");
            exit();
        }

        $stmt_insert->close();
    } else {
        // Failed to upload receipt photo, redirect back to booking form with an error message
        header("Location: booking_form.php?id=$package_id&error=3");
        exit();
    }
}
?>
