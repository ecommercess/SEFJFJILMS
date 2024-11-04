<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .header { display: flex; justify-content: center; align-items: center; padding: 20px; }
        .logo { width: 50px; height: 50px; margin-right: 10px; }
        .form-container { width: 300px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .form-group { margin: 10px 0; }
        .btn-submit { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;}
        .btn-back { background: none; border: none; cursor: pointer; margin-top: 10px; font-size: 24px; color: #f44336; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="header">
    <img src="img/logo.png" alt="Logo" class="logo">
    <h1>FJFilms Photography</h1>
</div>
<button class="btn-back" onclick="window.location.href='index.php';">
    <i class="fas fa-arrow-left"></i>
</button>

<div class="form-container">

    <h2>Make an Appointment</h2>
    <form id="appointmentForm" action="submit_appointment.php" method="POST">
        <div class="form-group">
            <label for="appointment_date">Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required>
        </div>
        <div class="form-group">
            <label for="appointment_time">Time:</label>
            <select id="appointment_time" name="appointment_time" required>
                <option value="08:00:00">8:00 AM</option>
                <option value="09:00:00">9:00 AM</option>
                <option value="10:00:00">10:00 AM</option>
                <option value="11:00:00">11:00 AM</option>
                <option value="13:00:00">1:00 PM</option>
                <option value="14:00:00">2:00 PM</option>
                <option value="15:00:00">3:00 PM</option>
                <option value="16:00:00">4:00 PM</option>
            </select>
        </div>
        <button type="submit" class="btn-submit">Submit Appointment</button>
    </form>

</div>

<script>
    document.getElementById('appointmentForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Show confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to submit this appointment?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!'
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
