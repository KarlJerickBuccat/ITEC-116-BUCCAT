<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'inc/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $category_id = intval($_POST['category']);

    if ($title === '' || $content === '' || $category_id === 0) {
        $error = 'All fields are required.';
    } else {
        $user_id = $_SESSION['user_id'];
        $stmt    = $conn->prepare("INSERT INTO posts (title, content, category_id, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssii', $title, $content, $category_id, $user_id);
        $stmt->execute();
        
        // redirect to home after successful post
        header('Location: index.php?msg=post_created');
        exit;
    }
}
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Create Blog Post</title>
  <link rel="stylesheet" href="css/usernewpoststyle.css">
</head>
<body>
<div class="outer">
  <div class="inner">
    <h1>Create New Blog Post</h1>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
      <input type="text" class="titlebox" name="title" placeholder="Title" required><br><br>
      <textarea name="content" rows="8" cols="50" placeholder="Content" required></textarea><br><br>
      <select name="category" required class="category">
        <option value="">-- Select category --</option>
        <?php while ($row = $categories->fetch_assoc()): ?>
          <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
      </select><br><br>
      <button type="submit">Publish</button>
    </form>

    <p><a href="index.php">&larr; Back to Blog</a></p>
  </div>
</div>
</body>
</html>
