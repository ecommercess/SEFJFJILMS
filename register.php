<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
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
            padding-bottom: 100px;
        }

        .registration-container {
            margin-top: 200px;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #004643; /* Matching color scheme */
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-container input,
        .input-container textarea {
            width: 80%;
            padding: 10px 40px; /* Add padding for icons */
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .input-container textarea {
            resize: none; /* Prevent resizing */
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
            background-color: #004643; /* Lighter shade for hover effect */
        }

        p {
            margin-top: 20px;
            color: #555;
        }

        a {
            color: #f9bc60; /* Matching color scheme */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
        .logo {
            margin-bottom: 20px; /* Space between logo and form */
        }

        .logo img {
            max-width: 60px; /* Set a maximum width for the logo */
            height: auto; /* Maintain aspect ratio */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="registration-container">
    <div class="logo">
            <img src="img/logo.png" alt="Logo"> <!-- Add your logo URL here -->
        </div>
        <h2>Register</h2>
        <form id="registrationForm" action="process_register.php" method="post" enctype="multipart/form-data">

            <div class="input-container">
                <i class="fas fa-user"></i> <!-- Full Name icon -->
                <input type="text" id="fullname" name="fullname" placeholder="Full Name" required>
            </div>

            <div class="input-container">
                <i class="fas fa-envelope"></i> <!-- Email icon -->
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-container">
                <i class="fas fa-lock"></i> <!-- Password icon -->
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-container">
                <i class="fas fa-lock"></i> <!-- Confirm Password icon -->
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <div class="input-container">
                <i class="fas fa-home"></i> <!-- Address icon -->
                <textarea id="address" name="address" placeholder="Address" required></textarea>
            </div>

            <div class="input-container">
                <i class="fas fa-phone"></i> <!-- Contact Number icon -->
                <input type="text" id="contact_no" name="contact_no" placeholder="Contact Number" required>
            </div>

            <div class="input-container">
                <i class="fas fa-id-card"></i> <!-- Valid ID icon -->
                <input type="file" id="valid_id" name="valid_id" accept="image/jpeg,image/png" required>
                <label for="valid_id" style="position: absolute; right: 10px; top: 10px; color: #aaa; font-size: 12px;">Valid ID (JPEG/PNG)</label>
            </div>

            <div class="input-container">
                <i class="fas fa-user-circle"></i> <!-- Profile Picture icon -->
                <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png" required>
                <label for="valid_id" style="position: absolute; right: 10px; top: 10px; color: #aaa; font-size: 10px;">Profile Picture (JPEG/PNG)</label>
            </div>

            <input type="submit" value="Register">
        </form>

        <p>Already a member? <a href="login.php" style="font-weight: bold;">Login here</a>.</p>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Check if passwords match
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Passwords do not match!',
                });
                return;
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to create this account?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, create it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    this.submit();
                }
            });
        });
    </script>
</body>
</html>
