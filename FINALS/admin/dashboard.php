<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: ../login.php'); exit; }
include '../inc/db.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/dashboardstyle.css">
</head>
<body>
<h1>Dashboard</h1>
<div class="outer">
  <div class="outer2">
  <div class="inner">
  <form action="new_post.php" method="get">
    <button type="submit" class="btn2">+ New Post</button>
  </form>
  <form action="categories.php" method="get">
    <button type="submit" class="btn2">Manage Categories</button>
  </form>
  <form action="../index.php" method="get">
    <button type="submit" class="btn2">Go to Blog</button>
  </form>
  <form action="../logout.php" method="get">
    <button type="submit" class="btn2">Logout</button>
  </form>
</div>
<div class="inner2">
    <table border='1' cellpadding='5' cellspacing='0'>
      <tr><th>ID</th><th>Title</th><th>Created</th><th>Actions</th></tr>
      <?php
      $res = $conn->query("SELECT id, title, created_at FROM posts ORDER BY created_at DESC");
      while ($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['title']}</td>";
    echo "<td>{$row['created_at']}</td>";
    echo "<td><a href='edit_post.php?id={$row['id']}'>Edit</a> | <a href='delete_post.php?id={$row['id']}' onclick='return confirm(\"Delete?\")'>Delete</a></td>";
    echo "</tr>";
}
?>
</table>
</div>
</div>
</div>
</body>
</html>
