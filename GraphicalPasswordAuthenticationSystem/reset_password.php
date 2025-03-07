<?php
session_start();
require 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure OTP was verified before allowing password reset
    if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        echo json_encode(["status" => "error", "message" => "OTP verification required."]);
        exit();
    }

    $username = $_POST['username'];
    $clickedBlocks = $_POST['clickedBlocks'];

    // Validate image file
    if ($_FILES['newImage']['size'] > 2000000) {
        echo json_encode(["status" => "error", "message" => "File size too large."]);
        exit();
    }

    $image = $_FILES['newImage']['tmp_name'];
    if ($image) {
        $imageData = file_get_contents($image);
    } else {
        echo json_encode(["status" => "error", "message" => "Image upload failed."]);
        exit();
    }

    // Convert clicked blocks into a formatted string
    $clickedBlocksArray = json_decode($clickedBlocks, true);
    $blocksString = '';
    foreach ($clickedBlocksArray as $block) {
        $blocksString .= "({$block['row']},{$block['col']})";
    }

    // Hash the new password
    $hashedPassphrase = password_hash($blocksString, PASSWORD_DEFAULT);

    // Update the database with the new image and password
    $stmt = $conn->prepare("UPDATE usersecret SET picture = ?, hash_password = ? WHERE username = ?");
    $stmt->bind_param("sss", $imageData, $hashedPassphrase, $username);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password reset successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to reset password."]);
    }

    $stmt->close();
    $conn->close();

    // Clear OTP session after successful reset
    unset($_SESSION['otp_verified']);
}
?>
