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
    $packageName = $_POST['packageName'];
    $packagePrice = $_POST['packagePrice'];
    $packageType = $_POST['packageType'];
    $contents = $_POST['contents']; // Array of contents

    // Begin transaction
    $conn->begin_transaction();

    try {
        // 1. Update the package information
        $updatePackageQuery = "UPDATE packages 
                               SET package_name = ?, package_price = ?, package_type = ?
                               WHERE id = ?";
        $stmt = $conn->prepare($updatePackageQuery);
        $stmt->bind_param("sdsi", $packageName, $packagePrice, $packageType, $packageId);
        $stmt->execute();
        $stmt->close();

        // 2. Delete old contents
        $deleteContentsQuery = "DELETE FROM package_contents WHERE package_id = ?";
        $stmt = $conn->prepare($deleteContentsQuery);
        $stmt->bind_param("i", $packageId);
        $stmt->execute();
        $stmt->close();

        // 3. Insert new contents (including any newly added ones)
        $insertContentQuery = "INSERT INTO package_contents (package_id, content) VALUES (?, ?)";
        $stmt = $conn->prepare($insertContentQuery);

        foreach ($contents as $content) {
            if (!empty($content)) { // Only insert non-empty contents
                $stmt->bind_param("is", $packageId, $content);
                $stmt->execute();
            }
        }

        $stmt->close();

        // Commit the transaction
        $conn->commit();

        // Redirect to packages page after success
        header("Location: packages.php");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo "Error updating package: " . $e->getMessage();
    }
}
?>
