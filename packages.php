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

// Fetch packages and their contents, using GROUP_CONCAT to merge contents
$packagesQuery = "
    SELECT p.id, p.package_name, p.package_price, p.package_type, 
    GROUP_CONCAT(c.content SEPARATOR '\n') AS contents
    FROM packages p
    LEFT JOIN package_contents c ON p.id = c.package_id
    GROUP BY p.id";
$packagesResult = $conn->query($packagesQuery);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Packages</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
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
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Dynamic content styling */
        .content-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .content-group input {
            flex: 1;
            margin-right: 10px;
        }
        .add-content, .remove-content {
            cursor: pointer;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
        }
        .remove-content {
            background-color: #dc3545;
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
    <h1>Manage Packages</h1>
    <button id="addPackageBtn">+</button> <!-- Add button -->

    <!-- Display the packages data -->
    <table id="packagesTable" class="display">
        <thead>
            <tr>
                <th>Package Name</th>
                <th>Price</th>
                <th>Type</th>
                <th>Contents</th>
                <th>Action</th> <!-- Add Action Column -->
            </tr>
        </thead>
        <tbody>
    <?php while ($row = $packagesResult->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['package_name']); ?></td>
        <td><?php echo htmlspecialchars($row['package_price']); ?></td>
        <td><?php echo htmlspecialchars($row['package_type']); ?></td>
        <td style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars($row['contents'])); ?></td>
        <td>
            <button class="update-btn" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['package_name']); ?>" data-price="<?php echo htmlspecialchars($row['package_price']); ?>" data-type="<?php echo htmlspecialchars($row['package_type']); ?>" data-contents="<?php echo htmlspecialchars($row['contents']); ?>">Update</button>
            <button class="delete-btn" data-id="<?php echo $row['id']; ?>">Delete</button>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>

    </table>
</div>



   
<div id="packageModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">Add Package</h2>
        <form id="packageForm" action="submit_package.php" method="POST">
            <input type="hidden" id="packageId" name="packageId"> <!-- Hidden input for package ID -->

            <label for="packageName">Package Name:</label>
            <input type="text" id="packageName" name="packageName" required><br><br>

            <label for="packagePrice">Package Price:</label>
            <input type="number" id="packagePrice" name="packagePrice" step="0.01" required><br><br>

            <label for="packageType">Package Type:</label>
            <select id="packageType" name="packageType" required>
                <option value="photo">Photo</option>
                <option value="photo and video">Photo and Video</option>
            </select><br><br>

            <h3>Contents</h3>
            <div id="contentsContainer">
                <div class="content-group">

                    <input type="text" name="contents[]" placeholder="Enter content" required>

                    <button type="button" class="add-content">Add Content</button>
                </div>
            </div>
            <br>

            <input type="submit" value="Submit">
        </form>
    </div>
</div>



    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
     $(document).ready(function() {
    // Initialize DataTables
    $('#packagesTable').DataTable();

    // Modal functionality
    var modal = document.getElementById("packageModal");
    var btn = document.getElementById("addPackageBtn");
    var span = document.getElementsByClassName("close")[0];
    var modalTitle = document.getElementById("modalTitle");
    var packageForm = document.getElementById("packageForm");
    var packageIdInput = document.getElementById("packageId");
    var packageNameInput = document.getElementById("packageName");
    var packagePriceInput = document.getElementById("packagePrice");
    var packageTypeInput = document.getElementById("packageType");
    var contentsContainer = document.getElementById("contentsContainer");

    // Reset modal for new package
    btn.onclick = function() {
        modalTitle.innerText = "Add Package";
        packageForm.action = "submit_package.php"; // Set the form action for adding
        packageIdInput.value = "";
        packageNameInput.value = "";
        packagePriceInput.value = "";
        packageTypeInput.value = "photo"; // default to 'photo'
        contentsContainer.innerHTML = `<div class="content-group">
                                        <input type="text" name="contents[]" placeholder="Enter content" required>
                                        <button type="button" class="add-content">Add Content</button>
                                      </div>`;
        modal.style.display = "block";
    };

    span.onclick = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // Handle Update button click
    $(document).on('click', '.update-btn', function() {
    modalTitle.innerText = "Update Package";
    packageForm.action = "update_package.php"; // Set the form action for updating
    var packageId = $(this).data("id");
    var packageName = $(this).data("name");
    var packagePrice = $(this).data("price");
    var packageType = $(this).data("type");
    var packageContents = $(this).data("contents").split("\n");

    packageIdInput.value = packageId;
    packageNameInput.value = packageName;
    packagePriceInput.value = packagePrice;
    packageTypeInput.value = packageType;

    // Clear existing contents
    contentsContainer.innerHTML = "";

    // Populate existing contents
    packageContents.forEach(function(content, index) {
        contentsContainer.innerHTML += `<div class="content-group">
                                            <input type="text" name="contents[]" value="${content}" required>
                                            <button type="button" class="remove-content">Remove</button>
                                        </div>`;
    });

    // Add the "Add Content" button after populating existing contents
    contentsContainer.innerHTML += `<div class="content-group">
                                        <button type="button" class="add-content">Add Content</button>
                                    </div>`;

    modal.style.display = "block";
});


    // Dynamic content fields - add new content
    $(document).on('click', '.add-content', function() {
        var contentHtml = `
            <div class="content-group">
                <input type="text" name="contents[]" placeholder="Enter content" required>
                <button type="button" class="remove-content">Remove</button>
            </div>`;
        $('#contentsContainer').append(contentHtml);
    });

    // Dynamic content fields - remove content
    $(document).on('click', '.remove-content', function() {
        $(this).closest('.content-group').remove();
    });

    // Handle Delete button click
    $(document).on('click', '.delete-btn', function() {
        var packageId = $(this).data("id");
        if (confirm("Are you sure you want to delete this package?")) {
            // Make an AJAX request to delete the package
            $.post("delete_package.php", { packageId: packageId }, function(response) {
                location.reload(); // Reload the page after deletion
            });
        }
    });
});


    </script>
</body>
</html>
