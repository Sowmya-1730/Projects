<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust the path based on your setup

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();                                          // Set mailer to use SMTP
    $mail->Host       = 'smtp.gmail.com';                   // Specify main SMTP server
    $mail->SMTPAuth   = true;                               // Enable SMTP authentication
    $mail->Username   = 'ashokanneboina55@gmail.com';             // Your Gmail address
    $mail->Password   = 'izuh zjld util apyg';                // Your App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    // Enable TLS encryption
    $mail->Port       = 587;                                // TCP port to connect to

    // Recipients
    $mail->setFrom('ashokanneboina55@gmail.com', 'Your Name');
    $mail->addAddress('ash929487@gmail.com');               // Add a recipient

    // Content
    $mail->isHTML(true);                                    // Set email format to HTML
    $mail->Subject = 'My Subject';
    $mail->Body    = nl2br("First line of text\nSecond line of text"); // Use nl2br to convert new lines to HTML line breaks
    $mail->AltBody = "First line of text\nSecond line of text"; // Non-HTML alternative body

    // Send the email
    $mail->send();
    echo 'Email has been sent successfully.';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
