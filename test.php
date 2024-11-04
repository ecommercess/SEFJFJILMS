<?php
session_start();
// Include database connection
include 'db.php';

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// If logged in, get user's full name and profile picture
if ($is_logged_in) {
    $fullname = $_SESSION['fullname'];
    $profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_profile.png';
}
$login_success_message = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : '';
unset($_SESSION['login_success']); // Clear the message after displaying it
// After successful login


// Query for packages
$packagesQuery = "
    SELECT p.id AS package_id, p.package_name, p.package_price, p.package_type, 
    GROUP_CONCAT(c.content SEPARATOR '\n') AS contents
    FROM packages p
    LEFT JOIN package_contents c ON p.id = c.package_id
    GROUP BY p.id";
$packagesResult = $conn->query($packagesQuery);

if (!$packagesResult) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Navbar styling */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
            color: white;
            position: relative;
            height: 50px; /* Adjusted height */
        }
        
        /* Logo and title styling */
        .navbar-logo {
            position: absolute;
            left: 20px;
            display: flex;
            align-items: center;
            font-size: 1.2em;
        }
        
        .navbar-logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        /* Middle tag links styling */
        .nav-links {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1em;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #ddd;
        }

        /* Profile and dropdown styling */
        .profile-container {
            position: absolute;
            right: 50px;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            color: black;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            width: 100px;
            border-radius: 5px;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 15px;
            color: black;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: #ddd;
        }

        .profile-container:hover .dropdown-menu {
            display: block;
        }

        /* Responsive menu styling */
        .hamburger {
            display: none;
            font-size: 1.5em;
            cursor: pointer;
            position: absolute;
            right: 20px;
        }

        .mobile-menu {
            display: none;
            flex-direction: column;
            background-color: #333;
            width: 100%;
            align-items: center;
        }

        .mobile-menu a {
            padding: 10px;
            border-top: 1px solid #444;
            color: white;
            text-align: center;
            text-decoration: none;
            width: 100%;
        }

        .mobile-menu a:hover {
            background-color: #444;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hamburger {
                display: block;
            }
        }
        .package-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            width: 150px;
            height: auto;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .package-box:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .package-title {
            font-size: 1.2em;
            font-weight: bold;
        }
        .package-price {
            font-size: 1em;
            color: green;
            margin-top: 10px;
        }
        /* Make an Appointment button styling */
        .appointment-section {
            text-align: center;
            margin-top: 40px;
        }
        .appointment-btn {
            padding: 15px 30px;
            background-color: #0b5e1d; /* Adjusted background color */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .appointment-btn:hover {
            background-color: #086b24; /* Darker green on hover */
        }
    </style>
</head>
<body>

<div class="navbar">
    <!-- Logo and title -->
    <div class="navbar-logo">
        <img src="img/logo.png" alt="Company Logo">
        <span>FJFILMS Photography</span>
    </div>

    <!-- Navigation links (middle of navbar) -->
    <div class="nav-links">
        <a href="#home">Home</a>
        <a href="#services">Services</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
    </div>

    <!-- Hamburger icon for mobile -->
    <i class="fas fa-bars hamburger" onclick="toggleMobileMenu()"></i>

    <!-- Profile icon and dropdown menu (right side) -->
    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile" class="profile-icon">
        <div class="dropdown-menu">
            <a href="profile.php">My Profile</a>
            <a href="#" onclick="confirmLogout()">Logout</a>
        </div>
    </div>
</div>

<!-- Mobile menu that appears on small screens -->
<div class="mobile-menu" id="mobileMenu">
    <a href="#home">Home</a>
    <a href="#services">Services</a>
    <a href="#about">About</a>
    <a href="#contact">Contact</a>
</div>

<!-- Content (packages and booking button) -->
<div class="content">
    <?php while ($row = $packagesResult->fetch_assoc()): ?>
        <div class="package-box" 
             onclick="location.href='booking_form.php?id=<?php echo $row['package_id']; ?>&price=<?php echo $row['package_price']; ?>'">
            <div class="package-title"><?php echo htmlspecialchars($row['package_name']); ?></div>
            <div><?php echo nl2br(htmlspecialchars($row['contents'])); ?></div>
            <div class="package-price">₱<?php echo htmlspecialchars($row['package_price']); ?></div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Appointment Section -->
<div class="appointment-section">
    <h2>Ready to Book Your Appointment?</h2>
    <button class="appointment-btn" onclick="location.href='appointment_form.php'">
        <i class="fas fa-calendar-alt"></i>
    </button>
</div>

<script>
    // Toggle mobile menu display
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.style.display = mobileMenu.style.display === 'flex' ? 'none' : 'flex';
    }

    // Automatically hide mobile menu when resizing to a larger screen
    const mediaQuery = window.matchMedia("(min-width: 769px)");
    function handleScreenResize(e) {
        if (e.matches) {
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileMenu) {
                mobileMenu.style.display = 'none';
            }
        }
    }

    // Attach listener to monitor screen size changes
    mediaQuery.addListener(handleScreenResize);
    handleScreenResize(mediaQuery); // Run once on load to set initial state

    // Confirm logout function
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log me out!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    }
    <?php if ($login_success_message): ?>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo addslashes($login_success_message); ?>', // This should be a string
        confirmButtonText: 'OK'
    });
<?php endif; ?>

</script>

</body>
</html>
