

<?php
session_start();

include 'db.php';

$is_logged_in = isset($_SESSION['user_id']);

if ($is_logged_in) {
    $fullname = $_SESSION['fullname'];
    $profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_profile.png';
}

$login_success_message = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : false;
unset($_SESSION['login_success']); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* General styling */
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        
        /* Navbar styling */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
            color: white;
            height: 70px;
            position: fixed;
            width: 100%;
            z-index: 1;
        }
        
        /* Logo styling */
        .navbar-logo {
            display: flex;
            align-items: center;
        }
        .navbar-logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        /* Center nav links styling */
        .nav-links {
            display: flex;
            gap: 15px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1em;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #ddd; }
        
        /* Right side profile, login/register styling */
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-right a, .navbar-right button {
            color: white;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1em;
            text-decoration: none;
        }
        .navbar-right .profile {
            position: relative;
            cursor: pointer;
        }
        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }
        .profile-picture img { width: 100%; height: 100%; }
        
        /* Profile dropdown menu */
        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #444;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
        }
        .profile-dropdown a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            text-align: left;
        }
        .profile-dropdown a:hover { background-color: #555; }
        
        /* Mobile menu */
        .hamburger { display: none; font-size: 1.5em; cursor: pointer; }
        .mobile-menu {
            display: none;
            flex-direction: column;
            background-color: #333;
            width: 100%;
            align-items: center;
        }
        .mobile-menu a {
            padding: 10px;
            color: white;
            text-align: center;
            text-decoration: none;
            width: 100%;
        }
        .mobile-menu a:hover { background-color: #444; }
        
        @media (max-width: 1000px) {
            .nav-links { display: none; }
            .hamburger { display: block; }
        }
        .appointment-section {
            text-align: center;
            margin-top: 40px;
        }
        .appointment-btn {
            padding: 10px 15px;
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
        section {
            width: 100%;
            height: 100vh; /* Each section takes full viewport height */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 100px;
            box-sizing: border-box; /* Include padding in height */
            z-index: 2000; /* Increase z-index */
        }

        /* Different background colors for sections */
        #home { background-color: #f4f4f4; }
        #calendar { background-color: #ffffff; }
        #gallery { background-color: #d9d9d9; }
        #packages { background-color: #cfcfcf; }
        #about { background-color: #bfbfbf; }
        #faqs { background-color: #afafaf; }
        #contact { background-color: #9f9f9f; }
    </style>
</head>
<body>

<div class="navbar">
    <!-- Logo and title -->
    <div class="navbar-logo">
        <img src="img/logo.png" alt="Company Logo">
        <span>FJFILMS Photography</span>
    </div>

    <!-- Center navigation links -->
    <div class="nav-links">
        <a href="#home">Home</a>
        <a href="#calendar">Calendar</a>
        <a href="#gallery">Gallery</a>
        <a href="#packages">Packages</a>
        <a href="#about">About</a>
        <a href="#faqs">Faqs</a>
        <a href="#contact">Contact</a>
    </div>

    <!-- Right side login/register or profile -->
    <div class="navbar-right">
        <?php if ($is_logged_in): ?>
            <div class="profile" onclick="toggleProfileDropdown()">
                <div class="profile-picture">
                    <img src="img/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="profile.php">My Profile</a>
                    <a href="javascript:void(0)" onclick="confirmLogout()">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>

    <!-- Hamburger icon for mobile -->
    <i class="fas fa-bars hamburger" onclick="toggleMobileMenu()"></i>
</div>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="#home">Home</a>
    <a href="#calendar">Calendar</a>
    <a href="#gallery">Gallery</a>
    <a href="#packages">Packages</a>
    <a href="#about">About</a>
    <a href="#faqs">Faqs</a>
    <a href="#contact">Contact</a>
</div>

<div class="home-section">

    <section id="home">

        <div class="appointment-section">
            <h2>Ready to Book Your Appointment?</h2>
            <button class="appointment-btn" onclick="location.href='appointment_form.php'">
                <i class="fas fa-calendar-check"></i> <!-- Changed icon to calendar check -->
            </button>
        </div>

    </section>

</div>



<div class="gallery-section">
    <section id="gallery">
        
    </section>
</div>


<div class="gallery-section">
    <section id="gallery">
        
    </section>
</div>


<div class="package-section">
    <section id="packages">


    </section>
</div>

<div class="about-section">
    <section id="about">this is your about

    </section>
</div>

<div class="faqs-section">
    <section id="faqs">this is your faqs

    </section>
</div>

<div class="contact-section">
    <section id="contact">
        this is your contact
    </section>
</div>
<script>
    // Toggle mobile menu display
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.style.display = mobileMenu.style.display === 'flex' ? 'none' : 'flex';
    }

    // Hide mobile menu when screen resizes to a larger view
    const mediaQuery = window.matchMedia("(min-width: 1000px)");
    function handleScreenResize(e) {
        if (e.matches) {
            document.getElementById('mobileMenu').style.display = 'none';
        }
    }
    mediaQuery.addListener(handleScreenResize);
    handleScreenResize(mediaQuery);

    // Toggle profile dropdown
    function toggleProfileDropdown() {
        const profileDropdown = document.getElementById('profileDropdown');
        profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Hide profile dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.closest('.profile')) {
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                profileDropdown.style.display = 'none';
            }
        }
    }

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
            title: 'Welcome!',
            text: 'You have successfully logged in.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
</script>

</body>
</html>




