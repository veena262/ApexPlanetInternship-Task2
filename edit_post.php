<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Post not found.");
}

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    die("Invalid post or no permission.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $imageName = $post['image']; // current image

    // If image uploaded
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $error = "Allowed image types: JPG, JPEG, PNG, GIF.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $error = "Image should be less than 2MB.";
        } else {
            // Delete old image
            if ($post['image'] && file_exists("uploads/{$post['image']}")) {
                unlink("uploads/{$post['image']}");
            }
            $imageName = uniqid('img_') . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imageName);
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, image = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $imageName, $id, $_SESSION['user_id']]);
        header("Location: dashboard.php");
        exit();
    }
}

// Custom background image
$backgroundImage = 'images/floral-edit.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Post | My Flower Shop</title>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      font-family: 'Quicksand', sans-serif;
      background: url('<?php echo $backgroundImage; ?>') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      max-width: 500px;
      width: 90%;
      background: rgba(255,255,255,0.92);
      padding: 32px 28px 26px;
      border-radius: 16px;
      box-shadow: 0 0 18px rgba(200,100,140,0.1);
    }
    h1 {
      font-family: 'Pacifico', cursive;
      text-align: center;
      color: #c86c9a;
      font-size: 2rem;
      margin-bottom: 26px;
    }
    form label {
      font-weight: bold;
      color: #a45b89;
      display: block;
      margin-bottom: 5px;
    }
    form input[type="text"],
    form input[type="file"] {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      border: 1.4px solid #f1cfe0;
      border-radius: 8px;
      background: #fffafc;
      margin-bottom: 16px;
    }
    img.preview {
      max-width: 100%;
      max-height: 220px;
      display: block;
      margin: 10px 0 18px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      border: 1px solid #eddbea;
    }
    .buttons {
      text-align: center;
    }
    button {
      padding: 11px 24px;
      font-size: 16px;
      font-weight: bold;
      background: #de81b0;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-right: 8px;
    }
    button:hover {
      background: #bf6a99;
    }
    .back {
      display: inline-block;
      font-size: 15px;
      margin-top: 18px;
      text-align: center;
      width: 100%;
    }
    a {
      color: #a14d80;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .error-msg {
      background: #ffe3eb;
      padding: 10px;
      color: #b4003f;
      margin-bottom: 16px;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Edit Flower Post</h1>

    <?php if ($error): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label for="title">Arrangement Title</label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

      <label>Current Photo</label>
      <img src="uploads/<?= $post['image'] ?>" class="preview" alt="Current Flower Image">

      <label for="image">Change Photo (optional)</label>
      <input type="file" name="image" id="image" accept="image/*">

      <div class="buttons">
        <button type="submit">Update Post</button>
        <a href="dashboard.php" style="margin-left: 16px; font-weight: bold;">← Back to Dashboard</a>
      </div>
    </form>
  </div>
</body>
</html>
