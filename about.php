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
        header {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 20" preserveAspectRatio="none"><path d="M0,10 Q6,0 12,10 T24,10 T36,10 T48,10 T60,10 T72,10" fill="none" stroke="%23D3D3D3" stroke-width="1.5"/></svg>');
    background-size: 72px 10px;
    background-position: bottom;
    background-repeat: repeat-x;
    padding-bottom: 10px;
    background-color: #ffffff;
    color: #1A1110;
    text-align: center;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 10px 20px 20px; /* Adjusted bottom padding */
    box-sizing: border-box;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

footer {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 20" preserveAspectRatio="none"><path d="M0,10 Q6,20 12,10 T24,10 T36,10 T48,10 T60,10 T72,10" fill="none" stroke="%23D3D3D3" stroke-width="1.5"/></svg>');
    background-size: 72px 10px;
    background-position: top;
    background-repeat: repeat-x;
    padding-top: 10px;
    background-color: #ffffff;
    color: #1A1110;
    text-align: center;
    position: sticky;
    bottom: 0;
    width: 100%;
    padding: 20px 20px 10px; /* Adjusted top padding */
    box-sizing: border-box;
}
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #1A1110;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
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
            left: 0px;
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
    border: 1px dotted #d71a21; /* Border color */
    padding: 10px 20px; /* Button size */
    border-radius: 50px; /* Makes the button round */
    font-size: 18px; /* Text size */
    cursor: pointer; /* Changes the cursor when you hover over the button */
    transition: background-color 0.3s, color 0.3s; /* Smooth transition */
}

.get-started:hover {
    background-color: white; /* Button color when hovered*/
    color: #d71a21; /* Text color when hovered */
}
li::marker {

    color: #d71a21;
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
                <a href="guidelines.php">Guidelines</a>      
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
                <a href="test.php">Posts</a>
                <a href="guidelines.php">Guidelines</a>
                <a href="contact.php">Contact</a>
                <a href="forums.php">Forums</a>
                <a href="signup.html">Signup/Login</a>
            <?php endif; ?>
        
        </nav>
    </header>
   
    <main>
    
        <div class="content-container">
            <h3>👋 Hello Tech Enthusiast! 👋</h3>
            <p style="text-align: justify;">Are you passionate about technology? Looking for a place to engage with like-minded individuals? You've come to the right place!</p>
            <p style="text-align: justify;">Welcome to NERDWANA, a vibrant online community where tech enthusiasts come together to share knowledge, discuss the latest trends, and help each other grow. Whether you're a seasoned developer, a curious beginner, or simply someone who loves staying up-to-date on the ever-evolving world of technology, NERDWANA has something for you.</p>
            <p style="text-align: justify;  display: block;">Here's what you can expect from Nerdwana:</p>
            <ul> 
                <p style="text-align: justify;"><li>Ask Questions - Got a burning tech question? Don't hesitate to ask. Our community is filled with knowledgeable folks ready to help. No matter how basic or complex your question may seem, there's always someone here who can offer insights or point you in the right direction. Don't be afraid to break down technical barriers and expand your understanding.</p></li>
                <p style="text-align: justify;"><li> Got some tech wisdom to share? We'd love to hear it. Your insights could help someone else in their tech journey. Whether you're a coding wizard or a master troubleshooter, share your expertise with the community. By contributing your knowledge, you can not only help others but also solidify your own understanding.</p></li>
                <p style="text-align: justify;"><li>Engage in Discussions - Join in on discussions about the latest tech trends. Your opinion matters! NERDWANA is a space for open communication and lively debates. Dive deep into the hottest topics in the tech world, share your perspectives, and learn from others. Whether it's the future of AI, the ethical implications of emerging technologies, or simply the latest gadget releases, there's always something new to discuss and explore.</p></li>
                <p style="text-align: justify;"><li>Build Connections - Find your tech tribe in NERDWANA. Connect with fellow tech enthusiasts, collaborate on projects, and build lasting friendships. NERDWANA goes beyond just information sharing; it's a place to foster a sense of belonging and camaraderie within the tech community. Network with like-minded individuals, learn from their experiences, and grow together on your tech journeys.</li></p>
            </ul>
            <p style="text-align: justify;">Remember, the only silly question is the one not asked. So, dive right in, start exploring, and make the most of Nerdwana!</p>
            <div style="text-align: right;">
                <p style="text-align: right;">Happy Posting! 🚀</p>
                
                <a href="editor.php">
                    <button class="get-started">Get Started Now</button>
                </a>
                
            </div>
        </div>
            <div class="image-container">
            <img src="https://cdn.dribbble.com/userupload/12609824/file/original-0ebf1e6c9a64bfe1510cf8d72de99185.gif" alt="Image">
        </div>
    </main>
    <footer id="myFooter">
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
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
        window.onscroll = function() {scrollFunction()};



    </script>
</body>
</html>

