<?php
require 'vendor/autoload.php'; // Make sure PHPMailer is installed via Composer
require 'db.php'; // Include your database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'];

    // Generate a random OTP
    $otp = rand(100000, 999999);

    // Create a connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update or insert OTP into the database
    $stmt = $conn->prepare("INSERT INTO users (email, otp, status) VALUES (?, ?, 'pending') ON DUPLICATE KEY UPDATE otp = ?");
    $stmt->bind_param("ssi", $email, $otp, $otp);
    
    if ($stmt->execute()) {
        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'fjfilms71@gmail.com'; // Your email
            $mail->Password = 'spbrvltocucqjieq'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('fjfilms71@gmail.com', 'FJFILMS PHOTOGRAPHY');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = 'Your OTP is <b>' . $otp . '</b>.';

            $mail->send();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save OTP.']);
    }

    $stmt->close();
    $conn->close();
}
?>
