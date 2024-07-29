<?php
session_start(); // Start the session

// Include the database connection file
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

// Fetch forums
$forums_result = $conn->query("SELECT id, name FROM forums");
if (!$forums_result) {
    die("Error fetching forums: " . $conn->error);
}
$forums = $forums_result->fetch_all(MYSQLI_ASSOC);

// Fetch topics
$topics_result = $conn->query("SELECT id, forum_id, name FROM topics");
if (!$topics_result) {
    die("Error fetching topics: " . $conn->error);
}
$topics = $topics_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start a Discussion</title>
    <!-- Include Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Include quill-emoji CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.css" rel="stylesheet">
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
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    color: #333;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
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
        .nav h3 {
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
    width: 100%;
    padding: 80px 20px 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    flex-direction: column;
    max-width: 1200px;
}

h3 {
    font-family: 'Nothinginspired', sans-serif;
    font-size: 28px;
    color: #454545;
    margin-bottom: 20px;
    text-align: center;
    width: 100%;
}

.content-container {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    width: 100%;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.dropdown-container {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

.dropdown-container label {
    font-family: 'Nothingnormal', sans-serif;
    font-size: 16px;
    margin-bottom: 5px;
}

.dropdown-container select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-family: 'Nothingnormal', sans-serif;
    font-size: 16px;
    margin-bottom: 10px;
}

.title-input {
    margin-bottom: 20px;
}

.title-input input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-family: 'Nothingnormal', sans-serif;
    font-size: 16px;
}

#editor {
    position: relative;
}

.button-like {
    background-color: #d71a21;
    color: white;
    border: 1px solid #d71a21;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-family: 'Nothingnormal', sans-serif;
    transition: background-color 0.3s, color 0.3s;
}

.button-like:hover {
    background-color: white;
    color: #d71a21;
}

#toolbar {
    background: #f1f1f1;
    border: 1px solid #ccc;
    border-radius: 5px 5px 0 0;
    padding: 5px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
}

#toolbar button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    margin-right: 5px;
}

#toolbar button:hover {
    color: #d71a21;
}

#editorContainer {
    border: 1px solid #ccc;
    border-radius: 0 0 5px 5px;
    min-height: 200px;
    padding: 10px;
    font-family: 'Nothingnormal', sans-serif;
    max-height: 300px;
    overflow-y: auto;
}

.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: none;
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
    overflow-y: auto;
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


.social-icons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 10px;
}

.social-icons a {
    font-size: 20px;
    color: #333;
    transition: transform 0.3s ease;
}

.social-icons a:hover {
    transform: scale(1.2);
    color: #d71a21;
}

p, ul {
    font-family: 'Nothingnormal', sans-serif;
    font-size: 18px;
    line-height: 1.5;
    text-align: justify;
    letter-spacing: 1px;
}

.ql-editor {
    font-family: 'Nothingnormal', sans-serif;
}
.ql-syntax {
            background-color: #f4f4f4; /* Light gray background */
            border-radius: 4px; /* Rounded corners */
            padding: 2px 5px; /* Padding inside the code block */
            font-family: monospace; /* Monospace font for code */
            display: inline; /* Restrict background color to text */
            white-space: pre-wrap; /* Preserve whitespace and wrap text */
        }
    </style>
</head>
<body>
<header>
    <div class="nav-left">
        <a href="#">NERDWANA</a>
    </div>            
    <nav class="nav-right">
        <a href="main.html">Home</a>
        <a href="#">About</a>
        <a href="guidelines.php">Guidelines</a>
        <a href="contact.php">Contact</a>
        <a href="#" id="welcome-link">Welcome, <?php echo $username; ?></a>
        <!-- Add a hidden popup div -->
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <span class="popup-close" onclick="closePopup()">×</span>
                <!-- Display the user's email -->
                <p>Email: <?php echo $email; ?></p>
                <!-- Add a logout button -->
                <button onclick="logout()">Logout</button>
            </div>
        </div>
    </nav>
</header>

