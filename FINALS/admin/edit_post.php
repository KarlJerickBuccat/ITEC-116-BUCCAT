<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: ../login.php'); exit; }
include '../inc/db.php';
$id = intval($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category'];
    $stmt = $conn->prepare("UPDATE posts SET title=?, content=?, category_id=? WHERE id=?");
    $stmt->bind_param('ssii', $title, $content, $category_id, $id);
    $stmt->execute();
    header('Location: dashboard.php');
    exit;
}
$postStmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$postStmt->bind_param('i', $id);
$postStmt->execute();
$post = $postStmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Post</title>
  <link rel="stylesheet" href="../css/usernewpoststyle.css"">
</head>
<body>
  <div class="outer">
    <div class="inner">
<h1>Edit Post</h1>
<form method="POST">
  <input type="text" class="titlebox" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>
  <textarea name="content" rows="10" cols="50" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>
  <select name="category" required class="category">
    <?php
      $cats = $conn->query("SELECT * FROM categories");
      while ($c = $cats->fetch_assoc()) {
        $sel = $c['id'] == $post['category_id'] ? 'selected' : '';
        echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
      }
    ?>
  </select><br><br>
  <button type="submit">Update</button>
</form>
<p><a href='dashboard.php'>&larr; Back to Dashboard</a></p>
</div>
</div>
</body>
</html>
