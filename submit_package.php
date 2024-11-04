<?php
session_start();

// Include database connection
include 'db.php';

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_email'])) {
    header("Location: adminlogin.php"); // Redirect to admin login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get package details from the form
    $packageName = $_POST['packageName'];
    $packagePrice = $_POST['packagePrice'];
    $packageType = $_POST['packageType'];
    $contents = $_POST['contents'];

    // Begin a transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Insert the package details into the packages table
        $packageQuery = "INSERT INTO packages (package_name, package_price, package_type) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($packageQuery);
        $stmt->bind_param("sds", $packageName, $packagePrice, $packageType);
        $stmt->execute();

        // Get the last inserted package ID
        $packageId = $stmt->insert_id;

        // Insert the contents into the package_contents table
        $contentQuery = "INSERT INTO package_contents (package_id, content) VALUES (?, ?)";
        $stmtContent = $conn->prepare($contentQuery);

        // Loop through all contents and insert each one with the same package_id
        foreach ($contents as $content) {
            $stmtContent->bind_param("is", $packageId, $content);
            $stmtContent->execute();
        }

        // Commit the transaction if everything is successful
        $conn->commit();

        // Redirect back to the packages page or display a success message
        header("Location: packages.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollback();

        // Handle the error (e.g., log it or show an error message)
        echo "Error: " . $e->getMessage();
    }

    // Close the prepared statements
    $stmt->close();
    $stmtContent->close();
}

// Close the database connection
$conn->close();
?>