<main>
    <div class="content-container">
        <h3>Start a New Discussion</h3>
        <!-- Forum and Topic Selection -->
        <div class="dropdown-container">
            <label for="forumSelect">Select Forum:</label>
            <select id="forumSelect">
                <?php foreach ($forums as $forum): ?>
                    <option value="<?php echo $forum['id']; ?>"><?php echo htmlspecialchars($forum['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="topicSelect">Select Topic:</label>
            <select id="topicSelect">
                <?php foreach ($topics as $topic): ?>
                    <option value="<?php echo $topic['id']; ?>" data-forum-id="<?php echo $topic['forum_id']; ?>"><?php echo htmlspecialchars($topic['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
          <!-- Title Input -->
          <div class="title-input">
                <input type="text" id="postTitle" placeholder="Enter the title of your discussion" required>
            </div>
        <!-- Quill Editor -->
        <input type="hidden" id="username" value="<?php echo $username; ?>">
        <div id="editor">
            
            <div id="toolbar" class="editor-toolbar">
                <!-- Add toolbar buttons here -->
                <button class="ql-bold">B</button>
                <button class="ql-italic">I</button>
                <button class="ql-underline">U</button>
                <button class="ql-link">Link</button>
                <button class="ql-emoji">Emoji</button>
                <button class="ql-code-block">Code</button>
                <button class="ql-blockquote">Blockquote</button>
                
            </div>
            <div id="editorContainer" class="editor-content"></div>
            <div id="emoji-picker" class="emoji-picker"></div>
        </div>
        <br>
        <div class="editor-header">
                <button id="publishButton" class="button-like">Publish</button>
            </div>
    </div>
</main>
<footer>
    <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
    <a style="font-family: 'Nothingnormal', sans-serif; font-size: 18px; line-height: 1.5;">© 2024 Our Community. All rights reserved.</a>
</footer>

<!-- Include Quill and quill-emoji JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.js"></script>
<script>
    var quill = new Quill('#editorContainer', {
        theme: 'snow',
        modules: {
            toolbar: '#toolbar',
            'emoji-toolbar': true,
            'emoji-textarea': false,
            'emoji-shortname': true,
        }
    });

    document.getElementById('publishButton').addEventListener('click', function() {
            var content = quill.root.innerHTML;
            var username = document.getElementById('username').value;
            var forumId = document.getElementById('forumSelect').value;
            var topicId = document.getElementById('topicSelect').value;
            var postTitle = document.getElementById('postTitle').value.trim();

            if (!postTitle) {
                alert("Please enter a title for your discussion.");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "submit_post.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        window.location.href = 'test.php';
                    } else {
                        alert("Error: " + xhr.responseText);
                    }
                }
            };
            xhr.send("content=" + encodeURIComponent(content) + "&username=" + encodeURIComponent(username) + "&forum_id=" + encodeURIComponent(forumId) + "&topic_id=" + encodeURIComponent(topicId) + "&postTitle=" + encodeURIComponent(postTitle));
        });

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

    // Add event listener to the close button
    document.querySelector('.popup-close').addEventListener('click', closePopup);

    // Add event listener to the welcome message link
    document.getElementById('welcome-link').addEventListener('click', function(event) {
        // Prevent default link behavior
        event.preventDefault();
        // Show the popup
        showPopup();
    });

    // Filter topics based on selected forum
    document.getElementById('forumSelect').addEventListener('change', function() {
        var selectedForumId = this.value;
        var topicSelect = document.getElementById('topicSelect');

        var firstVisibleOption = null;

        // Show only the topics that match the selected forum
        Array.from(topicSelect.options).forEach(function(option) {
            if (option.getAttribute('data-forum-id') === selectedForumId) {
                option.style.display = 'block';
                if (!firstVisibleOption) {
                    firstVisibleOption = option;
                }
            } else {
                option.style.display = 'none';
            }
        });

        // Automatically select the first visible topic
        if (firstVisibleOption) {
            firstVisibleOption.selected = true;
        } else {
            topicSelect.selectedIndex = -1; // No topic selected
        }
    });

    // Trigger the change event on page load to ensure topics are filtered correctly
    document.getElementById('forumSelect').dispatchEvent(new Event('change'));

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
</script>

</body>
</html>
