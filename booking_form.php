<?php
session_start();
include 'db.php'; // Include your database connection

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Get package ID and price from the URL
$package_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$package_price = isset($_GET['price']) ? floatval($_GET['price']) : 0.00;

// Fetch package name for display
$packageQuery = "SELECT package_name FROM packages WHERE id = $package_id";
$packageResult = $conn->query($packageQuery);
$package = $packageResult->fetch_assoc();

if (!$package) {
    die("Package not found.");
}

$package_name = htmlspecialchars($package['package_name']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Basic styling for the booking form */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<h2>Booking Form for <?php echo $package_name; ?></h2>
<p>Price: ₱<?php echo number_format($package_price, 2); ?></p>

<form id="bookingForm" action="submit_booking.php" method="POST" enctype="multipart/form-data">
    <?php if ($is_logged_in): ?>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
    <?php else: ?>
        <p>You must be logged in to make a booking.</p>
        <a href="login.php">Login Here</a>
        <?php exit(); // Stop further processing ?>
    <?php endif; ?>
    <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
    
    <div class="form-group">
        <label for="event_location">Event Location:</label>
        <select id="event_location" name="event_location" required>
            <option value="">Select Location</option>
            <option value="Hotel A">Hotel A</option>
            <option value="Hotel B">Hotel B</option>
            <option value="Hotel C">Hotel C</option>
            <option value="Other">Other</option>
        </select>
        <input type="text" name="custom_location" placeholder="If 'Other', specify here" style="display:none;">
    </div>

    <div class="form-group">
        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" required>
    </div>

    <div class="form-group">
        <label for="receipt_photo">Receipt Photo of Downpayment:</label>
        <input type="file" id="receipt_photo" name="receipt_photo" accept="image/*" required>
    </div>

    <input type="submit" value="Submit Booking" onclick="confirmBooking(event)">
</form>

<script>
    function confirmBooking(event) {
        event.preventDefault(); // Prevent the default form submission

        Swal.fire({
            title: 'Confirm Booking',
            text: "Are you sure you want to submit your booking?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form
                document.getElementById('bookingForm').submit();
            }
        });
    }

    // Show custom location input if 'Other' is selected
    document.getElementById('event_location').addEventListener('change', function() {
        const customLocationInput = document.querySelector('input[name="custom_location"]');
        if (this.value === 'Other') {
            customLocationInput.style.display = 'block';
        } else {
            customLocationInput.style.display = 'none';
            customLocationInput.value = ''; // Clear the input if not needed
        }
    });
    
</script>

</body>
</html>
