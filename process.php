<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(array("status" => "error", "message" => "Please fill in all fields."));
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("status" => "error", "message" => "Invalid email format."));
        exit();
    }

    $conn = new mysqli('localhost', 'root', '', 'comun');
    // Check connection
    if ($conn->connect_error) {
        echo json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error));
        exit();
    }

    $checkStmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(array("status" => "error", "message" => "Username or email already exists."));
        exit();
    }

    $insertStmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $insertStmt->bind_param("sss", $username, $email, $password);

    if ($insertStmt->execute()) {
        echo json_encode(array("status" => "success", "message" => "Registration successful."));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error occurred while registering user."));
    }

    $checkStmt->close();
    $insertStmt->close();
    $conn->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method."));
}
?>
