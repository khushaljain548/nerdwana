<?php
session_start();

// Include your database connection file
include 'db_connection.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    // If the username or email is not set, redirect to the login page
    header("Location: login.html"); // Change to your login page URL
    exit();
}

$logged_in_username = $_SESSION['username'];
$logged_in_email = $_SESSION['email'];

// Fetch user information based on the URL parameter
$url_user = isset($_GET['user']) ? $_GET['user'] : $logged_in_username;

function fetchUserData($conn, $username) {
    $query = "SELECT username, email FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fetch the profile information of the user specified in the URL
$user_data = fetchUserData($conn, $url_user);
if (!$user_data) {
    echo "User not found.";
    exit();
}

$username = $user_data['username'];
$email = $user_data['email'];

// Function to fetch all posts by the user
function fetchUserPosts($conn, $username) {
    $query = "SELECT posts.id, posts.postTitle, posts.content, posts.created_at, forums.name AS forum_name, topics.name AS topic_name 
              FROM posts 
              JOIN forums ON posts.forum_id = forums.id 
              JOIN topics ON posts.topic_id = topics.id 
              WHERE posts.username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    return $posts;
}

// Function to fetch all replies by the user
function fetchUserReplies($conn, $username) {
    $query = "SELECT replies.id, replies.reply_content, replies.created_at, posts.postTitle, forums.name AS forum_name, topics.name AS topic_name 
              FROM replies 
              JOIN posts ON replies.post_id = posts.id 
              JOIN forums ON posts.forum_id = forums.id 
              JOIN topics ON posts.topic_id = topics.id 
              WHERE replies.username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $replies = [];
    while ($row = $result->fetch_assoc()) {
        $replies[] = $row;
    }

    return $replies;
}

// Function to delete a post
if (isset($_POST['delete_post']) && $url_user === $logged_in_username) {
    $post_id = $_POST['post_id'];
    $query_delete = "DELETE FROM posts WHERE id = ?";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $post_id);
    if ($stmt_delete->execute()) {
        // Post deleted successfully
        header("Location: profile.php?user=$username"); // Redirect to refresh the page
        exit();
    } else {
        // Handle delete failure
        echo "Error deleting post.";
    }
}

// Function to delete a reply
if (isset($_POST['delete_reply']) && $url_user === $logged_in_username) {
    $reply_id = $_POST['reply_id'];
    $query_delete = "DELETE FROM replies WHERE id = ?";
    $stmt_delete = $conn->prepare($query_delete);
    $stmt_delete->bind_param("i", $reply_id);
    if ($stmt_delete->execute()) {
        // Reply deleted successfully
        header("Location: profile.php?user=$username"); // Redirect to refresh the page
        exit();
    } else {
        // Handle delete failure
        echo "Error deleting reply.";
    }
}

// Function to delete the account
if (isset($_POST['delete_account']) && $url_user === $logged_in_username) {
    $query_delete_posts = "DELETE FROM posts WHERE username = ?";
    $stmt_delete_posts = $conn->prepare($query_delete_posts);
    $stmt_delete_posts->bind_param("s", $username);
    $stmt_delete_posts->execute();

    $query_delete_replies = "DELETE FROM replies WHERE username = ?";
    $stmt_delete_replies = $conn->prepare($query_delete_replies);
    $stmt_delete_replies->bind_param("s", $username);
    $stmt_delete_replies->execute();

    $query_delete_user = "DELETE FROM users WHERE username = ?";
    $stmt_delete_user = $conn->prepare($query_delete_user);
    $stmt_delete_user->bind_param("s", $username);
    if ($stmt_delete_user->execute()) {
        session_destroy();
        header("Location: login.html"); // Redirect to login page after account deletion
        exit();
    } else {
        // Handle delete failure
        echo "Error deleting account.";
    }
}

// Fetch all posts and replies by the user
$posts = fetchUserPosts($conn, $username);
$replies = fetchUserReplies($conn, $username);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Profile - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="ProfileStyle.css">
    <style>
        
