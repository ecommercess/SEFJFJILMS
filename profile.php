<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Fetch user data from session
$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'No email available'; // Fallback if email is not set
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_profile.png'; // Fallback if no profile picture

// Fetch user's bookings from the database
$bookingsQuery = "
    SELECT b.id AS booking_id, p.package_name, p.package_price,
           pc.content,
           IF(b.custom_location IS NOT NULL AND b.custom_location <> '', 
              CONCAT(b.event_location, ' (', b.custom_location, ')'), 
              b.event_location) AS location,
           b.event_date, b.receipt_photo, b.status, b.decline_reason
    FROM bookings b
    JOIN packages p ON b.package_id = p.id
    LEFT JOIN package_contents pc ON p.id = pc.package_id
    WHERE b.user_id = ?
";

// Check if the query was prepared successfully
if ($stmt = $conn->prepare($bookingsQuery)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $bookingsResult = $stmt->get_result();

    $bookingsData = [];
    while ($row = $bookingsResult->fetch_assoc()) {
        $bookingId = $row['booking_id'];
        if (!isset($bookingsData[$bookingId])) {
            $bookingsData[$bookingId] = $row;
            $bookingsData[$bookingId]['contents'] = [];
        }
        // Add the content to the booking's content array
        if (!empty($row['content'])) {
            $bookingsData[$bookingId]['contents'][] = $row['content'];
        }
    }
    $stmt->close();
} else {
    // Output the SQL error for debugging
    echo "Error preparing statement: " . $conn->error;
    exit();
}

$appointmentsQuery = "
    SELECT a.queue_number, a.appointment_date, a.appointment_time, a.status
    FROM appointments a
    WHERE a.user_id = ?
";

if ($stmt = $conn->prepare($appointmentsQuery)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $appointmentsResult = $stmt->get_result();

    $appointmentsData = [];
    while ($row = $appointmentsResult->fetch_assoc()) {
        $appointmentsData[] = $row;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

$success_message = '';
if (isset($_SESSION['booking_success'])) {
    $success_message = $_SESSION['booking_success'];
    unset($_SESSION['booking_success']); // Clear the message after displaying it
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <title>My Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }



        h2 {
            margin-bottom: 20px;
            color: black;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-info img {
            border-radius: 50%;
            margin-right: 20px;
            border: 3px solid #078080;
        }

        .profile-info div {
            color: black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #078080;
            color: white;
        }

        .receipt-photo {
            width: 80px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .receipt-modal {
            display: none; /* Initially hidden */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px; /* Set specific width */
            z-index: 1000; /* Ensure modal appears above other content */
            border-radius: 8px;
        }

        .close-modal {
            float: right;
            cursor: pointer;
            color: #6D4C6C;
            font-size: 20px;
        }

        .receipt-modal h2 {
            color: #6D4C6C;
            text-align: center;
        }

        .receipt-modal p {
            margin: 5px 0;
            text-align: center;
        }

        .receipt-modal ul {
            list-style-type: none; /* Remove bullet points */
            padding: 0; /* Remove padding */
            text-align: center; /* Center the list items */
        }

        .receipt-modal li {
            margin: 5px 0; /* Add some margin between items */
        }

        button {
            background-color: #f45d48;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #8D6B8E;
        }

        @media print {
            body * {
                visibility: hidden; /* Hide everything on the page */
            }

            .receipt-modal, .receipt-modal * {
                visibility: visible; /* Show only modal content */
            }

            .receipt-modal {
                position: absolute; /* Position modal for print */
                top: 0; /* Align to top */
                left: 0; /* Align to left */
                transform: none; /* Remove transform for print */
                width: 300px; /* Keep width for printing */
                height: auto; /* Adjust height as necessary */
                margin: 0; /* Remove margin for print */
                box-shadow: none; /* Remove shadow for print */
            }

            .close-modal {
                display: none; /* Hide close button during print */
            }
            .receipt-modal button { 
                display: none; /* Hide all buttons in the modal during print */
            }
        }
        .back-button {
            background-color: #2F4F4F;
            color: white;
            border: none;
            padding: 5px 8px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px; /* Space below the button */
        }

        .back-button:hover {
            background-color: #8D6B8E;
        }
    </style>
    <script>
        function openModal(bookingId) {
            const modal = document.getElementById('receiptModal-' + bookingId);
            modal.style.display = 'block';
        }

        function closeModal(bookingId) {
            const modal = document.getElementById('receiptModal-' + bookingId);
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            // Close modal if clicked outside of it
            const modals = document.querySelectorAll('.receipt-modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        };
        
        function printReceipt(bookingId) {
            const modal = document.getElementById('receiptModal-' + bookingId);
            modal.style.display = 'block'; // Ensure modal is displayed for printing
            window.print(); // Trigger the print dialog
            modal.style.display = 'none'; // Hide modal after printing
        }
    </script>
</head>
<body>

  
    <div class="container">
    <a href="index.php" class="back-button" title="Back">
    <i class="fas fa-arrow-left" style="font-size: 24px;"></i>
</a>

        <div class="profile-info">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" width="150" height="150">
            <div>
                <h2><?php echo htmlspecialchars($fullname); ?></h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            </div>
        </div>

        <?php if ($success_message): ?>
            <p class="success-message" style="color: #4CAF50; text-align: center;"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <h2>My Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Package Name</th>
                    <th>Location</th>
                    <th>Event Date</th>
                    <th>Receipt Photo</th>
                    <th>Status</th>
                    <th>Decline Reason</th>
                    <th>Final Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bookingsData) > 0): ?>
                    <?php foreach ($bookingsData as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
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
                            <td>
                                <?php echo $booking['decline_reason'] ? htmlspecialchars($booking['decline_reason']) : 'N/A'; ?>
                            </td>
                            <td>
                                <button onclick="openModal(<?php echo $booking['booking_id']; ?>)">View Receipt</button>

                                <!-- Modal for receipt -->
                                <div id="receiptModal-<?php echo $booking['booking_id']; ?>" class="receipt-modal">
                                    <span class="close-modal" onclick="closeModal(<?php echo $booking['booking_id']; ?>)">×</span>
                                    
                                    <h3 style="text-align: center;">FJFILMS PHOTOGRAPHY</h3>
                                    <p style="text-align: center;">
                                        Address: 123 Photography Lane, City, Country<br>
                                        Date: <?php echo htmlspecialchars($booking['event_date']); ?><br>
                                        Time: 6:00 PM<br>
                                        Phone: (123) 456-7890
                                    </p>
                                    <h2><?php echo htmlspecialchars($booking['package_name']); ?></h2>
                                    <ul>
                                        <?php foreach ($booking['contents'] as $content): ?>
                                            <li><?php echo htmlspecialchars($content); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    
                                    <p style="text-align: center;"><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></p>
                                    <p style="text-align: center;"><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                                    <p style="text-align: center;"><strong>Total Price:</strong> ₱<?php echo htmlspecialchars($booking['package_price']); ?></p>
                                    <div style="text-align: center;">
                                        <button onclick="printReceipt(<?php echo $booking['booking_id']; ?>)">
                                            <i class="fas fa-print" style="font-size: 24px;"></i> Print Receipt
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>My Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>Queue Number</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($appointmentsData) > 0): ?>
                    <?php foreach ($appointmentsData as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['queue_number']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No appointments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
