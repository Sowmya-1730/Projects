<?php
session_start();
require 'config.php'; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\GPAS\vendor\phpmailer\phpmailer\src\Exception.php';
require 'C:\xampp\htdocs\GPAS\vendor\phpmailer\phpmailer\src\PHPMailer.php';
require 'C:\xampp\htdocs\GPAS\vendor\phpmailer\phpmailer\src\SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));

    // Fetch user email
    $stmt = $conn->prepare("SELECT email FROM usersecret WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($email);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
        date_default_timezone_set('Asia/Kolkata'); // Set timezone
        $otp_expiration = date("Y-m-d H:i:s", time() + 300); // 5 minutes validity

        // Store OTP in the database
        $update_stmt = $conn->prepare("UPDATE usersecret SET otp = ?, otp_expiration = ? WHERE username = ?");
        $update_stmt->bind_param("sss", $otp, $otp_expiration, $username);
        $update_stmt->execute();
        $update_stmt->close();

        // Send OTP via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ashokanneboina55@gmail.com'; // Your Gmail
            $mail->Password = 'izuh zjld util apyg'; // Your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('ashokanneboina55@gmail.com', 'ashok');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Hello,<br><br>Your OTP for password reset is: <b>$otp</b>.<br>This code is valid for 5 minutes.<br><br>Thank you!";

            $mail->send();
            echo json_encode(["status" => "success", "message" => "OTP sent successfully to your email."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Failed to send OTP. Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found."]);
    }

    $stmt->close();
    $conn->close();
}
?>
