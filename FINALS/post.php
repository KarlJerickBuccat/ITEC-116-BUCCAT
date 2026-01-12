<?php
session_start();
// block guests – allow either regular user or admin
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include 'inc/db.php';
$id = intval($_GET['id'] ?? 0);

// fetch post + category
$stmt = $conn->prepare("SELECT posts.*, categories.name AS category FROM posts LEFT JOIN categories ON posts.category_id = categories.id WHERE posts.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

// set current user (admin or regular)
$current_user = $_SESSION['username'] ?? ($_SESSION['admin'] ?? null);

// Handle new comment submission (only if logged‑in user/admin)
$comment_msg = '';
if ($current_user && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if ($comment) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, name, content) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $id, $current_user, $comment);
        $stmt->execute();
        $comment_msg = 'Comment posted successfully!';
    } else {
        $comment_msg = 'Comment cannot be empty.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($post['title'] ?? 'Post'); ?></title>
  <link rel="stylesheet" href="css/poststyle.css">
</head>
<body>
  <h1><?= htmlspecialchars($post['title']); ?></h1>
  <p><em>Category: <?= htmlspecialchars($post['category']); ?> | Published: <?= $post['created_at']; ?></em></p>
  <div>
    <?= nl2br($post['content']); ?>
  </div>
  <hr>

  <h2>Leave a Comment</h2>
  <?php if ($comment_msg) echo "<p style='color:green;'>$comment_msg</p>"; ?>

  <?php if ($current_user): ?>
    <p>Commenting as <strong><?= htmlspecialchars($current_user); ?></strong></p>
    <form method="POST">
      <textarea name="comment" placeholder="Write your comment here..." rows="4" cols="50" required></textarea><br><br>
      <button type="submit">Submit Comment</button>
    </form>
  <?php endif; ?>
  <hr>
  <h2>Comments</h2>
  <?php
  $stmt = $conn->prepare("SELECT name, content, created_at FROM comments WHERE post_id = ? ORDER BY created_at DESC");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          echo "<div><strong>" . htmlspecialchars($row['name']) . "</strong> <em>on " . $row['created_at'] . "</em><br>";
          echo nl2br(htmlspecialchars($row['content'])) . "</div><hr>";
      }
  } else {
      echo "<p>No comments yet.</p>";
  }
  ?>

  <p><a href="index.php">&larr; Back to Home</a></p>
</body>
</html>
