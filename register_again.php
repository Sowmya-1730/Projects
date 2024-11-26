<?php
require 'config.php';  // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $username = trim($_POST['username']);

    $clickedBlocks = isset($_POST['clickedBlocks']) ? $_POST['clickedBlocks'] : '';

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM usersecret WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    

    $stmt->close();

    // Process the image
    if ($_FILES['fileInput']['error'] !== UPLOAD_ERR_OK) {
        echo "Error uploading file. Please try again.";
        exit();
    }

    if ($_FILES['fileInput']['size'] > 2000000) {  // 2MB limit (adjust as needed)
        echo "File size is too large. Please upload a smaller image.";
        exit();
    }
    
    $imageMime = mime_content_type($_FILES['fileInput']['tmp_name']);
    if (strpos($imageMime, 'image') === false) {
        echo "Uploaded file is not a valid image.";
        exit();
    }

    $image = $_FILES['fileInput']['tmp_name'];
    $imageData = file_get_contents($image);
    // Decode the clicked blocks from JSON
    $clickedBlocksArray = json_decode($clickedBlocks, true);
    if($clickedBlocksArray === null){
        echo "Invalid clicked blocks data.";
        exit();
    }
    // Create a string representation for the clicked blocks
    $blocksString = '';
    foreach ($clickedBlocksArray as $block) {
        $blocksString .= "({$block['row']},{$block['col']})"; // e.g., (1,2)(3,4)
    };

    // Hash the passphrase
    $hashedPassphrase = password_hash($blocksString, PASSWORD_DEFAULT);

    // Insert into the database
    $stmt = $conn->prepare("UPDATE usersecret SET picture = ?, hash_password = ? WHERE username = ?");
    $stmt->bind_param("sss", $imageData, $hashedPassphrase, $username);


    
    if ($stmt->execute()) {
        echo "Password successfully updated!!";
    } 
    else {
        error_log("Error executing statement: " . $stmt->error);
        echo "Registration failed. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

