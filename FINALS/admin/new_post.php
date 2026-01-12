<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: ../login.php'); exit; }
include '../inc/db.php';

$error = '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $category_id = intval($_POST['category'] ?? 0);

    if ($title === '' || $content === '' || $category_id === 0) {
        $error = 'All fields — including a category — are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (title, content, category_id) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $title, $content, $category_id);
        $stmt->execute();
        header('Location: dashboard.php');
        exit;
    }
}

// Fetch categories for the select box
$cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>New Post</title>
  <link rel="stylesheet" href="../css/usernewpoststyle.css">
</head>
<body>
  <div class="outer">
    <div class="inner">
<h1>Create Post</h1>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
  <input type="text" class="titlebox" name="title" placeholder="Post title" required><br><br>
  <textarea name="content" placeholder="Post content" rows="10" cols="50" required></textarea><br><br>

  <?php if ($cats->num_rows > 0): ?>
      <select name="category" required class="category">
        <option value="">-- Select category --</option>
        <?php while ($c = $cats->fetch_assoc()): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
        <?php endwhile; ?>
      </select><br><br>
      <button type="submit">Publish</button>
  <?php else: ?>
      <p style="color:red;">No categories found. <a href="categories.php">Add a category first</a>.</p>
  <?php endif; ?>
</form>
<p><a href='dashboard.php'>&larr; Back to Dashboard</a></p>
</div>
</div>
</body>
</html>
