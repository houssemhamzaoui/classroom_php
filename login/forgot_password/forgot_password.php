<?php
require 'C:\xampp\htdocs\projet_web\vendor\autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit;
    }

    // Database connection
    $conn = new mysqli('localhost', 'root', 'hosshoss', 'projet');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM login WHERE email = ? and validated=?");
    $validated = 1;
    $stmt->bind_param("si", $email, $validated);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "No account found with that email";
        exit;
    }

    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Save token to the database
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $token, $expires_at);
    $stmt->execute();

    $reset_link = "http://localhost/projet_web/login/forgot_password/reset_password.php?token=" . $token;

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 's3if2003@gmail.com'; // Your Gmail address
        $mail->Password = 'tnik ueny eomv zpiy'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('s3if2003@gmail.com', 'classroom');
        $mail->addAddress($email);
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "Click the link below to reset your password:<br><a href='$reset_link'>$reset_link</a>";

        $mail->send();
        echo "Password reset link sent to your email.";
    } catch (Exception $e) {
        echo "Failed to send email. Error: {$mail->ErrorInfo}";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .container {
            max-width: 500px;
            margin-top: 100px;
        }
        .form-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Password Reset</h2>
        <form method="POST" id="resetForm">
            <div class="mb-3">
                <label for="email" class="form-label">Enter your email address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
        
        <div class="alert alert-success mt-3" role="alert">
            Password reset link sent to your email.
        </div>
        <div class="alert alert-danger mt-3" role="alert">
            Failed to send password reset email. Please try again.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const form = document.getElementById('resetForm');
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const email = document.getElementById('email').value;
        
        fetch('', {
            method: 'POST',
            body: new URLSearchParams({
                'email': email,
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(response => response.text())
        .then(data => {
            const successAlert = document.querySelector('.alert-success');
            const errorAlert = document.querySelector('.alert-danger');

            if (data.includes("Password reset link sent to your email")) {
                successAlert.style.display = 'block';
                errorAlert.style.display = 'none';
            } else {
                errorAlert.style.display = 'block';
                successAlert.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>

</body>
</html>
