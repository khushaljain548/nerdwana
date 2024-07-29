<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    die('User not logged in.');
}
$user_id = $_SESSION['user_id'];
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Prepare and execute query to get the current password
    $user_query = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $user_query->bind_param("i", $user_id);

    if ($user_query->execute()) {
        $user_result = $user_query->get_result();
        if ($user_result->num_rows > 0) {
            $user_data = $user_result->fetch_assoc();
            $stored_password = $user_data['password'];

            // Verify current password
            if ($current_password == $stored_password) {
                // Check if new passwords match
                if ($new_password == $confirm_password) {

                    // Update the password in the database
                    $update_query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update_query->bind_param("si", $new_password, $user_id);

                    if ($update_query->execute()) {
                        // Password successfully changed, redirect to profile page
                        header("Location: profile.php");
                        exit();
                    } else {
                        $error_message = "Error updating password.";
                    }
                } else {
                    $error_message = "New passwords do not match.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        } else {
            $error_message = "User not found.";
        }
    } else {
        $error_message = "Error executing query.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Change Password</title>
    <style>
        /* Your existing CSS styles here */
        /* Existing styles from login.html */
        @font-face {
            font-family: 'Nothinginspired';
            src: url('ndot-47-inspired-by-nothing.ttf');
        }
        @font-face {
            font-family: 'Nothingnormal';
            src: url('NType82-Regular.otf');
        }
        body {
            background: rgba(247, 156, 156, 0.42);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            background-image: url("https://cdn.dribbble.com/userupload/5998355/file/original-5ccb76da57dcfa3f005863c2e1c01fb2.gif");
            background-size: cover;
            background-repeat: no-repeat;
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #efefef;
            color: #1A1110;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }
        header {
            border-bottom: 1px dotted #000;
            display: inline-block;
            background-color: #ffffff;
            color: #1A1110;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .nav-left {
            display: flex;
            align-items: center;
            flex: 1;
            margin-left: 20px;
        }
        .nav-left a {
            font-family: 'Nothinginspired', sans-serif;
            font-size: 25px;
            text-decoration: none;
            color: #1A1110;
        }
        h3 {
            border-bottom: 2px dotted #000;
            border-top: 2px dotted #000;
            display: block;
            font-family: 'Nothinginspired', sans-serif;
            font-size: 24px;
            margin: 0 auto;
            font-weight: lighter;
            padding-bottom: 10px;
        }
        .nav-right {
            display: flex;
            gap: 10px;
        }
        .nav-right a {
            text-decoration: none;
            color: #333;
            margin: 0 10px;
            font-family: 'Nothinginspired', sans-serif;
            font-size: 20px;
            transition: color 0.3s ease;
        }
        .nav-right a:hover {
            color: #1A1110;
        }
        main {
            padding: 100px 20px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: calc(100vh - 140px);
            box-sizing: border-box;
            flex: 1;
        }
        .container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            width: 80%;
            max-width: 1000px;
            margin: auto;
            border-radius: 10px;
            overflow: hidden;
        }
        .container h2 {
            font-family: 'Nothingnormal', sans-serif;
        }
        .left-section, .right-section {
            padding: 20px;
            flex: 1;
        }
        .left-section h2 {
            font-family: 'Nothinginspired', sans-serif;
            font-size: 24px;
            margin: 0 auto;
            font-weight: lighter;
            padding-bottom: 10px;
        }
        .right-section {
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .social-icons a:hover {
            transform: scale(1.2);
        }
        .dotted-line {
            border-top: 1px dotted #f00;
            color: #fff;
            background-color: #f00;
            height: 1px;
            width: 50%;
        }
        .container-border {
            border-bottom: 1px dotted #000;
        }

        p, ul {
            font-family: 'Nothingnormal', sans-serif;
            font-size: 18px;
            line-height: 1.5;
            text-align: justify;
            letter-spacing: 1px;
        }
        .get-started {
            font-family: 'Nothingnormal', sans-serif;
            font-size: 18px;
            letter-spacing: 1px;
            background-color: #6495ed;
            color: white;
            border: 1px solid #6495ed;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
        .get-started:hover {
            background-color: white;
            color: #6495ed;
        }
        li::marker {
            color: #6495ed;
        }
        form {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
        }
        label {
            margin: 10px 0 5px;
            font-size: 18px;
            font-family: 'Nothingnormal', sans-serif;
        }
        input {
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border: none; /* Remove the existing border */
            border-top: 1px dotted #000; /* Add a top border */
            border-bottom: 1px dotted #000; /* Add a bottom border */
            font-family: 'Nothingnormal', sans-serif;
            border-radius: 0; /* Ensure no rounded corners */
            outline: none; /* Remove default outline */
            background-color: #ffffff;
        }
        button {
            font-family: 'Nothingnormal', sans-serif; /* Button font */
            font-size: 18px; /* Button text size */
            letter-spacing: 1px; /* Adjusts the gap between letters */
            background-color: #6495ed; /* Button color */
            color: white; /* Text color */
            border: 1px solid #6495ed; /* Border color */
            padding: 10px 20px; /* Button size */
            border-radius: 50px; /* Makes the button round */
            cursor: pointer; /* Changes the cursor when you hover over the button */
            transition: background-color 0.3s, color 0.3s; /* Smooth transition */
        }
        button:hover {
            background-color: white;
            color: #6495ed;
            border: 1px dotted #6495ed;
        }
        .signup-link {
            margin-top: 10px;
            font-family: 'Nothingnormal', sans-serif;
        }
        footer {
            border-top: 1px dotted #000;
            display: inline-block;
            background-color: #ffffff;
            color: #1A1110;
            padding: 10px 20px;
            text-align: center;
            width: 100%;
            margin-top: auto;
        }
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .social-icons a {
            font-size: 20px;
            color: #1A1110;
            transition: transform 0.3s ease;
        }
        .social-icons a:hover {
            transform: scale(1.2);
        }
        .left-section {
            border-right: 1px dotted #d3d3d3;
        }
        /* CSS for the popup */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
            z-index: 1000; /* Ensure the popup is above other content */
            display: none; /* Initially hidden */
        }

        .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            max-width: 80%;
            max-height: 80%; /* Limit the maximum height of the popup content */
            overflow-y: auto; /* Add vertical scrollbar when content exceeds the height */
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .popup-close:hover {
            color: red;
        }
    </style>
</head>
<body>
<header>
        <div class="nav-left">
            <a href="#">NERDWANA</a>
        </div>
        <nav class="nav-right">
            <a href="test.php">Posts</a>
            <a href="about.php">About</a>
            <a href="guidelines.php">Guidelines</a>
            <a href="contact.php">Contact</a>
        </nav>  
    </header>
    <main>
        <div class="container">
            <div class="right-section">
                <h2>Change Password</h2>
                <form id="form" method="POST" action="">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="input" id="current_password" name="current_password" placeholder="Current Password" required>
                    <label for="new_password">New Password</label>
                    <input type="password" class="input" id="new_password" name="new_password" placeholder="New Password" required>
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" class="input" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
                    <button type="submit" class="submit-btn" id="change-password-btn">Change Password</button>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <!-- Your existing footer content -->
    </footer>
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-content">
            <span class="popup-close" onclick="closePopup()">X</span>
            <p id="popupMessage"></p>
        </div>
    </div>
    <script src="change_password.js"></script>
    <script>
        // Show the popup if there is an error message
        document.addEventListener('DOMContentLoaded', (event) => {
            const errorMessage = "<?php echo $error_message; ?>";
            if (errorMessage) {
                document.getElementById('popupMessage').innerText = errorMessage;
                document.getElementById('popupOverlay').style.display = 'block';
            }
        });

        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
        }
    </script>
</body>
</html>
