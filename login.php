<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $loginEmail = $_POST['email'];
    $loginPassword = $_POST['password'];


    if (empty($loginEmail) || empty($loginPassword)) {
        echo json_encode(array("status" => "error", "message" => "Please fill in all fields."));
        exit();
    }

  
    $conn = new mysqli('localhost', 'root', '', 'comun');
    // Check connection
    if ($conn->connect_error) {
        echo json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error));
        exit();
    }

    // Prepare SQL statement to fetch user data based on email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $loginEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        if ($loginPassword === $user['password']) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(array("status" => "success"));
            exit();
        }
    }

    echo json_encode(array("status" => "error", "message" => "Invalid email or password."));
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method."));
}
?>
