<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            display: flex; /* Use flexbox */
            align-items: center; /* Center items vertically */
        }

        .logo {
            flex: 1; /* Make logo section flexible */
            text-align: center; /* Center the logo */
        }

        .logo img {
            max-width: 100px; /* Limit logo size */
            height: auto; /* Maintain aspect ratio */
        }

        .form-container {
            flex: 2; /* Make form section flexible */
            padding-left: 20px; /* Add space between logo and form */
        }

        h2 {
            margin-bottom: 20px;
            color: #004643; /* Matching color scheme */
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .input-container input {
            width: 75%;
            padding: 10px 40px 10px 40px; /* Add padding for icons */
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .input-container i {
            position: absolute;
            left: 10px; /* Position the icon */
            top: 50%;
            transform: translateY(-50%);
            color: #aaa; /* Icon color */
        }

        input[type="submit"] {
            background-color: #004643;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #005b5b; /* Lighter shade for hover effect */
        }

        p {
            margin-top: 20px;
            color: #555;
        }

        a {
            color:  #f9bc60; /* Matching color scheme */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="login-container">
        <div class="logo">
            <img src="img/logo.png" alt="Logo"> <!-- Add your logo URL here -->
        </div>

        <div class="form-container">
            <h2>Login</h2>

            <!-- Login Form -->
            <form id="loginForm" action="process_login.php" method="post">
                <div class="input-container">
                    <i class="fas fa-envelope"></i> <!-- Email icon -->
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-container">
                    <i class="fas fa-lock"></i> <!-- Password icon -->
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <input type="submit" value="Login">
            </form>

            <p>Not a Member? <a href="register.php" style="font-weight: bold;">Register here</a>.</p> <!-- Link to register -->

            <?php
            // Show success message if OTP verification was successful
            if (isset($_SESSION['success_message'])) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '" . addslashes($_SESSION['success_message']) . "',
                    });
                </script>";
                unset($_SESSION['success_message']); // Clear the message after showing it
            }

            // Show error message if login failed
            if (isset($_SESSION['login_error'])) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: '" . addslashes($_SESSION['login_error']) . "',
                    });
                </script>";
                unset($_SESSION['login_error']); // Clear error after showing it
            }
            ?>

            <script>
                // Optional: Add JavaScript to handle form submission with SweetAlert
                document.getElementById('loginForm').addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent default form submission
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;

                    // Basic validation
                    if (!email || !password) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Information',
                            text: 'Please fill in both fields!',
                        });
                        return;
                    }

                    // If everything is fine, submit the form
                    this.submit();
                });
            </script>
        </div>
    </div>
</body>
</html>
