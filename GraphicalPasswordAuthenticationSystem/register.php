<?php
require 'config.php';  // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $clickedBlocks = $_POST['clickedBlocks'];

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM usersecret WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "Username already exists.";
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Process the image
    if ($_FILES['fileInput']['size'] > 2000000) {  // 2MB limit (adjust as needed)
        echo "File size is too large. Please upload a smaller image.";
        exit();
    }
    
    $image = $_FILES['fileInput']['tmp_name'];
    if($image)
    {
        $imageData = file_get_contents($image);
    }
    else
    {
        echo "Image upload failed";
        exit();
    }
    // Decode the clicked blocks from JSON
    $clickedBlocksArray = json_decode($clickedBlocks, true);

    // Create a string representation for the clicked blocks
    $blocksString = '';
    foreach ($clickedBlocksArray as $block) {
        $blocksString .= "({$block['row']},{$block['col']})"; // e.g., (1,2)(3,4)
    };

    // Hash the passphrase
    $hashedPassphrase = password_hash($blocksString, PASSWORD_DEFAULT);

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO usersecret (username,phone,email, picture, hash_password) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $username, $phone, $email, $imageData, $hashedPassphrase);
    
    if ($stmt->execute()) {
        echo "Registration successful!";
    } 
    else {
        error_log("Error executing statement: " . $stmt->error);
        echo "Registration failed. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

