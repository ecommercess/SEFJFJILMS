<?php
$password = 'password'; // The plaintext password you want to hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

echo "Hashed Password: " . $hashed_password; // Output the hashed password
?>