<?php
include 'config.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, image) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $imageName]);
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Upload Flower Arrangement</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
<div class="container">
  <h2>Upload Flower Arrangement</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Arrangement Name:</label>
    <input name="title" placeholder="e.g., Pink Peony Bunch" required>
    <label>Flower Photo:</label>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">Upload</button>
  </form>
  <p><a href="dashboard.php">Back to Arrangements</a></p>
</div>
</body>
</html>
