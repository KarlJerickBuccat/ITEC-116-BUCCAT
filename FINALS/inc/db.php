<?php
$conn = new mysqli("localhost", "root", "", "simple_blog");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>