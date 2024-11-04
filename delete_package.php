<?php
session_start();
include 'db.php'; // Include your database connection

// Ensure the user is an admin and logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $packageId = $_POST['packageId'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // 1. Delete contents associated with the package
        $deleteContentsQuery = "DELETE FROM package_contents WHERE package_id = ?";
        $stmt = $conn->prepare($deleteContentsQuery);
        $stmt->bind_param("i", $packageId);
        $stmt->execute();
        $stmt->close();

        // 2. Delete the package itself
        $deletePackageQuery = "DELETE FROM packages WHERE id = ?";
        $stmt = $conn->prepare($deletePackageQuery);
        $stmt->bind_param("i", $packageId);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        // Send response back (for AJAX)
        echo "success";

    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo "Error deleting package: " . $e->getMessage();
    }
}
?>
