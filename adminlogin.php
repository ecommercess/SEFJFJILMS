<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            position: relative; /* Added to position the logo */
        }
        .logo {
            position: absolute; /* Position the logo */
            top: 10px;
            left: 10px;
            width: 60px; /* Set the width of the logo */
            height: auto; /* Maintain aspect ratio */
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 87%;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .footer {
            margin-top: 15px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>

<div class="login-container">
    <img src="img/logo.png" alt="Company Logo" class="logo"> <!-- Replace 'logo.png' with your actual logo file -->
    <h2>Admin Login</h2>
    <form action="process_admin_login.php" method="post">
        <label for="admin_email">Email:</label>
        <input type="text" id="admin_email" name="admin_email" required>

        <label for="admin_password">Password:</label>
        <input type="password" id="admin_password" name="admin_password" required>

        <input type="submit" value="Login">
    </form>
    <div class="footer">
        <p>&copy; FJFILMS Photography</p>
    </div>
</div>

</body>
</html>
