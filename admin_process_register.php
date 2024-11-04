<?php
// Database connection
include 'db.php'; // Include the database connection

// Sanitize input data
$fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);

// Check if passwords match
if ($password !== $confirm_password) {
    die("Passwords do not match");
}

// Hash password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle file uploads
$valid_id = $_FILES['valid_id'];
$profile_picture = $_FILES['profile_picture'];

// Define upload directories
$upload_dir_valid_id = 'uploads/valid_ids/';
$upload_dir_profile_picture = 'uploads/profile_pictures/';

// Ensure directories exist
if (!is_dir($upload_dir_valid_id)) {
    mkdir($upload_dir_valid_id, 0777, true);
}
if (!is_dir($upload_dir_profile_picture)) {
    mkdir($upload_dir_profile_picture, 0777, true);
}

// Save the uploaded valid ID
$valid_id_path = $upload_dir_valid_id . basename($valid_id['name']);
if (!move_uploaded_file($valid_id['tmp_name'], $valid_id_path)) {
    die("Error uploading valid ID.");
}

// Save the uploaded profile picture
$profile_picture_path = $upload_dir_profile_picture . basename($profile_picture['name']);
if (!move_uploaded_file($profile_picture['tmp_name'], $profile_picture_path)) {
    die("Error uploading profile picture.");
}

// Insert user data into the database, with status 'pending'
$sql = "INSERT INTO users (fullname, email, password, address, contact_no, valid_id, profile_picture, status)
        VALUES ('$fullname', '$email', '$hashed_password', '$address', '$contact_no', '$valid_id_path', '$profile_picture_path', 'pending')";

if ($conn->query($sql) === TRUE) {
    // Redirect back to the form with user data in the URL (exclude password)
    header("Location: users.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
