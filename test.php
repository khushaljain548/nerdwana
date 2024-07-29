<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection file
include 'db_connection.php';

// Handle topic selection (using AJAX or server-side redirection)
$selectedTopicId = null; // Initialize to null
if (isset($_GET['topic_id'])) {
  $selectedTopicId = (int) $_GET['topic_id']; // Sanitize input as integer
}

$selectedForumId = null; // Initialize to null
if (isset($_GET['forum_id'])) {
  $selectedForumId = (int) $_GET['forum_id']; // Sanitize input as integer
}

$searchQuery = null;
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Fetch posts based on the selected topic (or all posts if none is selected)
$query = "SELECT p.id, p.username, p.content, p.created_at, p.topic_id, p.forum_id, p.postTitle
         FROM posts p";

if ($searchQuery !== null) {
  $searchQuery = $conn->real_escape_string($searchQuery);
  $query .= " WHERE p.content LIKE '%$searchQuery%' OR p.postTitle LIKE '%$searchQuery%'";
  
}

if($selectedTopicId !== null){
  $query .= " WHERE p.forum_id = $selectedTopicId";
}
if ($selectedForumId!== null){
  $query .= " WHERE p.topic_id = $selectedForumId";
}


$query .= " ORDER BY p.created_at DESC";

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

// Check if the username and email are set in the session
$isLoggedIn=0;
if(isset($_SESSION['username']) && isset($_SESSION['email'])) {
  $isLoggedIn = isset($_SESSION['username']);
  $username = $_SESSION['username'];
  $email = $_SESSION['email'];
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

      h3 {
        border-bottom: 2px dotted #000;
        padding-bottom: 10px;
        border-top: 2px dotted #000;
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
        color: #1A1110;
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
        padding-right: 20px;
      }
      .content-container h3 {
        margin-bottom: 20px;
      }

      .no-posts-message {
            text-align: center;
            margin: 20px auto;
            padding: 10px;
            max-width: 600px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 16px;
            color: #555;
        }

        #posts-container {
            margin: 0 auto;
            padding: 20px;
        }

        .post-container {
            margin-bottom: 20px;
        }
      
      .post-container {
    background-color: #ffffff;
    margin-bottom: 20px;
    margin-right: 20px;
    padding: 20px;
    width: 1000px;
    position: relative;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-top: 1px solid #4682B4;
    border-bottom: 1px solid #4682B4;
    transition: background-color 0.3s ease; /* Add smooth transition */
    cursor: pointer; /* Add pointer cursor */
}

/* Add this new rule for hover effect */
.post-container:hover {
    background-color: #efefef; /* Change to pure white on hover */
    border: 2px dotted #4682B4;
}

.post-container::before,
.post-container::after {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    width: 10px;
    background-size: 10px 10px;
    background-repeat: repeat-y;
}

