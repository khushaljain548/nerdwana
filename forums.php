<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// Initialize variables
$forum_id = isset($_GET['forum_id']) ? intval($_GET['forum_id']) : null;
$topic_id = isset($_GET['topic_id']) ? intval($_GET['topic_id']) : null;

// Fetch the posts based on forum_id or topic_id
if ($forum_id) {
    $query = "SELECT p.username, p.content, p.created_at, p.topic_id, p.forum_id
              FROM posts p
              WHERE p.forum_id = $forum_id
              ORDER BY p.created_at DESC";
} elseif ($topic_id) {
    $query = "SELECT p.username, p.content, p.created_at, p.topic_id, p.forum_id
              FROM posts p
              WHERE p.topic_id = $topic_id
              ORDER BY p.created_at DESC";
} else {
    $query = "SELECT p.username, p.content, p.created_at, p.topic_id, p.forum_id
              FROM posts p
              ORDER BY p.created_at DESC";
}

$result = $conn->query($query);

if ($result === false) {
    die('Error: ' . $conn->error);
}

$posts = $result->fetch_all(MYSQLI_ASSOC);

// Fetch topic and forum details for each post
foreach ($posts as &$post) {
    // Fetch topic name
    $topic_query = "SELECT name FROM topics WHERE id = {$post['topic_id']}";
    $topic_result = $conn->query($topic_query);
    if ($topic_result && $topic_result->num_rows > 0) {
        $topic_row = $topic_result->fetch_assoc();
        $post['topic_name'] = $topic_row['name'];
    } else {
        $post['topic_name'] = 'Unknown Topic';
    }

    // Fetch forum name
    $forum_query = "SELECT name FROM forums WHERE id = {$post['forum_id']}";
    $forum_result = $conn->query($forum_query);
    if ($forum_result && $forum_result->num_rows > 0) {
        $forum_row = $forum_result->fetch_assoc();
        $post['forum_name'] = $forum_row['name'];
    } else {
        $post['forum_name'] = 'Unknown Forum';
    }
}
$isLoggedIn=0;
// Check if the username and email are set in the session
if(isset($_SESSION['username']) && isset($_SESSION['email'])) {
    $isLoggedIn = isset($_SESSION['username']);
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
} 


date_default_timezone_set('Asia/Kolkata');

// Function to convert seconds to a human-readable format
function timeAgo($timestamp) {
    $difference = time() - strtotime($timestamp);
  
    if ($difference < 1) {
      return 'just now';
    }
  
    $periods = array(
      'year' => 31536000,
      'month' => 2592000,
      'week' => 604800,
      'day' => 86400,
      'hour' => 3600,
      'minute' => 60,
      'second' => 1,
    );
  
    foreach ($periods as $key => $value) {
      if ($difference >= $value) {
        $count = floor($difference / $value);
        return $count . ' ' . $key . ($count > 1 ? 's' : '') . ' ago';
      }
    }
  
    return 'unknown';
  }

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
            background-color: #FFFFFF;
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
        h3 {
    border-bottom: 2px dotted #000;
    padding-bottom: 10px;
    border-top: 2px dotted #000;
    display: block;
    font-family: 'Nothinginspired', sans-serif;
    font-size: 24px;
    margin: 0 auto;
    font-weight: lighter;
    width: calc(100% - 110px); /* Adjust the width based on your padding values */
    margin-left: auto; /* Align to the right */
    margin-right: auto; /* Align to the right */
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
            display: flex;
            padding: 100px 20px 20px;
            width: 100%;
            justify-content: flex-start;
            align-items: flex-start;
        }
        .content-container {
            flex: 1;
            padding: 0px;
            padding-right: 100px ;
            padding-left:100px;
        }
        .content-container h3 {
    margin-bottom: 20px;
    
}

