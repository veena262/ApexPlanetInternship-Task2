<?php
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = htmlspecialchars(trim($_POST['username']));
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
    $error = "Passwords do not match.";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";
  } else {
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    try {
      $stmt->execute([$username, $hashed]);
      $success = "Account created! <a href='login.php'>Login now</a>";
    } catch (PDOException $e) {
      $error = "Username already exists.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | My Flower Shop</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;700&display=swap');

    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      font-family: 'Quicksand', sans-serif;
      background: url('images/flowers-register.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 30px;
      max-width: 420px;
      width: 90%;
      border-radius: 14px;
      text-align: center;
      box-shadow: 0 6px 25px rgba(0,0,0,0.15);
    }
    h1 {
      font-family: 'Pacifico', cursive;
      color: #c86c9a;
      margin-bottom: 10px;
    }
    h2 {
      color: #874f68;
      margin-bottom: 25px;
    }
    input {
      padding: 12px;
      margin-bottom: 16px;
      border-radius: 8px;
      border: 1px solid #f0cfe6;
      font-size: 16px;
      width: 100%;
    }
    button {
      padding: 12px;
      font-size: 16px;
      background: #c86c9a;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }
    button:hover {
      background-color: #b65988;
    }
    a {
      color: #ad588b;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🌼 My Flower Shop</h1>
    <h2>Register</h2>
    <?php if ($error): ?>
      <p style="color:red;"><?= $error ?></p>
    <?php elseif ($success): ?>
      <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Choose a username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm password" required>
      <button type="submit">Register</button>
    </form>
    <p style="margin-top: 14px;">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
