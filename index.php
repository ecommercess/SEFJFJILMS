<?php
session_start();

include 'db.php';

$is_logged_in = isset($_SESSION['user_id']);

$login_success_message = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : false;
unset($_SESSION['login_success']); 

$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_profile.png'; // Fallback if no profile picture
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        /* General styling */
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        
        /* Navbar styling */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color:#004643;
            padding: 10px 20px;
            color: black;
            height: 70px;
            position: fixed;
            width: 100%;
            z-index: 5000;    
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

/* Media query for screens 500px or less */
@media (max-width: 500px) {    
    .navbar-logo img {
        height: 30px; /* Adjust to desired size */
    }
}

        
        /* Center nav links styling */
        .nav-links {
            display: flex;
            gap: 15px;
            font-size: 18px;

        }
        .nav-links a {
            color: #abd1c6;
            text-decoration: none;
            font-size: 1em;
            transition: color 0.3s;  
        }
        .nav-links a:hover { color: white; }

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
            background-color: white;  
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
        }
        .profile-dropdown a {
            display: block;
            padding: 10px;
            color: black;
            text-decoration: none;
            text-align: left;
            width: 100px;
        }
        .profile-dropdown a:hover { background-color: #2F4F4F; color:white; }
        
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
            margin-top: 50px;
        }
        .mobile-menu a:hover { background-color: #444; }
        
        @media (max-width: 1000px) {
            .nav-links { display: none; }
            .hamburger { display: block; }
        }
 
        section {
            width: 100%;
            height: 100vh; /* Each section takes full viewport height */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            
            box-sizing: border-box; /* Include padding in height */
            z-index: 2000; /* Increase z-index */
        }

        /* Different background colors for sections */
        #home {
    position: relative; /* Allows overlay positioning */
    background-image: url('img/background.jpg'); /* Replace with your image path */
    background-size: cover; /* Cover the entire section */
    background-repeat: no-repeat; /* Prevent the image from repeating */
    background-position: center; /* Center the image */
    background-color: white; /* Fallback color */
    height: 100vh; /* Full viewport height */
    display: flex; /* Center content */
    align-items: center; /* Center vertically */
    justify-content: center; /* Center horizontally */
    overflow: hidden;
}

#home::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.4); /* Black overlay with 40% opacity */
    z-index: 1; /* Layer it below text content */
}

.appointment-section {
    position: relative; /* Ensure it's above the overlay */
    z-index: 2; /* Places text content above the overlay */
    text-align: center;
    color: white; /* White text for contrast */
    flex-direction: column; /* Stacks heading and button vertically */
    align-items: center; /* Centers content horizontally */
}

.appointment-section h2 {
    font-size: 2.5rem;
    color: #FFF;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
    font-weight: bold;
    margin-bottom: 20px;
}

/* Media query for screens 500px or less */
@media (max-width: 500px) {
    .appointment-section h2 {
        font-size: 1.5rem; 
    }
}



        #calendar { background-color: white; }
        #gallery { background-color: #F0E5D8    ; }
        #packages { background-color: #cfcfcf; }
        #about { background-color: #bfbfbf; }
        #faqs { background-color: #afafaf; }
        #contact { background-color: #9f9f9f; }

        
        .navbar-logo span {
    font-family: 'Poppins', sans-serif; /* Modern font style */
    font-size: 1.2em;
    font-weight: bold;
    color: #fffffe; /* Light color to stand out on a dark background */
    position: absolute;
    left: 70px;
}
@media (max-width: 500px) {
    .navbar-logo span {
        font-size: 0.9em; /* Adjust to desired size */
        left: 57px;
    }
}
    /* Add transition and scaling effect */
    .appointment-icon {
        transition: transform 0.3s ease, color 0.3s ease;
    }
    
    /* Scale up the icon on hover */
    .appointment-icon:hover {
        transform: scale(1.2); /* Adjust scale as desired */
        color: #ffd700; /* Optional: Change color on hover */
    }
    .announcement-marquee {
    position: absolute;
    top: 70px;
    left: 0;
    width: 100%;
    overflow: hidden;
    
    color: #FFF9D9;
    padding: 15px 0;
    text-align: center;
    z-index: 10;
    border-radius: 8px;
}

.announcement-content {
    display: inline-block;
    white-space: nowrap;
    animation: marquee 10s linear infinite;
}

.announcement-content strong {
    color: #f9bc60;
    margin-right: 10px;
}

.announcement-content span {
    margin-right: 15px;
}

