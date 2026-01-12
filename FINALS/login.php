<?php
session_start();
include 'inc/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role']     ?? 'user'; // default to regular user

    // Choose table based on role
    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $account = $stmt->get_result()->fetch_assoc();

    // Verify password
    if ($account && password_verify($password, $account['password'])) {
        if ($role === 'admin') {
            $_SESSION['admin'] = $account['username'];
            header('Location: admin/dashboard.php');
        } else {
            $_SESSION['user_id'] = $account['id'];
            $_SESSION['username'] = $account['username'];
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="css/loginstyle.css">
</head>
<body>
<h1>Login</h1>
<div class="outer">
    <div class="inner">
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
  <input type="text" class="inputbox" name="username" placeholder="Username" required><br><br>
  <input type="password" class="inputbox" name="password" placeholder="Password" required><br><br>
  <label><input type="radio" name="role" value="user" checked> Regular User</label>
  <label><input type="radio" name="role" value="admin"> Admin</label><br><br>
  <button type="submit">Login</button>
</form>

<p>
  <a href="user_register.php">Register as User</a> |
  <a href="register.php">Register as Admin</a>
</p>
<p><a href="index.php">&larr; Back to Home</a></p>
</div>
</div>
</body>
</html>