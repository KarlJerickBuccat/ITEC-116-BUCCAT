<?php
session_start();
include 'inc/db.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Simple Blog</title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

<h1>Latest Blog Posts</h1>
<div class="divbtn">
<?php
if (isset($_SESSION['admin'])) { // admin logged in
?>
  <p>
    <form action="admin/dashboard.php" method="get">
      <button class="btn2" type="submit">Manage Posts</button>
    </form>
    <form action="logout.php" method="get">
      <button class="btn2" type="submit">Logout (<?= htmlspecialchars($_SESSION['admin']); ?>)</button>
    </form>
  </p>
<?php
} elseif (isset($_SESSION['user_id'])) { // regular user logged in
?>
  <p>
    <form action="user_new_post.php" method="get">
      <button class="btn2" type="submit">+ Create New Blog Post</button>
    </form>
    <form action="logout.php" method="get">
      <button class="btn2" type="submit">Logout (<?= htmlspecialchars($_SESSION['username']); ?>)</button>
    </form>
  </p>
<?php
} else { // guest
?>
  <p>
    <form action="login.php" method="get">
      <button class="btn2" type="submit">Login</button>
    </form>
    <form action="user_register.php" method="get">
      <button class="btn2" type="submit">Register</button>
    </form>
  </p>
<?php } ?>
</div>

<?php
$result = $conn->query("SELECT posts.*, categories.name AS category, users.username AS author
                        FROM posts
                        LEFT JOIN categories ON posts.category_id = categories.id
                        LEFT JOIN users ON posts.user_id = users.id
                        ORDER BY posts.created_at DESC");

while ($row = $result->fetch_assoc()) {
    // link to post or login gate
    $postLink = (isset($_SESSION['user_id']) || isset($_SESSION['admin']))
                ? "post.php?id={$row['id']}"
                : "login.php";

    echo "<div>";
    echo "<h2><a href='{$postLink}'>" . htmlspecialchars($row['title']) . "</a></h2>";
    echo "<p><em>Category: " . htmlspecialchars($row['category']) . "</em>";
    if (isset($_SESSION['user_id']) || isset($_SESSION['admin'])) {
        $author = $row['author'] ?? 'Unknown';
        echo " | Author: <strong>" . htmlspecialchars($author) . "</strong>";
    }
    echo "</p>";
    echo "<p>" . substr(strip_tags($row['content']), 0, 150) . "...</p>";
    echo "</div><hr>";
}
?>

</body>
</html>
