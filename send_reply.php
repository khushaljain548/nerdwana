<?php
session_start();

// Include your database connection file
include 'db_connection.php';

// Get data from POST request
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$reply_content = isset($_POST['reply']) ? $_POST['reply'] : '';

// Validate input
if ($post_id === 0 || empty($reply_content)) {
    die('Invalid data.');
}

// Get username from session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    die('User not logged in.');
}

// Insert reply into database
$query = "INSERT INTO replies (post_id, username, reply_content, created_at) VALUES (?, ?, ?, current_timestamp())";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $post_id, $username, $reply_content);

if ($stmt->execute()) {
    echo 'Reply added successfully.';
} else {
    echo 'Error adding reply.';
}

$stmt->close();
$conn->close();
?>
