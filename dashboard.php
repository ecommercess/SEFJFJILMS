<?php
session_start();

// Include database connection
include 'db.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php"); // Redirect to admin login if not logged in
    exit();
}

// Fetch admin email from session
$admin_email = $_SESSION['admin_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CDN -->
    <style>
        /* Basic styling for the dashboard */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            display: flex;
            background-color: #f0f0f0;
        }
        .sidebar {
            width: 250px;
            background-color: #2C3E50; /* Darker color for the sidebar */
            color: #ffffff;
            padding: 20px;
            height: 100vh;
            position: fixed; /* Fixed sidebar */
            transition: width 0.3s;
        }
        .sidebar h2 {
            font-size: 1.4em;
            margin-bottom: 20px;
        }
        .sidebar p {
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: #ffffff;
            text-decoration: none;
            margin: 10px 0;
            border-radius: 5px; /* Rounded corners for links */
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #34495E; /* Lighter shade on hover */
        }
        .content {
            padding: 20px;
            margin-left: 250px; /* Leave space for sidebar */
            flex-grow: 1;
            background-color: #ffffff; /* White background for content area */
            height: 100vh; /* Full height */
            overflow-y: auto; /* Scrollable content */
            transition: margin-left 0.3s; /* Smooth transition */
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .logout {
            background-color: #E74C3C; /* Red color for logout button */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center; /* Center icon vertically */
        }
        .logout:hover {
            background-color: #C0392B; /* Darker red on hover */
        }
        .logout i {
            margin-right: 5px; /* Space between icon and text */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>FJFILMS Photography</h2>
        <p>Welcome, <?php echo htmlspecialchars($admin_email); ?></p>
        <hr>
        <a href="users.php">Users</a>
        <a href="gallery.php">Gallery</a>
        <a href="appointment.php">Appointments</a>
        <a href="bookings.php">Bookings</a>
        <a href="packages.php">Packages</a>
        <a href="announcement.php">Announcement</a>
        
        <hr>
        <a href="adminlogout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i> Logout <!-- Font Awesome Logout Icon -->
        </a>
    </div>
    <div class="content">
        <div class="header">

              

        </div>
    </div>
</body>
</html>
