<?php
include 'inc/db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif (strlen($username) < 3 || strlen($password) < 4) {
        $error = 'Username must be at least 3 characters and password at least 4 characters';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Username already exists';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $insert->bind_param('ss', $username, $hash);
            $insert->execute();
            $success = 'User registered! You can now <a href="login.php">login</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Registration</title>
  <link rel="stylesheet" href="css/userregstyle.css">
</head>
<body>
<h1>User Registration</h1>
<div class="outer">
    <div class="inner">
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="POST">
  <input type="text" class="inputbox" name="username" placeholder="Username" required><br><br>
  <input type="password" class="inputbox" name="password" placeholder="Password" required><br><br>
  <input type="password" class="inputbox" name="confirm" placeholder="Confirm Password" required><br><br>
  <button type="submit">Register</button>
</form>
<p><a href="login.php">&larr; Back to Login</a></p>
</div>
</div>
</body>
</html>