@keyframes marquee {
    from { transform: translateX(100%); }
    to { transform: translateX(-100%); }
}
/* Megaphone Icon styling */
.megaphone-icon {
    position: absolute;
    top: 80px;
    left: 15px; /* Adjust positioning as desired */
    font-size: 1.5em;
    color: white;
    cursor: pointer;
    z-index: 15;
    transition: color 0.3s ease;
}

.megaphone-icon:hover {
    color: #FFD700; /* Change color on hover */
}
    </style>
</head>
<body>

<div class="navbar">

    <div class="navbar-logo">
        <img src="img/logo.png" alt="Company Logo">
        <span>FJFILMS PHOTOGRAPHY</span>
    </div>

  
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="calendar.php">Calendar</a>
        <a href="galleryuser.php">Gallery</a>
        <a href="packagesuser.php">Packages</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </div>

    <div class="navbar-right">
        <?php if ($is_logged_in): ?>
            
            <div class="profile" onclick="toggleProfileDropdown()">
                <div class="profile-picture">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="profile.php">My Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
            <?php else: ?>
    <a href="login.php" style="display: inline-block; padding: 10px 15px; margin: 0 5px; font-size: 15px; background: #f9bc60; color: #001e1d; text-decoration: none; border-radius: 5px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease; position: absolute; right: 50px;"
       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0px 6px 10px rgba(0, 0, 0, 0.2)'; this.style.filter='brightness(1)';"
       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0px 4px 6px rgba(0, 0, 0, 0.1)'; this.style.filter='brightness(1)';">
        Login
    </a>
<?php endif; ?>

    </div>


    <i class="fas fa-bars hamburger" onclick="toggleMobileMenu()"></i>
</div>

<div class="mobile-menu" id="mobileMenu">
    <a href="index.php">Home</a>
    <a href="calendar.php">Calendar</a>
    <a href="galleryuser.php">Gallery</a>
    <a href="packagesuser.php">Packages</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
</div>



<div class="home-section">
<?php
// Fetch the latest announcement
$announcement = null;
$query = "SELECT title, detail, date FROM announcements ORDER BY date DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $announcement = mysqli_fetch_assoc($result);
}
?>

<section id="home">
    <!-- Megaphone Icon -->
    <div class="megaphone-icon" onclick="toggleAnnouncementMarquee()">
        <i class="fas fa-bullhorn"></i>
    </div>

    <!-- Announcement Marquee - initially hidden -->
    <?php if ($announcement): ?>
    <div class="announcement-marquee" id="announcementMarquee" style="display: none;">
        <div class="announcement-content">
            <strong><?php echo htmlspecialchars($announcement['title']); ?>:</strong>
            <span><?php echo htmlspecialchars($announcement['detail']); ?></span>
            <span class="announcement-date">(<?php echo date("F j, Y", strtotime($announcement['date'])); ?>)</span>
        </div>
    </div>
    <?php endif; ?>

    <div class="appointment-section">
        <h2>Book Your Appointment now!</h2> 
        <a href="appointment_form.php">
            <svg class="appointment-icon w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="none" viewBox="0 0 24 24" style="color:#f9bc60;">
                <path fill="currentColor" d="M4 9.05H3v2h1v-2Zm16 2h1v-2h-1v2ZM10 14a1 1 0 1 0 0 2v-2Zm4 2a1 1 0 1 0 0-2v2Zm-3 1a1 1 0 1 0 2 0h-2Zm2-4a1 1 0 1 0-2 0h2Zm-2-5.95a1 1 0 1 0 2 0h-2Zm2-3a1 1 0 1 0-2 0h2Zm-7 3a1 1 0 0 0 2 0H6Zm2-3a1 1 0 1 0-2 0h2Zm8 3a1 1 0 1 0 2 0h-2Zm2-3a1 1 0 1 0-2 0h2Zm-13 3h14v-2H5v2Zm14 0v12h2v-12h-2Zm0 12H5v2h14v-2Zm-14 0v-12H3v12h2Zm0 0H3a2 2 0 0 0 2 2v-2Zm14 0v2a2 2 0 0 0 2-2h-2Zm0-12h2a2 2 0 0 0-2-2v2Zm-14-2a2 2 0 0 0-2 2h2v-2Zm-1 6h16v-2H4v2ZM10 16h4v-2h-4v2Zm3 1v-4h-2v4h2Zm0-9.95v-3h-2v3h2Zm-5 0v-3H6v3h2Zm10 0v-3h-2v3h2Z"/>
            </svg>
        </a>
    </div>
</section>

<script>
 
    // Toggle visibility of the announcement marquee
    function toggleAnnouncementMarquee() {
        const announcementMarquee = document.getElementById('announcementMarquee');
        announcementMarquee.style.display = announcementMarquee.style.display === 'none' ? 'block' : 'none';
    }

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