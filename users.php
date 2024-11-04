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
    
    <!-- DataTables CSS -->
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
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn-accept {
            background-color: green;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .btn-decline {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .btn-delete {
            background-color: orange; /* Customize the delete button */
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
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
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h2>Registered Users</h2>

        <!-- Button to Open the Modal -->
        <button id="addUserBtn" style="margin-bottom: 20px;">+</button>
        
        <div class="table-responsive">
        <table id="usersTable" class="display">
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Contact Number</th>
            <th>Valid ID</th>
            <th>Profile Picture</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Query to fetch all registered users with their status
    $sql = "SELECT id, fullname, email, address, contact_no, valid_id, profile_picture, status 
            FROM users 
            ORDER BY CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'approved' THEN 2 
                ELSE 3 
            END";
    $result = $conn->query($sql);

    // Check if there are any users
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                <td><img src="<?php echo htmlspecialchars($row['valid_id']); ?>" alt="Valid ID" width="100" height="100"></td>
                <td><img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture" width="100" height="100"></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
            <?php
        }
    } else {
        // If no users are found
        echo "<p>No registered users found.</p>";
    }
    $conn->close();
    ?>
    </tbody>
</table>

        </div>

        <!-- Modal for Adding User -->
        <div id="addUserModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Add User</h2>
                <form action="admin_process_register.php" method="post" enctype="multipart/form-data">
        <label for="fullname">Full Name:</label><br>
        <input type="text" id="fullname" name="fullname" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <label for="address">Address:</label><br>
        <textarea id="address" name="address" required></textarea><br><br>

        <label for="contact_no">Contact Number:</label><br>
        <input type="text" id="contact_no" name="contact_no" required><br><br>

        <label for="valid_id">Valid ID (JPEG/PNG):</label><br>
        <input type="file" id="valid_id" name="valid_id" accept="image/jpeg,image/png" required><br><br>

        <label for="profile_picture">Profile Picture (JPEG/PNG):</label><br>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png" required><br><br>

        <input type="submit" value="Register">
    </form>
            </div>
        </div>

    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable(); // Initialize DataTable
            
            // Get the modal
            var modal = document.getElementById("addUserModal");

            // Get the button that opens the modal
            var btn = document.getElementById("addUserBtn");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks the button, open the modal 
            btn.onclick = function() {
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>
</body>
</html>
