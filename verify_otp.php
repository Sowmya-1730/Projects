<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];

    // Check if OTP is correct and within expiration time
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiration'])) {
        if ($_SESSION['otp'] == $otp && $_SESSION['otp_expiration'] >= time()) {
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expiration']);
            
            echo json_encode(["status" => "success", "message" => "OTP verified successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid or expired OTP."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "OTP not set."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
