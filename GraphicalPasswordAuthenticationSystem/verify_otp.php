<?php
session_start();
require 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $otp = trim($_POST['otp']);

    // Fetch OTP and expiry from database
    $stmt = $conn->prepare("SELECT otp, otp_expiration FROM usersecret WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if a record exists before fetching
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_otp, $otp_expiration);
        $stmt->fetch();

        date_default_timezone_set('Asia/Kolkata');
        if ($stored_otp === $otp && time() <= strtotime($otp_expiration)) {
            // OTP is correct and not expired
            $_SESSION['otp_verified'] = true; // This should persist across requests
            
            // Clear OTP from the database to prevent reuse
            $clear_stmt = $conn->prepare("UPDATE usersecret SET otp = NULL, otp_expiration = NULL WHERE username = ?");
            $clear_stmt->bind_param("s", $username);
            $clear_stmt->execute();
            $clear_stmt->close();

            echo json_encode(["status" => "success", "message" => "OTP verified successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid or expired OTP."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found or OTP not set."]);
    }

    $stmt->close();
    $conn->close();
}
?>
