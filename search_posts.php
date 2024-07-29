<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
// Get the search keyword from the URL
$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';

$query = "SELECT p.id, p.username, p.content, p.created_at, p.topic_id, p.forum_id, p.postTitle
          FROM posts p";

if (!empty($keyword)) {
    $query .= " WHERE p.content LIKE '%$keyword%' OR p.postTitle LIKE '%$keyword%'";
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
    $units = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second',
    );
    foreach ($units as $unit => $text) {
        if ($difference < $unit) continue;
        $numberOfUnits = floor($difference / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
}

if (count($posts) > 0): ?>
    <br> <?php foreach ($posts as $post) : ?> <div class="post-container">
          <h2> <?php echo htmlspecialchars($post['username']); ?> </h2>
          <p>
            <a href="post.php?id=<?php echo $post['id']; ?>"> <?php echo htmlspecialchars($post['postTitle']); ?> </a>
          </p>
          <p>Topic: <?php echo htmlspecialchars($post['topic_name']); ?> </p>
          <p>Forum: <?php echo htmlspecialchars($post['forum_name']); ?> </p>
          <hr>
          <div class="content"> <?php echo nl2br($post['content']); ?> </div>
          <hr>
          <div class="timestamp"> <?php echo timeAgo($post['created_at']); ?> </div>
        </div> <?php endforeach; ?>
      </div>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>

