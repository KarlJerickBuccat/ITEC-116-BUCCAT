<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: ../login.php'); exit; }
include '../inc/db.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM posts WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
header('Location: dashboard.php');
exit;
?>
