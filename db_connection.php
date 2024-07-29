<?php
// db_connection.php

$servername = "localhost";
$dbUsername = "root"; // Your database username
$dbPassword = ""; // Your database password
$dbname = "comun"; // Your database name

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
  