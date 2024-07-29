<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    http_response_code(401);
    echo 'Unauthorized';
    exit();
}

include 'db_connection.php';

$content = $_POST['content'] ?? '';
$username = $_POST['username'] ?? '';
$forum_id = $_POST['forum_id'] ?? null;
$topic_id = $_POST['topic_id'] ?? null;
$postTitle = $_POST['postTitle'] ?? '';

if (empty($content) || empty($username) || empty($forum_id) || empty($topic_id) || empty($postTitle)) {
    http_response_code(400);
    echo 'Content, username, forum ID, topic ID, and post title are required';
    exit();
}

$sql = "INSERT INTO posts (username, content, forum_id, topic_id, postTitle) VALUES (?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssss", $username, $content, $forum_id, $topic_id, $postTitle);
    if ($stmt->execute()) {
        http_response_code(200);
        echo 'Post submitted successfully';
    } else {
        http_response_code(500);
        echo 'Error: ' . $stmt->error;
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo 'Error: ' . $conn->error;
}

$conn->close();
?>
