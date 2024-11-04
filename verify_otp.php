<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
        }

        .otp-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #6D4C6C; /* Matching color scheme */
        }

        .otp-inputs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 0 5px; /* Space between inputs */
        }

        input[type="submit"] {
            background-color: #6D4C6C;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #8D6B8E; /* Lighter shade for hover effect */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="otp-container">
    <h2>Verify OTP</h2>
    <form id="otpForm" action="process_verify_otp.php" method="post">
        <div class="otp-inputs">
            <input type="text" id="otp1" class="otp-input" maxlength="1" required>
            <input type="text" id="otp2" class="otp-input" maxlength="1" required>
            <input type="text" id="otp3" class="otp-input" maxlength="1" required>
            <input type="text" id="otp4" class="otp-input" maxlength="1" required>
            <input type="text" id="otp5" class="otp-input" maxlength="1" required>
            <input type="text" id="otp6" class="otp-input" maxlength="1" required>
        </div>
        <input type="hidden" name="otp" id="otp" value="">
        <input type="submit" value="Verify OTP">
    </form>
</div>

<script>
    // Handle input events for OTP fields
    const inputs = document.querySelectorAll('.otp-input');
    const otpInput = document.getElementById('otp');

    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            // Move to the next input when a value is entered
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            // Concatenate all OTP values into a single string
            otpInput.value = Array.from(inputs).map(input => input.value).join('');
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
                // Move to the previous input on backspace
                inputs[index - 1].focus();
            }
        });
    });

    // Optional: Add client-side validation for OTP input
    document.getElementById('otpForm').addEventListener('submit', function(event) {
        if (otpInput.value.length !== 6) {
            event.preventDefault();
            Swal.fire('Error!', 'Please enter a valid 6-digit OTP.', 'error');
        }
    });

   
</script>
</body>
</html>
