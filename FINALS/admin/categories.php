<?php
/* admin/categories.php */
session_start();
if (!isset($_SESSION['admin'])) {      // protect page
    header('Location: ../login.php'); 
    exit;
}

include '../inc/db.php';

# ── Handle “add new category”
if (isset($_POST['new_cat'])) {
    $name = trim($_POST['cat_name'] ?? '');

    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param('s', $name);
        $stmt->execute();
    }
    header('Location: categories.php');
    exit;
}

# ── Handle “delete category”
if (isset($_GET['delete'])) {
    $del = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param('i', $del);
    $stmt->execute();
    header('Location: categories.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Categories</title>
  <link rel="stylesheet" href="../css/categoriesstyle.css">
</head>
<body>
<h1>Categories</h1>
<div class="outer">
  <div class="outer2">
    <div class="inner">
<!-- add-category form -->
<form method="POST" style="margin-bottom:1rem;">
  <input type="text" class="addbox" name="cat_name" placeholder="New category name" required>
  <button type="submit" name="new_cat">Add</button>
</form>
</div>
<div class="inner2">
<!-- categories table -->
<table border="1" cellpadding="6" cellspacing="0">
 <tr><th>ID</th><th>Name</th><th>Posts</th><th>Action</th></tr>
 <?php
 $res = $conn->query("
     SELECT c.id, c.name, COUNT(p.id) AS total
     FROM categories c
     LEFT JOIN posts p ON p.category_id = c.id
     GROUP BY c.id
     ORDER BY c.name
 ");
 while ($row = $res->fetch_assoc()):
 ?>
   <tr>
     <td><?= $row['id'] ?></td>
     <td><?= htmlspecialchars($row['name']) ?></td>
     <td><?= $row['total'] ?></td>
     <td>
       <a href="?delete=<?= $row['id']; ?>"
          onclick="return confirm('Delete this category?')">Delete</a>
     </td>
   </tr>
 <?php endwhile; ?>
</table>
</div>
<div class="inner3">
<p><a href='dashboard.php'>&larr; Back to Dashboard</a></p>
</div>
</div>
</div>
</div>
</body>
</html>
