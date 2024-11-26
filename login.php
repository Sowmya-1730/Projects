<?php
session_start();
require 'config.php';  // Database connection

header('Content-Type: application/json');

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        // Step 1: Username Check
        $username = $_POST['username'];

        // Check if the username exists in the database
        $stmt = $conn->prepare("SELECT picture FROM usersecret WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Username exists, fetch the image data
            $stmt->bind_result($imageData);
            $stmt->fetch();
            
            // Store the username and image in session
            $_SESSION['username'] = $username;
            $_SESSION['imageData'] = base64_encode($imageData);  // Store image for later use

            echo json_encode(['imageData' => $_SESSION['imageData']]);
        } else {
            // Username doesn't exist
            echo json_encode(['message' => 'User not found.']);
        }
        $stmt->close();
    } 
    elseif (isset($_POST['clickedBlocks'])) {
        // Step 2: Graphical Authentication (Handle the grid block verification)
        if (!isset($_SESSION['username'])) {
            echo json_encode(['status' => 'error', 'message' => 'Session expired.']);
            exit();
        }

        $username = $_SESSION['username'];
        $clickedBlocks = json_decode($_POST['clickedBlocks'], true);

        // Convert the clicked blocks back to a string like in registration
        $blocksString = '';
        foreach ($clickedBlocks as $block) {
            $blocksString .= "({$block['row']},{$block['col']})";
        }

        // Fetch the user's hashed grid password from the database
        $stmt = $conn->prepare("SELECT hash_password FROM usersecret WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($storedHash);
        $stmt->fetch();

        // Verify the password
        if (password_verify($blocksString, $storedHash)) {
            // Log in the user
            $_SESSION['logged_in'] = true;

            echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
            exit(); 
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
        }

        $stmt->close();
    }

    $conn->close();
} 
?>
