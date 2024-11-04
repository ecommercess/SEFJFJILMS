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

// Handle form submission for new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcementTitle'])) {
    // Get announcement data from POST request
    $title = $_POST['announcementTitle'];
    $detail = $_POST['announcementDetail'];
    $date = $_POST['announcementDate'];

    // Insert announcement into database
    $sql = "INSERT INTO announcements (title, detail, date) VALUES ('$title', '$detail', '$date')";
    $conn->query($sql);
}

// Handle update announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateAnnouncementId'])) {
    $id = intval($_POST['updateAnnouncementId']);
    $title = $_POST['updateAnnouncementTitle'];
    $detail = $_POST['updateAnnouncementDetail'];
    $date = $_POST['updateAnnouncementDate'];

    // Update announcement in the database
    $sql = "UPDATE announcements SET title = '$title', detail = '$detail', date = '$date' WHERE id = $id";
    $conn->query($sql);
}

// Handle delete announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAnnouncementId'])) {
    $id = intval($_POST['deleteAnnouncementId']);

    // Delete announcement from the database
    $sql = "DELETE FROM announcements WHERE id = $id";
    $conn->query($sql);
}

// Fetch announcements from the database
$announcements = $conn->query("SELECT * FROM announcements ORDER BY date DESC");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
            padding-top: 60px; /* Location of the box */
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
            gap: 5px;
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
        <button id="addAnnouncementBtn" style="margin-top: 20px;">+</button> <!-- Add button -->
        
        <h2>Announcements</h2>
        <table id="announcementsTable" class="display">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Detail</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                        <td><?php echo htmlspecialchars($announcement['detail']); ?></td>
                        <td><?php echo htmlspecialchars($announcement['date']); ?></td>
                        <td class="action-buttons">
                            <button class="updateBtn" data-id="<?php echo htmlspecialchars($announcement['id']); ?>" 
                                    data-title="<?php echo htmlspecialchars($announcement['title']); ?>" 
                                    data-detail="<?php echo htmlspecialchars($announcement['detail']); ?>" 
                                    data-date="<?php echo htmlspecialchars($announcement['date']); ?>">Update</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="deleteAnnouncementId" value="<?php echo htmlspecialchars($announcement['id']); ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- The Modal for adding announcement -->
    <div id="announcementModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Announcement</h2>
            <form id="announcementForm">
                <label for="announcementTitle">Title:</label>
                <input type="text" id="announcementTitle" name="announcementTitle" required><br><br>
                <label for="announcementDetail">Detail:</label>
                <textarea id="announcementDetail" name="announcementDetail" required></textarea><br><br>
                <label for="announcementDate">Date:</label>
                <input type="date" id="announcementDate" name="announcementDate" required><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>

    <!-- The Modal for updating announcement -->
    <div id="updateAnnouncementModal" class="modal">
        <div class="modal-content">
            <span class="closeUpdate">&times;</span>
            <h2>Update Announcement</h2>
            <form id="updateAnnouncementForm">
                <input type="hidden" id="updateAnnouncementId" name="updateAnnouncementId">
                <label for="updateAnnouncementTitle">Title:</label>
                <input type="text" id="updateAnnouncementTitle" name="updateAnnouncementTitle" required><br><br>
                <label for="updateAnnouncementDetail">Detail:</label>
                <textarea id="updateAnnouncementDetail" name="updateAnnouncementDetail" required></textarea><br><br>
                <label for="updateAnnouncementDate">Date:</label>
                <input type="date" id="updateAnnouncementDate" name="updateAnnouncementDate" required><br><br>
                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#announcementsTable').DataTable(); // Initialize DataTable

            // Get the modal for adding announcement
            var modal = document.getElementById("announcementModal");
            var btn = document.getElementById("addAnnouncementBtn");
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

            // Handle form submission for adding announcement via AJAX
            $('#announcementForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    type: 'POST',
                    url: '', // Current page
                    data: $(this).serialize(),
                    success: function(response) {
                        // Reload the page to see the new announcement
                        location.reload();
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

            // Get the modal for updating announcement
            var updateModal = document.getElementById("updateAnnouncementModal");
            var closeUpdateSpan = document.getElementsByClassName("closeUpdate")[0];

            // When the user clicks on update button, open the update modal
            $('.updateBtn').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var detail = $(this).data('detail');
                var date = $(this).data('date');

                $('#updateAnnouncementId').val(id);
                $('#updateAnnouncementTitle').val(title);
                $('#updateAnnouncementDetail').val(detail);
                $('#updateAnnouncementDate').val(date);

                updateModal.style.display = "block";
            });

            // When the user clicks on <span> (x), close the update modal
            closeUpdateSpan.onclick = function() {
                updateModal.style.display = "none";
            }

            // When the user clicks anywhere outside of the update modal, close it
            window.onclick = function(event) {
                if (event.target == updateModal) {
                    updateModal.style.display = "none";
                }
            }

            // Handle form submission for updating announcement via AJAX
            $('#updateAnnouncementForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    type: 'POST',
                    url: '', // Current page
                    data: $(this).serialize(),
                    success: function(response) {
                        // Reload the page to see the updated announcement
                        location.reload();
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
