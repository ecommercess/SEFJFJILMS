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

$query = "
    SELECT a.id AS appointment_id, u.fullname, a.appointment_date, a.appointment_time, a.status, a.queue_number, a.created_at
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        /* Basic styling for the dashboard */
        body {
            font-family: Arial, sans-serif;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #f4f4f4;
            padding: 15px;
            height: 100vh;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
            margin: 5px 0;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .content {
            padding: 20px;
            flex-grow: 1;
        }
        .status-btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <p>Welcome! <?php echo htmlspecialchars($admin_email); ?></p>
        <hr>
        <a href="users.php">Users</a>
        <a href="gallery.php">Gallery</a>
        <a href="appointment.php">Appointments</a>
        <a href="bookings.php">Bookings</a>
        <a href="packages.php">Packages</a>
        <a href="announcement.php">Announcement</a>
        
        <hr>
        <a href="adminlogout.php">Logout</a>
    </div>
    <div class="content">
        <h2>Appointments</h2>

        <table id="appointmentsTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                    <th>Queue Number</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                        <td><?php echo htmlspecialchars($row['queue_number'] ?: 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <?php if ($row['status'] === 'pending'): ?>
                                <button class="status-btn status-approved" onclick="updateStatus(<?php echo $row['appointment_id']; ?>, 'approved')">Approve</button>
                                <button class="status-btn status-declined" onclick="deleteAppointment(<?php echo $row['appointment_id']; ?>)">Decline</button>
                            <?php elseif ($row['status'] === 'approved'): ?>
                                <button class="status-btn" onclick="deleteAppointment(<?php echo $row['appointment_id']; ?>)">Delete</button>
                            <?php else: ?>
                                <button class="status-btn" disabled><?php echo ucfirst($row['status']); ?></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#appointmentsTable').DataTable({
                    "language": {
                        "emptyTable": "No data available in table" // Customize message for no data
                    }
                }); // Initialize DataTable
            });

            function updateStatus(appointmentId, newStatus) {
                if (confirm("Are you sure you want to change the status to " + newStatus + "?")) {
                    $.ajax({
                        url: 'update_appointment_status.php',
                        method: 'POST',
                        data: { appointment_id: appointmentId, status: newStatus },
                        success: function(response) {
                            alert(response);
                            location.reload(); // Reload the page to reflect changes
                        }
                    });
                }
            }

            function deleteAppointment(appointmentId) {
                if (confirm("Are you sure you want to delete this appointment?")) {
                    $.ajax({
                        url: 'delete_appointment.php', // Create this file to handle the delete action
                        method: 'POST',
                        data: { appointment_id: appointmentId },
                        success: function(response) {
                            alert(response);
                            location.reload(); // Reload the page to reflect changes
                        }
                    });
                }
            }
        </script>
    </div>
</body>
</html>
