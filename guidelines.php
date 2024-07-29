<?php
session_start();
$isLoggedIn = isset($_SESSION['username']); // Check if the user is logged in
$username = $isLoggedIn ? $_SESSION['username'] : '';
$email = $isLoggedIn ? $_SESSION['email'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Page</title>
    <style>
        @font-face {
            font-family: 'Nothinginspired';
            src: url('ndot-47-inspired-by-nothing.ttf');
        }
        @font-face {
            font-family: 'Nothingnormal';
            src: url('NType82-Regular.otf');
        }
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #efefef;
            color: #1A1110;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        header {
            border-bottom: 1px dotted #000; /* Creates a dotted line */
            display: inline-block;
            background-color: #ffffff;
            color: #1A1110;
            padding: 0;
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
        h3{
            border-bottom: 2px dotted #000; /* Creates a dotted line */
            display: block; /* Ensures the border only extends as far as the text */
            padding-bottom: 10px; /* Optional: Adjusts the space between the text and the dotted line */
            border-top: 2px dotted #000; /* Creates a dotted line */
            display: block; 
            font-family: 'Nothinginspired', sans-serif;
            font-size: 24px;
            margin: 0 auto;
            font-weight: lighter;
    
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
            padding: 100px 20px 20px; /* Adjust top padding to accommodate header */
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
        }
        .content-container {
            max-width: 50%; /* Adjust the width as needed */
            text-align: justify;
            padding-right: 0px; /* Adds space to the right */
            margin-right: 50px;
        }

        footer {
            border-top: 1px dotted #000; /* Creates a dotted line */
            display: inline-block;
            background-color: #ffffff;
            color: #1A1110;
            padding: 10px 20px;
            text-align: center;
            position:sticky;
            bottom: 0px;
            width: 100%;
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
        .image-container {
            width: 50%; /* Adjust the width as needed */
            position: fixed;
            top: 35px; /* Adjust the top position as needed */
            left: 0;
            bottom: 80px;
            overflow: hidden;
            display: flex;
            justify-content: left;
            align-items: center;
        }
        .image-container img {
            max-width: 100%;
            max-height: 100%;
        }
        p {
            font-family: 'Nothingnormal', sans-serif;
            font-size: 18px;
            line-height: 1.5;
            text-align: justify;
            letter-spacing: 1px; /* Adjusts the gap between letters */
        }
        ul {
            font-family: 'Nothingnormal', sans-serif;
            font-size: 18px;
            line-height: 1.5;
            text-align: justify;
            letter-spacing: 1px; /* Adjusts the gap between letters */
        }

        .get-started {
            font-family: 'Nothingnormal', sans-serif; /* Button font */
            font-size: 18px; /* Button text size */
            letter-spacing: 1px; /* Adjusts the gap between letters */
            background-color: #d71a21; /* Button color */
            color: white; /* Text color */
            border: 1px solid #d71a21; /* Border color */
            padding: 10px 20px; /* Button size */
            border-radius: 50px; /* Makes the button round */
            cursor: pointer; /* Changes the cursor when you hover over the button */
            transition: background-color 0.3s, color 0.3s; /* Smooth transition */
        }
        .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Semi-transparent black background */
        z-index: 1000;
        /* Ensure the popup is above other content */
        display: none;
        /* Initially hidden */
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
        max-height: 80%;
        /* Limit the maximum height of the popup content */
        overflow-y: auto;
        /* Add vertical scrollbar when content exceeds the height */
      }

      .popup-close {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
      }

      /* Adjust the close button style as needed */
      .popup-close:hover {
        color: red;
      }
        .get-started:hover {
            background-color: white; /* Button color when hovered*/
            color: #d71a21; /* Text color when hovered */
        }

        li::marker {
            color: #d71a21;
        }

    </style>
</head>
<body>
    <header>
        <div class="nav-left">
            <a href="#">NERDWANA</a>
        </div>            
        <nav class="nav-right">
        <?php if ($isLoggedIn) : ?>
                <a href="test.php">Posts</a>
                <a href="about.php">About</a>      
                <a href="contact.php">Contact</a>
                <a href="forums.php">Forums</a>
                <a href="#" id="welcome-link" onclick="showPopup()">Welcome, <?php echo htmlspecialchars($username); ?></a>
                <!-- Add a hidden popup div -->
                <div class="popup-overlay" id="popupOverlay">
                    <div class="popup-content">
                        <span class="popup-close" onclick="closePopup()">×</span>
                        <!-- Display the user's email -->
                        <p>Email: <?php echo htmlspecialchars($email); ?></p>
                        <!-- Add a visit profile button -->
                        <button onclick="visitProfile()">Visit Profile</button>
                        <!-- Add a logout button -->
                        <button onclick="logout()">Logout</button>
                    </div>
                </div>
            <?php else : ?>
                <a href="about.php">About</a> 
                <a href="test.php">Posts</a>
                <a href="contact.php">Contact</a>
                <a href="forums.php">Forums</a>
                <a href="signup.html">Signup/Login</a>
            <?php endif; ?>
        
        </nav>
    </header>
   
    <main>
        <div class="image-container">
            <img src="https://cdn.dribbble.com/userupload/12609824/file/original-0ebf1e6c9a64bfe1510cf8d72de99185.gif" alt="Image">
        </div>

        <div class="content-container">
        
            <h3>📜 Community Guidelines 📜</h3>
            <ul> 
                <p><li><strong>Be Respectful:</strong> Treat fellow members with courtesy and respect. We encourage open discussions and debates, but personal attacks, harassment, hate speech, and trolling will not be tolerated.</li></P>
                <p><li><strong>Contribute Positively:</strong> Contribute constructively to discussions. Avoid spamming, excessive self-promotion, or any behavior that disrupts the community's harmony.</li></P>
                <p><li><strong>Keep it Legal:</strong> Do not engage in or promote illegal activities. Respect copyright laws, and avoid sharing pirated or unauthorized content.</li></P>
                <p><li><strong>Maintain Privacy:</strong> Respect the privacy of others. Do not share personal information without consent, and be cautious about sharing sensitive data.</li></P>
                <p><li><strong>Report Violations:</strong> If you encounter any content or behavior that violates these guidelines, report it to the moderators for review. Help us maintain a safe and welcoming environment for all members.</li></P>
                <p><li><strong>Stay On Topic:</strong> Keep discussions relevant to the forum's theme and purpose. Avoid derailing threads with unrelated topics or spam.</li></P>
                <p><li><strong>Use Appropriate Language:</strong> Refrain from using offensive, vulgar, or inappropriate language. Maintain a professional and respectful tone in your interactions.</li></P>
                <p><li><strong>Be Patient:</strong> Be patient and understanding with fellow members, especially newcomers. Everyone is here to learn and contribute, so offer guidance and support when needed.</li></P>
                <p><li><strong>Respect Moderators:</strong> Follow the instructions of moderators and administrators. They are here to ensure the smooth functioning of the community and uphold the guidelines.</li></P>
                <p><li><strong>Keep it Safe:</strong> Do not share or promote harmful or unsafe practices. Prioritize the well-being and safety of yourself and others.</li></P>
            </ul>
        </div>
    </main>

    <footer id="myFooter">
        <div class="social-icons">
            <a href="https://twitter.com/nerdwana_c"><i class="fab fa-twitter"></i></a>
            <a href="https://www.instagram.com/nerdwana.community/"><i class="fab fa-instagram"></i></a>
        </div>
        <a style="font-family: 'Nothingnormal', sans-serif;
            font-size: 18px;
            line-height: 1.5;">© 2024 Our Community. All rights reserved.</a>
    </footer>
    <script>
        function showPopup() {
            document.getElementById('popupOverlay').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
        }

        function visitProfile() {
            window.location.href = 'profile.php';
        }

        function logout() {
            window.location.href = 'logout.php';
        }

        // Get all the navigation links
        var navLinks = document.querySelectorAll('.nav-right a');

        // Function to reset all links to default color
        function resetNavLinksColor() {
            navLinks.forEach(function(link) {
                link.style.color = '#1A1110'; // Black color
            });
        }

        // Add mouseover and mouseout event listeners to each link
        navLinks.forEach(function(link) {
            link.addEventListener('mouseover', function() {
                resetNavLinksColor();
                this.style.color = '#1A1110'; // Black color for hovered link
                navLinks.forEach(function(otherLink) {
                    if (otherLink !== link) {
                        otherLink.style.color = '#B3B2B2'; // Creamish color for other links
                    }
                });
            });

            link.addEventListener('mouseout', resetNavLinksColor);
        });
    </script>
</body>
</html>
