<?php
session_start();

// Include database connection
include 'db.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php");
    exit();
}

// Fetch admin email from session
$admin_email = $_SESSION['admin_email'];

// Handle accept, decline or delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];
    $decline_reason = isset($_POST['decline_reason']) ? $_POST['decline_reason'] : '';

    // Determine the new status based on the action
    if ($action === 'accept') {
        $new_status = 'approved';
        $decline_reason = null; // No reason needed for approval
    } elseif ($action === 'decline') {
        $new_status = 'declined';
    } elseif ($action === 'delete') {
        // Handle the delete action
        $deleteQuery = "DELETE FROM bookings WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $booking_id);
        if (!$stmt->execute()) {
            die("Error deleting booking: " . $stmt->error);
        }
        $stmt->close();
        header("Location: bookings.php"); // Redirect after delete
        exit();
    } else {
        $new_status = null; // Invalid action
    }

    // Update the status and decline reason in the database if applicable
    if ($new_status) {
        $updateQuery = "UPDATE bookings SET status = ?, decline_reason = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ssi', $new_status, $decline_reason, $booking_id);
        if (!$stmt->execute()) {
            die("Error updating booking status: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Fetch bookings data including receipt_photo and status
$bookingsQuery = "
    SELECT b.id AS booking_id, u.fullname, p.package_name, 
           IF(b.custom_location IS NOT NULL AND b.custom_location <> '', 
              CONCAT(b.event_location, ' (', b.custom_location, ')'), 
              b.event_location) AS location,
           b.event_date, b.receipt_photo, b.status, b.decline_reason
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN packages p ON b.package_id = p.id
";

$bookingsResult = $conn->query($bookingsQuery);

// Check if the query was successful
if (!$bookingsResult) {
    die("Query failed: " . $conn->error);
}

$bookingsData = [];
while ($row = $bookingsResult->fetch_assoc()) {
    $bookingsData[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bookings</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .receipt-photo {
            width: 100px;
            height: auto;
        }
        button {
            background: none;
            border: none;
            cursor: pointer;
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
        <h2>Bookings</h2>
        <table id="bookingsTable" class="display">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User Name</th>
                    <th>Package Name</th>
                    <th>Location</th>
                    <th>Event Date</th>
                    <th>Receipt Photo</th>
                    <th>Status</th>
                    <th>Reason for Decline</th> <!-- New Column Header for Reason Dropdown -->
                    <th>Decline Reason</th> <!-- New Column for Decline Reason Text Display -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookingsData as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                        <td><?php echo htmlspecialchars($booking['event_date']); ?></td>
                        <td>
                            <?php if (!empty($booking['receipt_photo'])): ?>
                                <img src="<?php echo htmlspecialchars($booking['receipt_photo']); ?>" alt="Receipt Photo" class="receipt-photo">
                            <?php else: ?>
                                No receipt uploaded
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($booking['status']); ?></td>

                        <!-- Reason for Decline Dropdown Column -->
                        <td>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
                                    <select name="decline_reason" style="display:inline;">
                                        <option value="">Select Reason</option>
                                        <option value="Incomplete Information">Incomplete Information</option>
                                        <option value="Payment Issue">Payment Issue</option>
                                        <option value="Double Booking">Double Booking</option>
                                        <option value="Other">Other</option>
                                    </select>
                            <?php endif; ?>
                        </td>

                        <!-- Decline Reason Text Column -->
                        <td>
                            <?php echo $booking['decline_reason'] ? htmlspecialchars($booking['decline_reason']) : 'N/A'; ?>
                        </td>

                        <!-- Action Buttons Column -->
                        <td>
                        <?php if ($booking['status'] === 'pending'): ?>
        <div style="display: flex; gap: 10px;"> <!-- Flex container for horizontal alignment -->
            <button type="submit" name="action" value="accept" title="Accept">
                <i class="fas fa-check-circle" style="color:green; font-size: 1.5em;"></i>
            </button>
            <button type="submit" name="action" value="decline" title="Decline">
                <i class="fas fa-times-circle" style="color:red; font-size: 1.5em;"></i>
            </button>
        </div>
        </form>
    <?php else: ?>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['booking_id']); ?>">
            <button type="submit" name="action" value="delete" title="Delete" onclick="return confirm('Are you sure you want to delete this booking?')">
                <i class="fas fa-trash-alt" style="color:#666; font-size: 1.5em;"></i>
            </button>
        </form>
    <?php endif; ?>
</td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#bookingsTable').DataTable(); // Initialize DataTable
        });
    </script>
</body>
</html>
