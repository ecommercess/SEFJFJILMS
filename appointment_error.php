<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo addslashes($_SESSION['error_message']); ?>',
            confirmButtonText: 'OK'
        }).then(() => {
            // Optionally redirect or take action after alert
            window.location.href = 'appointment_form.php'; // Redirect to the main page or another page
        });
    </script>
</body>
</html>
<?php
unset($_SESSION['error_message']); // Clear the message after displaying
?>
