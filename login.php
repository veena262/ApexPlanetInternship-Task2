<?php
include 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = htmlspecialchars(trim($_POST['username']));
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    header("Location: dashboard.php");
    exit();
  } else {
    $error = "Invalid username or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | My Flower Shop</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;700&display=swap');

    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      font-family: 'Quicksand', sans-serif;
      background: url('images/flowers-login.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      background: rgba(255, 255, 255, 0.92);
      padding: 35px 30px;
      border-radius: 14px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      box-shadow: 0 6px 30px rgba(0,0,0,0.15);
    }
    h1 {
      font-family: 'Pacifico', cursive;
      color: #d18aaa;
      margin-bottom: 10px;
    }
    h2 {
      color: #874f68;
      margin-bottom: 25px;
    }
    form {
      display: flex;
      flex-direction: column;
    }
    input {
      padding: 12px;
      margin-bottom: 16px;
      border-radius: 8px;
      border: 1px solid #f0cfe6;
      font-size: 16px;
    }
    button {
      background: #d18aaa;
      color: white;
      font-weight: bold;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      padding: 12px;
      cursor: pointer;
    }
    button:hover {
      background: #c1799d;
    }
    a {
      color: #ad588b;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🌸 My Flower Shop</h1>
    <h2>Login</h2>
    <?php if ($error): ?>
      <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p style="margin-top: 12px;">Don't have an account? <a href="register.php">Register here</a></p>
  </div>
</body>
</html>
