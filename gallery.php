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

// Handle form submission for gallery items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['galleryType'];

    // Common data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $uploadOk = 1;

    if ($type === 'photo' || $type === 'featured') {
        $targetDir = "uploads/photos/";
        $fileKey = 'photoFile';
    } elseif ($type === 'video') {
        $targetDir = "uploads/videos/";
        $fileKey = 'videoFile';
    } else {
        echo "Invalid gallery type.";
        exit();
    }

    $targetFile = $targetDir . basename($_FILES[$fileKey]["name"]);
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // For photos
    if ($type === 'photo' || $type === 'featured') {
        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES[$fileKey]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES[$fileKey]["size"] > 50000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
    }
    // For videos
    elseif ($type === 'video') {
        // Check file size (limit to 20MB)
        if ($_FILES[$fileKey]["size"] > 20000000) {
            echo "Sorry, your video file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($fileType, ['mp4', 'avi', 'mov'])) {
            echo "Sorry, only MP4, AVI & MOV files are allowed.";
            $uploadOk = 0;
        }
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $targetFile)) {
            // Insert into database using prepared statement
            $stmt = $conn->prepare("INSERT INTO gallery (title, description, type, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $description, $type, $targetFile);

            if ($stmt->execute()) {
                // Redirect to gallery.php
                header("Location: gallery.php");
                exit(); // Ensure to exit after redirecting
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Fetch gallery items
$galleryItems = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");

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

        /* Styles for type selection */
        .type-selection {
            margin: 20px 0;
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
        <h1>Gallery Management</h1>
        <button id="openModalBtn">+</button>
        <!-- Form for adding new gallery items -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="type-selection">
                        <label for="galleryType">Select Gallery Type:</label>
                        <select name="galleryType" id="galleryType" required>
                            <option value="" disabled selected>Select type</option>
                            <option value="photo">Photo</option>
                            <option value="video">Video</option>
                            <option value="featured">Featured</option>
                        </select>
                    </div>

                    <!-- Rest of the form inputs hidden initially -->
                    <div id="formInputs" style="display:none;">
                        <div>
                            <label for="title">Title:</label>
                            <input type="text" name="title" required>
                        </div>
                        <div>
                            <label for="description">Description:</label>
                            <textarea name="description" required></textarea>
                        </div>
                        <div id="fileInputContainer">
                            <label for="photoFile" style="display:none;">Upload Photo:</label>
                            <input type="file" name="photoFile" accept="image/*" style="display:none;">
                            <label for="videoFile" style="display:none;">Upload Video:</label>
                            <input type="file" name="videoFile" accept="video/*" style="display:none;">
                        </div>
                        <button type="submit">Upload</button>
                    </div>
                </form>
            </div>
        </div>
        
        <h2>Gallery Items</h2>
        <table id="galleryTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>File Path</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $galleryItems->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td>
                        <?php if ($row['type'] === 'photo' || $row['type'] === 'featured'): ?>
                            <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="max-width: 100px; max-height: 100px;">
                        <?php elseif ($row['type'] === 'video'): ?>
                            <video width="100" height="100" controls>
                                <source src="<?php echo htmlspecialchars($row['file_path']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                    </td>
                    <td><button onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#galleryTable').DataTable({
                // You can customize DataTable options here
                "order": [[0, "desc"]] // Order by ID descending
            });

            // Show/hide file input fields based on gallery type selection
            $('#galleryType').change(function() {
                const type = $(this).val();
                $('#formInputs').show();  // Show the form inputs once a type is selected

                if (type === 'photo' || type === 'featured') {
                    $('input[name="photoFile"]').show().siblings('label').show();
                    $('input[name="videoFile"]').hide().siblings('label').hide();
                } else if (type === 'video') {
                    $('input[name="photoFile"]').hide().siblings('label').hide();
                    $('input[name="videoFile"]').show().siblings('label').show();
                }
            });

            // Open the modal when "+" button is clicked
            $('#openModalBtn').on('click', function() {
                $('#myModal').show();
                $('#formInputs').hide();  // Hide the form inputs initially
            });

            // Close the modal when 'x' is clicked
            $('.close').on('click', function() {
                $('#myModal').hide();
            });

            // Hide the modal when clicking outside of it
            $(window).on('click', function(event) {
                if ($(event.target).is('#myModal')) {
                    $('#myModal').hide();
                }
            });
        });

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this item?")) {
                window.location.href = "delete_gallery.php?id=" + id; // Redirect to delete script
            }
        }
    </script>
</body>
</html>
