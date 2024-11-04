<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the file path before deletion
    $sql = "SELECT file_path FROM gallery WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = $row['file_path'];

        // Delete the item from the database
        $sql = "DELETE FROM gallery WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Delete the file from the server
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        header("Location: gallery.php"); // Redirect back to gallery page
        exit();
    } else {
        echo "Item not found.";
    }
}
$conn->close();
?>