.post-container::before {
    left: -5px;
    background-image: 
        radial-gradient(circle at 0 50%, transparent 0, transparent 3px, #4682b4 3px, #4682b4 5px, transparent 5px);
}

.post-container::after {
    right: -5px;
    background-image: 
        radial-gradient(circle at 100% 50%, transparent 0, transparent 3px, #4682b4 3px, #4682b4 5px, transparent 5px);
}
.post-container a {
    text-decoration: none;
    color: inherit;
}
    .post-container h2 {
        margin-top: 0;
        font-size: 18px;
      }

      .post-container .content {
        text-align: center;
        font-size: 16px;
      }

      .post-container .timestamp {
        text-align: right;
        color: #888;
        font-size: 14px;
      }

      .post-container hr {
        margin: 10px 0;
        border: none;
        border-top: 1px dotted #4682b4;
      }

      .post-container img {
        max-width: 500px;
        /* Set a specific width */
        max-height: 500px;
        /* Set a specific height */
        width: auto;
        height: auto;
        display: block;
        margin: 0 auto;
      }

      .fullscreen-overlay {
        display: none;
        /* Initially hidden */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        /* Semi-transparent background */
        justify-content: center;
        align-items: center;
        z-index: 1000;
      }

      .fullscreen-overlay img {
        max-width: 90%;
        max-height: 90%;
      }

      .fullscreen-overlay:target {
        display: flex;
        /* Show overlay when targeted */
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

      p,
      ul {
        font-family: 'Nothingnormal', sans-serif;
        font-size: 18px;
        line-height: 1.5;
        text-align: justify;
        letter-spacing: 1px;
      }

      .get-started {
        font-family: 'Nothingnormal', sans-serif;
        /* Button font */
        font-size: 18px;
        /* Button text size */
        letter-spacing: 1px;
        /* Adjusts the gap between letters */
        background-color: #d71a21;
        /* Button color */
        color: white;
        /* Text color */
        border: 1px dotted #d71a21;
        /* Border color */
        padding: 10px 20px;
        /* Button size */
        border-radius: 50px;
        /* Makes the button round */
        font-size: 18px;
        /* Text size */
        cursor: pointer;
        /* Changes the cursor when you hover over the button */
        transition: background-color 0.3s, color 0.3s;
        /* Smooth transition */
      }

      .get-started:hover {
        background-color: white;
        color: #d71a21;
      }

      .discussion-button-container {
        position: fixed;
        /* Fixed position relative to the viewport */
        bottom: 100px;
        /* Adjust this value to move the element above the footer */
        right: 10px;
        /* 10px from the right edge of the screen */
        z-index: 999;
        /* Ensure it's above other elements */
      }

      li::marker {
        color: #d71a21;
      }

      .forums {
        display: flex;
        flex-direction: column;
        margin-top: 20px;
        
      }

      .forum {
        background-color: #efefef;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 20px;
        margin-left: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        /* Set to 100% to ensure it takes the full width */
      }

      .forum h4 {
        margin-top: 0;
      }

      .forum-link {
        text-decoration: none;
        /* Remove underline from the link */
      }

      .forum-link .forum-heading {
        font-family: 'Nothinginspired', sans-serif;
        font-size: 25px;
        text-decoration: none;
        color: #1A1110;
        cursor: pointer;
        /* Change cursor to pointer */
      }

      .discussion-topics {
        list-style-type: none;
        padding: 0;
      }

      .discussion-topics li {
        margin-bottom: 5px;
      }

      .discussion-topics li a {
        text-decoration: none;
        color: #333;
        transition: color 0.3s ease;
      }

      .discussion-topics li a:hover {
        color: #d71a21;
      }
      .forum a {
    text-decoration: none; /* Removes underline from the link */
    color: inherit; /* Inherits the color from the <h4> */
}

/* Ensure the <h4> itself doesn't have a different color or decoration */
.forum a h4 {
    color: #333; /* Sets default color */
    transition: color 0.3s ease; /* Adds smooth transition */
    font-family: 'NothingNormal', sans-serif; /* Apply local font */
    font-size: 20px; /* Match font size */
    font-weight: normal;
    margin: 0; /* Remove extra margin */
}

/* Hover effect for forum names */
.forum a:hover h4 {
    color: #d71a21; /* Change color on hover */
    text-decoration: none; /* Ensures no underline on hover */
    cursor: pointer; /* Pointer cursor on hover */
}

   

      .forum img {
        width: 100%;
        max-width: 300px;
        /* Adjust this value as necessary */
        height: auto;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
      }

   

  
      /* Your existing CSS styles */
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

      <style>#search-form {
        font-family: 'Nothingnormal', sans-serif;
        /* Button font */
        font-size: 18px;
        /* Button text size */
        align-self: center;
        margin-right: 10px;
        margin-left: 10px;
        border: 1px solid #4682b4;
        /* Border color */
        padding: 10px 20px;
        border-radius: 50px;
        /* Make sure to match the border-radius with the form */
      }

      #search-input {
        font-family: 'Nothingnormal', sans-serif;
        /* Button font */
        font-size: 18px;
        /* Button text size */
        align-self: center;
        margin-right: 10px;
        margin-left: 10px;
        border: 1px solid #4682b4;
        /* Border color */
        padding: 10px 20px;
        border-radius: 50px;
        /* Make sure to match the border-radius with the form */
      }

      #search-button {
        font-family: 'Nothingnormal', sans-serif;
        /* Button font */
        font-size: 18px;
        /* Button text size */
        letter-spacing: 1px;
        /* Adjusts the gap between letters */
        background-color: #4682b4;
        /* Button color */
        color: white;
        /* Text color */
        border: 1px solid #4682b4;
        /* Border color */
        padding: 10px 20px;
        /* Button size */
        border-radius: 50px;
        /* Makes the button round */
        cursor: pointer;
        /* Changes the cursor when you hover over the button */
        transition: background-color 0.3s, color 0.3s;
        /* Smooth transition */
        float: right
      }

      #search-button:hover {
        background-color: white;
        /* Button color when hovered*/
        color: #4682b4;
        /* Text color when hovered */
        border: 1px dotted #4682b4;
      }
      .content .one-line {
        white-space: nowrap;         /* Prevents text from wrapping */
        overflow: hidden;            /* Ensures overflow is hidden */
        text-overflow: ellipsis;     /* Adds ellipsis for overflowing text */
        max-width: 100%;             /* Adjust max-width to fit your layout */
        display: block;              /* Ensures it is a block element */
        margin: 0;                   /* Reset margin */
    }
    .content {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .timestamp {
        font-size: 0.9em;
        color: #777;
    }

    </style>

   
    </style>
    <script>
    
  </script>
  </head>
  <body>
    <header>
      <div class="nav-left">
        <a href="#">NERDWANA</a>
        <!-- Add the search form -->
        <form id="search-form">
          <input type="text" id="search-input" placeholder="🔎 | Search posts...">
          <button type="submit" id="search-button" onclick="searchPosts()">Search</button>
        </form>
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
                <a href="test.php">Posts</a>
                <a href="about.php">About</a> 
                <a href="contact.php">Contact</a>
                <a href="forums.php">Forums</a>
                <a href="signup.html">Signup/Login</a>
            <?php endif; ?>
        
        </nav>
    </header>
    <main>
      <div class="content-container">
        <a href="forums.php" class="forum-link">
          <h3 class="forum-heading">Forums</h3>
        </a>
        <div class="forums">
          <div class="forum">
          <a href="#" onclick="selectTopic(1)" >
              <!-- Add this line -->
              <img src="https://cdn.dribbble.com/users/11871569/screenshots/18684853/media/8e07eb37ca68d287356bf97d5943687b.png" alt="Mobile Tech">
              <h4>Mobile Tech</h4>
            </a>
            <!-- Add this line -->
            <ul class="discussion-topics">
              <li>
                <a href="#" onclick="selectForum(1)">Android Devices</a>
              </li>
              <li>
                <a href="#" onclick="selectForum(2)">iOS Devices</a>
              </li>
            </ul>
          </div>
          <div class="forum">
          <a href="#" onclick="selectTopic(2)" >
              <!-- Add this line -->
              <img src="https://cdn.dribbble.com/userupload/10443398/file/original-df0a4c03c4f818cfd7f398f1b67abf9e.gif" alt="Mobile Tech">
              <h4>AI Development</h4>
            </a>
            <!-- Add this line -->
            <ul class="discussion-topics">
              <li>
                <a href="#" onclick="selectForum(3)">Machine Learning</a>
              </li>
              <li>
                <a href="#" onclick="selectForum(4)">AI Applications</a>
              </li>
            </ul>
          </div>
          <div class="forum">
          <a href="#" onclick="selectTopic(3)" >
                <img src="https://cdn.dribbble.com/userupload/5419084/file/original-83073a761a4785d3e68224a582b6bc2f.gif" alt="Mobile Tech">
                <h4>Everything Else</h4>
            </a>
              <!-- Add this line -->
              <ul class="discussion-topics">
                <li>
                  <a href="#" onclick="selectForum(5)">Blogs</a>
                </li>
                <li>
                  <a href="#" onclick="selectForum(6)">Co-Creation</a>
                </li>
              </ul>
          </div>
        </div>
      </div>
      <div>
            <h3>Posts</h3>
            <div id="posts-container">
        <br>
        <?php if (empty($posts)) : ?>
            <p class="no-posts-message">No posts found.</p>
    <?php else : ?>
        <?php foreach ($posts as $post) : ?>
          <div class="post-container" data-post-id="<?php echo $post['id']; ?>">
    <div class="post-content">
        <p class="username">
            <a href="profile.php?user=<?php echo urlencode($post['username']); ?>" class="user-link">
                <?php echo htmlspecialchars($post['username']); ?>
            </a>
        </p>
        <p class="post-title">
            <?php echo htmlspecialchars($post['postTitle']); ?>
        </p>
        <p>Forum: <?php echo htmlspecialchars($post['forum_name']); ?> - Topic: <?php echo htmlspecialchars($post['topic_name']); ?></p>
        <hr>
        <div class="content">
            <div class="one-line">
                <?php 
                $stripped_content = strip_tags($post['content']);
                echo substr($stripped_content, 0, 100);
                if (strlen($stripped_content) > 100) {
                    echo '...';
                }
                ?>
            </div>
        </div>
        <hr>
        <div class="timestamp">
            <?php echo timeAgo($post['created_at']); ?>
        </div>
    </div>
</div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>
      <div class="discussion-button-container">
        <a href="editor.php">
          <button class="get-started">Start a Discussion</button>
        </a>
      </div>
    </main>
    <footer>
      <div class="social-icons">
        
        <a href="https://x.com/nerdwana_c">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="https://www.instagram.com/nerdwana.community/">
          <i class="fab fa-instagram"></i>
        </a>
      </div>
      <a style="font-family: 'Nothingnormal', sans-serif;
          font-size: 18px;
          line-height: 1.5;">© 2024 NERDWANA. All rights reserved.</a>
    </footer>
    <div id="fullscreen-overlay" class="fullscreen-overlay">
      <img src="" alt="Full screen image">
    </div>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.js"></script>
    <script>
      document.querySelectorAll('.post-container img').forEach(img => {
        img.addEventListener('click', () => {
          const overlay = document.getElementById('fullscreen-overlay');
          const overlayImg = overlay.querySelector('img');
          overlayImg.src = img.src; // Set the overlay image source to the clicked image source
          overlay.style.display = 'flex'; // Show the overlay
        });
      });
      document.getElementById('fullscreen-overlay').addEventListener('click', () => {
        document.getElementById('fullscreen-overlay').style.display = 'none'; // Hide the overlay on click
      });
      document.querySelectorAll('.post-container img').forEach(img => {
        img.addEventListener('click', () => {
          const overlay = document.getElementById('fullscreen-overlay');
          const overlayImg = overlay.querySelector('img');
          overlayImg.src = img.src; // Set the overlay image source to the clicked image source
          overlay.style.display = 'flex'; // Show the overlay
        });
      });
      document.getElementById('fullscreen-overlay').addEventListener('click', () => {
        document.getElementById('fullscreen-overlay').style.display = 'none'; // Hide the overlay on click
      });
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
      window.onscroll = function() {
        scrollFunction()
      };
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
      fetch('fetch_discussions.php').then(response => response.json()).then(data => {
        const postsContainer = document.getElementById('posts');
        // Clear any existing content inside the posts container
        postsContainer.innerHTML = '';
        // Loop through the fetched data and create HTML elements for each post
        data.forEach(post => {
          const postDiv = document.createElement('div');
          postDiv.classList.add('post');
          // Create elements for username, content, and created_at
          const usernameDiv = document.createElement('div');
          usernameDiv.textContent = 'Username: ' + post.username;
          const contentDiv = document.createElement('div');
          contentDiv.textContent = 'Content: ' + post.content;
          const createdAtDiv = document.createElement('div');
          createdAtDiv.textContent = 'Created At: ' + post.created_at;
          // Append username, content, and created_at elements to the post container
          postDiv.appendChild(usernameDiv);
          postDiv.appendChild(contentDiv);
          postDiv.appendChild(createdAtDiv);
          // Append the post container to the posts container
          postsContainer.appendChild(postDiv);
        });
      }).catch(error => console.error('Error fetching posts:', error));
      document.getElementById('search-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission behavior
        // Get the search query from the input field
        const searchQuery = document.getElementById('search-input').value.trim();
        // Fetch search results from the server
        fetch(`search.php?query=${encodeURIComponent(searchQuery)}`).then(response => response.json()).then(data => {
          // Process the search results (you can render them however you want)
          console.log('Search results:', data);
        }).catch(error => console.error('Error searching posts:', error));
      });
      document.addEventListener('DOMContentLoaded', function() {
        const postContainers = document.querySelectorAll('.post-container');
        
        postContainers.forEach(container => {
            container.addEventListener('click', function() {
                const postId = this.getAttribute('data-post-id');
                window.location.href = `post.php?id=${postId}`;
            });
        });
    });
    </script>
  </body>
</html>