header {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 20" preserveAspectRatio="none"><path d="M0,10 Q6,0 12,10 T24,10 T36,10 T48,10 T60,10 T72,10" fill="none" stroke="%23D3D3D3" stroke-width="1.5"/></svg>');
    /* Rest of the properties remain the same */
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
    padding: 10px 20px 20px;
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
        p, ul {
            font-family: 'Nothingnormal', sans-serif;
            font-size: 18px;
            line-height: 1.5;
            text-align: justify;
            letter-spacing: 1px;
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
    font-size: 18px; /* Text size */
    cursor: pointer; /* Changes the cursor when you hover over the button */
    transition: background-color 0.3s, color 0.3s; /* Smooth transition */
}
        .get-started:hover {
            background-color: white;
            color: #d71a21;
        }
        .discussion-button-container {
    position: fixed; /* Fixed position relative to the viewport */
    bottom: 100px; /* Adjust this value to move the element above the footer */
    right: 10px; /* 10px from the right edge of the screen */
    z-index: 999; /* Ensure it's above other elements */
}
        li::marker {
            color: #d71a21;
        }
        .forums {
    display: flex;
    flex-direction: column;
    padding: 20px; /* Add padding around the forum container */
    gap: 20px; /* Add space between forum boxes */
    background-color: #FFFFFF; /* Background color for contrast */
    border-radius: 10px; /* Rounded corners for the forum container */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
}

.forum {
    background-color: #ffffff; /* White background for each forum */
    border: 1px solid #ccc; /* Light grey border */
    border-radius: 10px; /* Rounded corners for each forum */
    padding: 20px; /* Inner padding for content */
    margin-bottom: 20px; /* Space between forums */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Light shadow */
}

.forum h4 {
    margin-top: 0; /* Remove default top margin */
    padding-bottom: 10px; /* Space below the title */
    border-bottom: 2px solid #d71a21; /* Bottom border with forum color */
    font-family: 'Nothingnormal', sans-serif; /* Apply custom font */
    font-size: 25px; /* Adjust font size */
    color: #1A1110; /* Text color */
    font-weight: lighter;
}
        .forum-link {
    text-decoration: none; /* Remove underline from the link */
}

        .forum h4:hover {
    color: #d71a21; /* Change color on hover */
        }
/* Existing styles for discussion topics */
.discussion-topics {
    list-style-type: none; /* Remove bullet points */
    padding: 0; /* Remove padding */
    margin-top: 10px; /* Space above topics */
}

.discussion-topics li {
    margin-bottom: 10px; /* Space between topics */
    border-bottom: 1px solid #ccc; /* Line between topics */
    padding: 5px 0; /* Space within each topic */
}

.discussion-topics li a {
    text-decoration: none; /* Remove underline */
    color: #1A1110; /* Text color */
    transition: color 0.3s ease; /* Smooth color transition */
    display: block; /* Block-level link */
    font-family: 'Nothingnormal', sans-serif; /* Apply custom font */
}

.discussion-topics li a:hover {
    color: #d71a21; /* Change color on hover */
}

/* Add styles for forum and discussion container */
.forum-content {
    display: flex; /* Flexbox for layout */
    flex-direction: column; /* Column layout */
    gap: 10px; /* Space between sections */
    padding: 20px; /* Inner padding */
    background-color: #fff; /* Background color */
    border: 1px solid #ccc; /* Border */
    border-radius: 10px; /* Rounded corners */
}

.forum-content h4 {
    font-family: 'Nothinginspired', sans-serif; /* Custom font */
    font-size: 10px; /* Adjust font size */
    color: #1A1110; /* Text color */
}

.forum-content img {
    width: 100%; /* Full width image */
    max-width: 600px; /* Maximum width */
    height: auto; /* Maintain aspect ratio */
    border-radius: 10px; /* Rounded corners */
}

.forum-content .discussion-topics {
    list-style-type: none; /* Remove bullet points */
    padding: 0; /* Remove padding */
}

.forum-content .discussion-topics li {
    margin-bottom: 5px; /* Space between topics */
    padding: 5px; /* Inner padding */
    border-bottom: 1px solid #d71a21; /* Line between topics */
}

.forum-content .discussion-topics li a {
    text-decoration: none; /* Remove underline */
    color: #1A1110; /* Text color */
    font-family: 'Nothingnormal', sans-serif; /* Custom font */
    transition: color 0.3s ease; /* Smooth transition */
}


       
.forum img {
    width: 100%; /* Full width image */
    max-width: 600px; /* Maximum width */
    height: auto; /* Maintain aspect ratio */
    margin-bottom: 10px; /* Space below image */
    border-radius: 5px; /* Rounded corners */
}
        #submit-button {
            display: block;
            margin: 10px 0;
            width: 100px;
            margin-left: auto;
            margin-right: 0;
        }
         /* Your existing CSS styles */
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

        /* Adjust the close button style as needed */
        .popup-close:hover {
            color: red;
        }
        
        .discussion {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}