body {
    padding-top:100px;
    padding-bottom:100px;
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    color: #1A1110;
    overflow-x:hidden;
   
}
        /* Additional styling specific to this page */
        .section {
            margin-top: 20px;
        }
      
        .post img {
            max-width: 500px;
        /* Set a specific width */
        max-height: 500px;
        /* Set a specific height */
        width: auto;
        height: auto;
        display: block;
        margin: 0 auto;
        }
        .switch-buttons button {
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
        
        .switch-buttons button.active {
    background-color: white;
    color: #d71a21;
}
        .delete-account-button {
    font-family: 'Nothingnormal', sans-serif;
    font-size: 18px;
    letter-spacing: 1px;
    background-color: #d71a21;
    color: white;
    border: 1px dotted #d71a21 !important;
    padding: 10px 20px;
    border-radius: 50px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
    margin-top: 20px; /* Add some space above the button */
}
blockquote {
            margin: 5px;
            text-align: left;
            padding: 10px 20px;
            background-color: #efefef;
            border-left: 5px solid #ccc;
            font-style: italic;
        }
.delete-account-button:hover {
    background-color: white;
    color: #d71a21;
}   .post {
    margin-bottom: 30px; /* Adjust spacing between posts */
    padding-bottom: 20px; /* Add padding below each post */
    border-bottom: 1px solid #ddd; /* Add a bottom border */
}

.post h4 {
    margin-top: 0; /* Remove top margin for post title */
}

.post p {
    margin-bottom: 10px; /* Bottom margin for post content */
}

.post small {
    display: block;
    margin-top: 10px; /* Top margin for metadata */
    margin-bottom: 10px;
}
      .post button {
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
        .post button:hover {
            background-color: white;
        color: #d71a21;
      }
      .post + .post {
    margin-top: 20px;
}
.delete-account-form .delete-account-button:hover {
    background-color: white !important;
    color: #d71a21 !important;
}
.ql-syntax {
    background-color: #f4f4f4; /* Light gray background */
    border-radius: 4px; /* Rounded corners */
    padding: 5px; /* Padding inside the code block */
    font-family: monospace; /* Monospace font for code */
    display: inline-block; /* Use block display to take full width */
    text-align: left; /* Align text to the left */
    white-space: pre-wrap; /* Preserve whitespace and wrap text */
    margin-left: auto; /* Auto margin for centering */
    margin-right: auto; /* Auto margin for centering */
}
header {
    overflow:hidden;
    
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 20" preserveAspectRatio="none"><path d="M0,10 Q6,0 12,10 T24,10 T36,10 T48,10 T60,10 T72,10" fill="none" stroke="%23D3D3D3" stroke-width="1.5"/></svg>');
    /* Rest of the properties remain the same */
    background-size: 72px 10px;
    background-position: bottom;
    background-repeat: repeat-x;
    padding-bottom: 10px;
    overflow-x:hidden;
    background-color: #ffffff;
    color: #1A1110;
    text-align: center;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 10px 20px 20px;
    text-overflow:hidden;
    position: fixed;
    top: 0;
    left: 0;
    padding-right: 20px !important;
 
}
footer {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 20" preserveAspectRatio="none"><path d="M0,10 Q6,20 12,10 T24,10 T36,10 T48,10 T60,10 T72,10" fill="none" stroke="%23D3D3D3" stroke-width="1.5"/></svg>');
    background-size: 72px 10px;
    background-position: top;
    background-repeat: repeat-x;
    background-color: #fff;  /* Change this to transparent */
    color: #1A1110;
    text-align: center;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    padding: 10px 20px;
    box-sizing: border-box;
    box-shadow: none;
    z-index: 1000;
}
footer::before {
    content: '';
    position: absolute;
    top: -1px;
    left: 0;
    right: 0;
    height: 1px;
    background-color: #ffffff;  /* Match this to your page background color */
    shadow: none;
}

    </style>
</head>
<body>
<header>
    <div class="nav-left">
        <a href="about.php">NERDWANA</a>
    </div>            
    <nav class="nav-right">
        <a href="test.php">Posts</a>
        <a href="about.php">About</a>
        <a href="guidelines.php">Guidelines</a>
        <a href="contact.php">Contact</a>
        <a href="#" id="welcome-link" onclick="showPopup()" style="padding-right:25px";>Welcome, <?php echo htmlspecialchars($logged_in_username); ?></a>
        <!-- Add a hidden popup div -->
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <span class="popup-close" onclick="closePopup()">×</span>
                <!-- Display the logged-in user's email -->
                <p>Email: <?php echo htmlspecialchars($logged_in_email); ?></p>
                
                <!-- Add a logout button -->
                <button onclick="logout()">Logout</button>
                <button onclick="window.location.href='change_password.php';">Change Password</button>
            </div>
        </div>
    </nav>
</header>

<main>
    <div class="content-container">
        <h3><?php echo htmlspecialchars($username); ?>'s Profile</h3>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>

        <div class="switch-buttons">
            <button onclick="showSection('posts')">Posts</button>
            <button onclick="showSection('replies')"class="active">Replies</button>
        </div>

         <div id="posts-section" class="section">
            <h3>Posts</h3>
    <?php if (empty($posts)): ?>
        <p>No posts found.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h4><?php echo htmlspecialchars($post['postTitle']); ?></h4>
                <p><?php echo nl2br(($post['content'])); ?></p>
                <small>Forum: <?php echo htmlspecialchars($post['forum_name']); ?> | Topic: <?php echo htmlspecialchars($post['topic_name']); ?> | Created at: <?php echo $post['created_at']; ?></small>
                
                <?php if ($url_user === $logged_in_username): ?>
                    <form method="post" action="">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" name="delete_post" class="delete-button">Delete Post</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

        <div id="replies-section" class="section" style="display:none;">
            <h3>Replies</h3>
            <?php if (empty($replies)): ?>
                <p>No replies found.</p>
            <?php else: ?>
                <?php foreach ($replies as $reply): ?>
                    <div class="post">
                        <h4>Reply to: <?php echo htmlspecialchars($reply['postTitle']); ?></h4>
                        <p><?php echo htmlspecialchars($reply['reply_content']); ?></p>
                        <small>Forum: <?php echo htmlspecialchars($reply['forum_name']); ?> | Topic: <?php echo htmlspecialchars($reply['topic_name']); ?> | Created at: <?php echo $reply['created_at']; ?></small>
                        <?php if ($url_user === $logged_in_username): ?>
                            <form method="post" action="">
                                <input type="hidden" name="reply_id" value="<?php echo $reply['id']; ?>">
                                <button type="submit" name="delete_reply">Delete Reply</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($url_user === $logged_in_username): ?>
    <form method="post" action="" class="delete-account-form">
        <button type="submit" name="delete_account" class="delete-account-button">Delete Account</button>
    </form>
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
          line-height: 1.5;">© 2024 NERDWANA. All rights reserved.</a>
    </footer>

<script>
    function showSection(section) {
        document.getElementById('posts-section').style.display = 'none';
        document.getElementById('replies-section').style.display = 'none';
        document.getElementById(section + '-section').style.display = 'block';
    }

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
    function showSection(section) {
    document.getElementById('posts-section').style.display = 'none';
    document.getElementById('replies-section').style.display = 'none';
    document.getElementById(section + '-section').style.display = 'block';
    
    // Update button styles
    updateButtonStyles(section);
}

function updateButtonStyles(activeSection) {
    var buttons = document.querySelectorAll('.switch-buttons button');
    buttons.forEach(function(button) {
        if (button.textContent.toLowerCase() === activeSection) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
}

// Set initial active state
updateButtonStyles('posts');    
</script>
</body>
</html>