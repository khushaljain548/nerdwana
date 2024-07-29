<?php
// fetch_discussions.php

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'comun');

if ($conn->connect_error) {
    echo json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error));
    exit();
}

$forum_id = isset($_POST['forum_id']) ? intval($_POST['forum_id']) : null;
$topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : null;

$sql = "SELECT p.username, p.content, p.created_at, p.postTitle, p.id, f.forum_content, t.topic_content 
        FROM posts p
        LEFT JOIN forums f ON p.forum_id = f.forum_id
        LEFT JOIN topics t ON p.topic_id = t.topic_id";

$conditions = [];
$params = [];
$param_types = '';

if ($forum_id) {
    $conditions[] = 'p.forum_id = ?';
    $params[] = $forum_id;
    $param_types .= 'i';
}

if ($topic_id) {
    $conditions[] = 'p.topic_id = ?';
    $params[] = $topic_id;
    $param_types .= 'i';
}

if (!empty($conditions)) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY p.created_at DESC';

$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$posts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($posts);
?>