.discussion .username {
    font-weight: bold;
    margin-bottom: 5px;
}

.discussion .content {
    margin-bottom: 5px;
}

.discussion .created_at {
    font-size: 12px;
    color: #777;
    text-align: right;
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
                <a href="guidelines.php">Guidelines</a>     
                <a href="contact.php">Contact</a>
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
                <a href="about.php">About</a>
                <a href="guidelines.php">Guidelines</a>
                <a href="contact.php">Contact</a>
                <a href="signup.html">Signup/Login</a>
            <?php endif; ?>
        
        </nav>
    </header>
    <main>
    <div class="content-container">
        <h3 class="forum-heading">Forums</h3>
        <div class="forums">
            <div class="forum">
                <a href="test.php?topic_id=1" class="forum-link">
                    <img src="https://cdn.dribbble.com/users/11871569/screenshots/18684853/media/8e07eb37ca68d287356bf97d5943687b.png" alt="Mobile Tech">
                    <h4>Mobile Tech</h4>
                </a>
                <ul class="discussion-topics">
                    <li><a href="test.php?forum_id=1">Android Devices</a></li>
                    <li><a href="test.php?forum_id=2">iOS Devices</a></li>
                </ul>
            </div>
            <div class="forum">
                <a href="test.php?topic_id=2" class="forum-link">
                    <img src="https://cdn.dribbble.com/userupload/10443398/file/original-df0a4c03c4f818cfd7f398f1b67abf9e.gif" alt="AI Development">
                    <h4>AI Development</h4>
                </a>
                <ul class="discussion-topics">
                    <li><a href="test.php?forum_id=3">Machine Learning</a></li>
                    <li><a href="test.php?forum_id=4">AI Applications</a></li>
                </ul>
            </div>
            <div class="forum">
                <a href="test.php?topic_id=3" class="forum-link">
                    <img src="https://cdn.dribbble.com/userupload/5419084/file/original-83073a761a4785d3e68224a582b6bc2f.gif" alt="Everything Else">
                    <h4>Everything Else</h4>
                </a>
                <ul class="discussion-topics">
                    <li><a href="test.php?dorum_id=5">Blogs</a></li>
                    <li><a href="test.php?forum_id=6">Co-Creation</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>


    
            <footer>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
                <a style="font-family: 'Nothingnormal', sans-serif;
          font-size: 18px;
          line-height: 1.5;">© 2024 NERDWANA. All rights reserved.</a>
            </footer>
            <script>
      

// Get all the navigation links
var navLinks = document.querySelectorAll('.nav-right a');
      function searchPosts() {
            const query = document.getElementById('search-input').value;
            if (query.trim() !== '') {
                window.location.href = '?search=' + encodeURIComponent(query);
            }
        }
      // Function to reset all links to default color
      function selectTopic(topicId) {
        window.location.href = "test.php?topic_id=" + topicId;
    }
    function selectForum(forumId) {
        window.location.href = "test.php?forum_id=" + forumId;
    }

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

       // Function to show the popup
       function showPopup() {
            document.getElementById('popupOverlay').style.display = 'block';
        }

        // Function to close the popup
        function closePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
        }

        // Function to logout
        function logout() {
            // Redirect to the logout page
            window.location.href = 'logout.php';
        }
        function visitProfile() {
    // Replace with the actual profile page URL
    window.location.href = 'profile.php';
        }
        // Add event listener to the close button
        document.querySelector('.popup-close').addEventListener('click', closePopup);

        // Add event listener to the welcome message link
        document.getElementById('welcome-link').addEventListener('click', function(event) {
            // Prevent default link behavior
            event.preventDefault();
            // Show the popup
            showPopup();
        });


    </script>
</body>
</html>