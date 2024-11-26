<?php
session_start();
require 'config.php'; // Database connection

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\GraphicalPasswordAuthenticationSystem\vendor\phpmailer\phpmailer\src\Exception.php'; // Adjust the path
require 'C:\xampp\htdocs\GraphicalPasswordAuthenticationSystem\vendor\phpmailer\phpmailer\src\PHPMailer.php'; // Adjust the path
require 'C:\xampp\htdocs\GraphicalPasswordAuthenticationSystem\vendor\phpmailer\phpmailer\src\SMTP.php'; // Adjust the path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));

    // Query the user's email based on the username
    $stmt = $conn->prepare("SELECT email FROM usersecret WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($email);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Store OTP in the session for verification
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiration'] = time() + 300; // 5 minutes expiration
        
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ashokanneboina55@gmail.com'; // Your Gmail address
            $mail->Password = 'izuh zjld util apyg'; // Your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('ashokanneboina55@gmail.com', 'ashok'); // Your name
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Hello,<br><br>Your OTP for password reset is: <b>$otp</b>.<br>This code is valid for 5 minutes.<br><br>Thank you!";

            // Send the email
            $mail->send();
            echo json_encode(["status" => "success", "message" => "OTP sent successfully to your email."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

