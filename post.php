<?php
    session_start();

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Include your database connection file
    include 'db_connection.php';
// Check if the username and email are set in the session
if (isset($_SESSION['username']) && isset($_SESSION['email'])) {
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
} else {
    // If the username or email is not set, redirect to the login page or handle it accordingly
    header("Location: login.html"); // Change to your login page URL
    exit();
}
    // Get the post ID from URL
    $post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($post_id === 0) {
        die('Invalid post ID.');
    }

    // Fetch the post from the database
    $query = "SELECT p.id, p.username, p.content, p.created_at, p.topic_id, p.forum_id, p.postTitle,
                    t.name AS topic_name, f.name AS forum_name
            FROM posts p
            LEFT JOIN topics t ON p.topic_id = t.id
            LEFT JOIN forums f ON p.forum_id = f.id
            WHERE p.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die('Post not found.');
    }

    $post = $result->fetch_assoc();

    // Fetch replies for the current post
    $query_replies = "SELECT r.id, r.username, r.reply_content, r.created_at
                    FROM replies r
                    WHERE r.post_id = ?";
    $stmt_replies = $conn->prepare($query_replies);
    $stmt_replies->bind_param("i", $post_id);
    $stmt_replies->execute();
    $result_replies = $stmt_replies->get_result();

    // Store replies in an array
    $replies = [];
    while ($reply = $result_replies->fetch_assoc()) {
        $replies[] = $reply;
    }

    // Check if the username and email are set in the session
    if (isset($_SESSION['username']) && isset($_SESSION['email'])) {
        $username = $_SESSION['username'];
        $email = $_SESSION['email'];
    } else {
        // If the username or email is not set, redirect to the login page or handle it accordingly
        header("Location: login.html"); // Change to your login page URL
        exit();
    }


    // Check if the username and email are set in the session
    if (isset($_SESSION['username']) && isset($_SESSION['email'])) {
        $username = $_SESSION['username'];
        $email = $_SESSION['email'];
    } else {
        // If the username or email is not set, redirect to the login page or handle it accordingly
        header("Location: login.html"); // Change to your login page URL
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Include Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Include quill-emoji CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.css" rel="stylesheet">
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
           
    .ql-syntax {
    background-color: #f4f4f4; /* Light gray background */
    border-radius: 4px; /* Rounded corners */
    padding: 5px; /* Padding inside the code block */
    font-family: monospace; /* Monospace font for code */
    display: inline-block; /* Use block display to take full width */
    text-align: left !important; /* Align text to the left */
    white-space: pre-wrap; /* Preserve whitespace and wrap text */
  
}
.ql-editor .ql-image {
    max-width: 100%; /* Ensure images are responsive */
    height: auto; /* Maintain aspect ratio */
    display: block; /* Ensure images behave as block elements */
    margin: 10px 0; /* Add margin around images for spacing */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: Add a subtle box shadow */
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
                overflow-x: hidden;
            }
            header {
                border-bottom: 1px dotted #000;
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
            .popup {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        width: 80%;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1001; /* Ensure it appears above other elements */
    }


    .popup h2 {
        margin-top: 0;
        margin-bottom: 20px;
        border-bottom: 2px dotted #000;
        padding-bottom: 10px;
    }

    .popup textarea {
        width: 100%;
        height: 150px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        box-sizing: border-box;
        margin-bottom: 10px;
        font-family: 'Nothingnormal', sans-serif;
    }

    .popup button {
        background-color: #d71a21;
        color: white;
        border: 1px solid #d71a21;
        padding: 10px 20px;
        border-radius: 50px;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        margin-right: 10px;
    }

    .popup button:hover {
        background-color: white;
        color: #d71a21;
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
                padding-right: 20px;
                padding-left:20px;
            }
            .replies-container {
                padding-right: 20px;
            }
            .content-container h3 {
                margin-bottom: 20px;
            }
          
            .post-container {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 100px;
                width: 100%;
                box-sizing: border-box;
            }
            .post-container h1 {
                margin-top: 0;
            }
            .post-container p {
                text-align: left;
            }
            .post-container small {
                display: block;
                text-align: right;
                color: #888;
            }
            .post-container a {
                display: block;
                text-align: center;
                margin-top: 20px;
            }
            .post-container img {
                max-width: 100%;
                height: auto;
            }
            .post-container .username {
                font-weight: bold;
            }
            .post-container .created_at {
                font-size: 14px;
                color: #777;
            }
            .fullscreen-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.9);
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
            }
            footer {
                border-top: 1px dotted #000;
                background-color: #ffffff;
                color: #1A1110;
                padding: 10px 20px;
                text-align: center;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                z-index: 1000;
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
            .reply-button {
                font-family: 'Nothingnormal', sans-serif;
                font-size: 18px;
                letter-spacing: 1px;
                background-color: #d71a21;
                color: white;
                border: 1px dotted #d71a21;
                padding: 10px 20px;
                border-radius: 50px;
                cursor: pointer;
                transition: background-color 0.3s, color 0.3s;
            }
            .reply-button:hover {
                background-color: white;
                color: #d71a21;
            }
            .discussion-button-container {
                position: fixed;
                bottom: 100px;
                right: 10px;
                z-index: 999;
            }
            li::marker {
                color: #d71a21;
            }
            .forums {
                display: flex;
                flex-direction: column;
            }
            .forum {
                background-color: #ffffff;
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 20px;
                margin-left: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .forum h4 {
                margin-top: 0;
            }
            .forum-link {
                text-decoration: none;
            }
            .forum-link .forum-heading {
                font-family: 'Nothinginspired', sans-serif;
                font-size: 25px;
                color: #1A1110;
                cursor: pointer;
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
      .no-replies-message {
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

  .pre {
    align: left;
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
            .editor {
                display: none;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background-color: #fff;
                border-top: 1px solid #ddd;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                padding: 20px;
                box-sizing: border-box;
            }
            .editor textarea {
                width: 100%;
                height: 100px;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 10px;
                box-sizing: border-box;
            }
            .editor button {
                display: inline-block;
                margin-top: 10px;
                background-color: #d71a21;
                color: white;
                border: 1px dotted #d71a21;
                padding: 10px 20px;
                border-radius: 50px;
                cursor: pointer;
                transition: background-color 0.3s, color 0.3s;
            }
            .editor button:hover {
                background-color: white;
                color: #d71a21;
            }
            .username-link {
    font-family: 'Nothinginspired', sans-serif;
    font-size: 18px;
    color: #d71a21;
    text-decoration: none;
    cursor: pointer;
    /* No need for text-align: left; */
}
.reply {
    margin-right: 20px;
}
.username-link:hover {
    text-decoration: underline;
}
blockquote {
            margin: 5px;
            text-align: left;
            padding: 10px 20px;
            background-color: #efefef;
            border-left: 5px solid #ccc;
            font-style: italic;
        }
        </style>
    </head>
    <body>
        <header>
            <div class="nav-left">
                <a href="index.html">NDOT</a>
            </div>
            <div class="nav-right">
            <a href="test.php">Posts</a>
                <a href="about.php">About</a>      
                <a href="contact.php">Contact</a>
                <a href="forums.php">Forums</a>
                <a href="#" id="welcome-link" onclick="showPopups()">Welcome, <?php echo htmlspecialchars($username); ?></a>
                <!-- Add a hidden popup div -->
                <div class="popup-overlay" id="popupOverlay">
                    <div class="popup-content">
                        <span class="popup-close" onclick="closePopups()">×</span>
                        <!-- Display the user's email -->
                        <p>Email: <?php echo htmlspecialchars($email); ?></p>
                        <!-- Add a visit profile button -->
                        <button onclick="visitProfile()">Visit Profile</button>
                        <!-- Add a logout button -->
                        <button onclick="logout()">Logout</button>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <div class="content-container">
                <h3><?php echo htmlspecialchars($post['forum_name']); ?> - <?php echo htmlspecialchars($post['topic_name']); ?></h3>
                <div class="post-container">
    <div class="post-header">
        
        <h1 class="post-title"><?php echo htmlspecialchars($post['postTitle']); ?></h1>
        <a href="profile.php?user=<?php echo urlencode($post['username']); ?>" class="username-link">
            <?php echo htmlspecialchars($post['username']); ?>
        </a>
    </div>
    <p><?php echo nl2br($post['content']); ?></p>
    <small>Posted on <span class="created_at"><?php echo htmlspecialchars($post['created_at']); ?></span></small>
    <button class="reply-button" onclick="showEditor()">Reply</button>
</div>
            </div>
            <!-- Display replies -->
            <div class="replies-container">
    <h3>Replies</h3>
    <?php if (empty($replies)) : ?>
        <p class="no-replies-message">No replies yet.</p>
    <?php else : ?>
        <?php foreach ($replies as $reply): ?>
            <div class="reply">
                <p><?php echo nl2br(htmlspecialchars($reply['reply_content'])); ?></p>
                <small>Replied by <span class="username"><?php echo htmlspecialchars($reply['username']); ?></span> on <span class="created_at"><?php echo htmlspecialchars($reply['created_at']); ?></span></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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
          line-height: 1.5;">© 2024 Our Community. All rights reserved.</a>
    </footer>

        <div id="popupOverlayReply" class="fullscreen-overlay">
        <div class="popup">
            <h2 style="font-family: 'Nothinginspired', sans-serif; font-size: 24px;">Reply to Post</h2>
            <textarea id="replyText" placeholder="Type your reply here..." style="font-family: 'Nothingnormal', sans-serif;"></textarea>
            <div style="margin-top: 10px;">
                <button onclick="sendReply()" class="reply-button">Send Reply</button>
                <button onclick="hidePopup('popupOverlayReply')" class="reply-button">Cancel</button>
            </div>
        </div>
    </div>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.js"></script>

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
      window.onscroll = function() {
        scrollFunction()
      };
            function showPopups() {
            document.getElementById('popupOverlay').style.display = 'block';
        }

        function closePopups() {
            document.getElementById('popupOverlay').style.display = 'none';
        }

        function visitProfile() {
            window.location.href = 'profile.php';
        }

        function logout() {
            window.location.href = 'logout.php';
        }

            function showPopup(popupId) {
                document.getElementById(popupId).style.display = 'block';
            }

            function hidePopup(popupId) {
                document.getElementById(popupId).style.display = 'none';
            }

            function showEditor() {
                showPopup('popupOverlayReply');
            }

            function sendReply() {
                const replyText = document.getElementById('replyText').value;
                if (replyText.trim() === '') {
                    alert('Reply cannot be empty.');
                    return;
                }

                // Perform AJAX request to send the reply to the server
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'send_reply.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert('Reply sent successfully.');
                        hidePopup('popupOverlayReply');
                    } else {
                        alert('Error sending reply.');
                    }
                };
                xhr.send('post_id=<?php echo $post_id; ?>&reply=' + encodeURIComponent(replyText));
            }

            function showImagePopup(imageSrc) {
                const popupImage = document.getElementById('popupImage');
                popupImage.src = imageSrc;
                showPopup('popupOverlayImage');
            }
            function sendReply() {
        const replyText = document.getElementById('replyText').value;
        if (replyText.trim() === '') {
            alert('Reply cannot be empty.');
            return;
        }

        // Perform AJAX request to send the reply to the server
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'send_reply.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Reply sent successfully.');
                hidePopup('popupOverlayReply');
                // Optional: You can reload the page or update the replies section dynamically here
            } else {
                alert('Error sending reply.');
            }
        };
        xhr.send('post_id=<?php echo $post_id; ?>&reply=' + encodeURIComponent(replyText));
    }

    document.addEventListener('DOMContentLoaded', function() {
            fetch('fetch_discussions.php')
                .then(response => response.json())
                .then(data => {
                    const postsContainer = document.getElementById('posts'); // Assuming you have a container with id='posts'

                    // Clear any existing content inside the posts container
                    postsContainer.innerHTML = '';

                    // Loop through the fetched data and create HTML elements for each post
                    data.forEach(post => {
                        const postContainer = document.createElement('div');
                        postContainer.classList.add('post-container');

                        const usernameElement = document.createElement('h2');
                        usernameElement.textContent = post.username;

                        const postTitleElement = document.createElement('a');
                        postTitleElement.href = 'post.php?id=' + post.id;
                        postTitleElement.textContent = post.postTitle;

                        const forumElement = document.createElement('p');
                        forumElement.textContent = 'Forum: ' + post.forum_name;

                        const contentElement = document.createElement('div');
                        contentElement.classList.add('content');
                        contentElement.textContent = post.content;

                        const timestampElement = document.createElement('div');
                        timestampElement.classList.add('timestamp');
                        timestampElement.textContent = timeAgo(post.created_at);

                        postContainer.appendChild(usernameElement);
                        postContainer.appendChild(postTitleElement);
                        postContainer.appendChild(forumElement);
                        postContainer.appendChild(document.createElement('hr'));
                        postContainer.appendChild(contentElement);
                        postContainer.appendChild(document.createElement('hr'));
                        postContainer.appendChild(timestampElement);

                        postsContainer.appendChild(postContainer);
                    });
                })
                .catch(error => console.error('Error fetching posts:', error));
        });
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': '1' }, { 'header': '2' }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['code-block'],
                    ['link', 'image']
                ]
            }
        });
        </script>
    </body>
    </html>