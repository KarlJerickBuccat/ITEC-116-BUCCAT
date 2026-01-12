<?php include 'inc/db.php'; $catId = intval($_GET['id'] ?? 0);
$catStmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$catStmt->bind_param('i', $catId);
$catStmt->execute();
$cat = $catStmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Category: <?php echo htmlspecialchars($cat['name'] ?? ''); ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>Posts in "<?php echo htmlspecialchars($cat['name']); ?>"</h1>
<?php
$postStmt = $conn->prepare("SELECT * FROM posts WHERE category_id = ? ORDER BY created_at DESC");
$postStmt->bind_param('i', $catId);
$postStmt->execute();
$res = $postStmt->get_result();
while ($row = $res->fetch_assoc()) {
    echo "<div>";
    echo "<h2><a href='post.php?id={$row['id']}'>" . htmlspecialchars($row['title']) . "</a></h2>";
    echo "<p>" . substr(strip_tags($row['content']), 0, 150) . "...</p>";
    echo "</div><hr>";
}
?>
<p><a href='index.php'>&larr; Back to Home</a></p>
</body>
</html>