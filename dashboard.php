<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
// Handle image upload if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && isset($_POST['title'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $imageName = null;

    if ($_FILES['image']['name']) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $error = "Only JPG, JPEG, PNG, and GIF images are allowed.";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $error = "Image must be less than 2MB.";
        } else {
            $imageName = uniqid('img_') . "." . $ext;
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                $stmt = $conn->prepare("INSERT INTO posts (user_id, title, image) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $title, $imageName]);
                header("Location: dashboard.php"); // simple reload (prevents resubmits)
                exit();
            } else {
                $error = "Error uploading your image. Try again.";
            }
        }
    } else {
        $error = "Please select an image to upload.";
    }
}

// Now fetch all posts for display
$stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();

// For background image
$backgroundImage = 'images/flowers-dashboard.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🌸 My Flower Shop - Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      font-family: 'Quicksand', sans-serif;
      background-image: url('<?php echo $backgroundImage; ?>');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      background-attachment: fixed;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      width: 95%;
      max-width: 900px;
      background: rgba(255,255,255,0.92);
      padding: 38px 32px 32px 32px;
      border-radius: 18px;
      box-shadow: 0 0 30px rgba(180,90,120,0.10);
    }
    h1 {
      font-family: 'Pacifico', cursive;
      text-align: center;
      color: #c86c9a;
      font-size: 2.2rem;
      margin-bottom: 25px;
      letter-spacing:1px;
    }
    .nav {
      text-align: right;
      margin-bottom: 25px;
    }
    .nav a {
      background: #f5b5cd;
      color: #fff;
      padding: 10px 16px;
      font-weight: bold;
      border-radius: 8px;
      margin-left: 10px;
      text-decoration: none;
      transition: background 0.3s ease;
      font-size: 1.04rem;
    }
    .nav a:hover {
      background-color: #e48bb0;
    }
    .uploadform {
      background: #f9e8f0;
      border-radius: 10px;
      padding: 22px 18px 16px 18px;
      margin-bottom: 28px;
      box-shadow: 0 2px 12px rgba(214,110,165,0.08);
      max-width: 450px;
      margin-left: auto;
      margin-right: auto;
    }
    .uploadform label {
      display: block;
      font-weight: bold;
      color: #b05e89;
      margin-bottom: 6px;
    }
    .uploadform input[type="text"] {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1.5px solid #eebcdc;
      margin-bottom: 13px;
      font-size: 1rem;
      background: #fff;
    }
    .uploadform input[type="file"] {
      margin-bottom: 16px;
    }
    .uploadform button {
      background: #de81b0;
      color: white;
      font-weight: bold;
      padding: 10px 18px;
      border: none;
      border-radius: 7px;
      font-size: 1.05rem;
      cursor: pointer;
      box-shadow: 0 2px 5px #f8daf466;
      transition: background 0.24s;
    }
    .uploadform button:hover {
      background-color: #b85b90;
    }
    .error-msg {
      color: #b91548;
      text-align: center;
      margin-bottom: 12px;
      font-weight: bold;
    }
    .post {
      background: #fff9fc;
      padding: 18px 22px;
      margin-bottom: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: box-shadow 0.3s ease;
    }
    .post:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.09);
    }
    .post h3 {
      color: #ab5480;
      margin-bottom: 8px;
      font-family: 'Pacifico', cursive;
    }
    .post small {
      color: #888;
      display: block;
      margin-bottom: 8px;
    }
    .post img {
      width: 100%;
      max-height: 350px;
      object-fit: cover;
      border-radius: 12px;
      margin: 13px 0 15px;
      box-shadow: 0 3px 14px rgba(0,0,0,0.10);
      border: 1.5px solid #f7d5e3;
      background: #fff;
    }
    .actions a {
      display: inline-block;
      margin-top: 5px;
      margin-right: 18px;
      color: #b0628e;
      text-decoration: none;
      font-weight: bold;
      transition: color 0.2s;
    }
    .actions a:hover {
      text-decoration: underline;
      color: #a13d77;
    }
    @media (max-width: 650px) {
      .container {
        padding: 13px 2vw 7vw 2vw;
        max-width: 99vw;
      }
      .post img {
        max-height: 200px;
      }
      h1 { font-size: 1.5rem;}
      .uploadform { padding: 8px 6vw 12px 6vw;}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="nav">
      <a href="logout.php">🚪 Logout</a>
    </div>
    <h1>My Flower Shop Dashboard</h1>

    <form class="uploadform" method="POST" enctype="multipart/form-data">
      <?php if($error): ?>
        <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>
      <label for="title">Arrangement Name</label>
      <input type="text" id="title" name="title" placeholder="e.g. Pink Roses Bouquet" required>
      <label for="image">Flower Photo</label>
      <input type="file" id="image" name="image" accept="image/*" required>
      <button type="submit">Upload</button>
    </form>

    <?php if (empty($posts)): ?>
      <p style="text-align:center;">No flower images yet. Start by posting one! 🌸</p>
    <?php else: ?>
      <?php foreach ($posts as $post): ?>
        <div class="post">
          <h3><?= htmlspecialchars($post['title']) ?></h3>
          <?php if (!empty($post['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Flower Arrangement">
          <?php endif; ?>
          <small>Uploaded on <?= htmlspecialchars($post['created_at']) ?></small>
          <div class="actions">
            <a href="edit_post.php?id=<?= $post['id'] ?>">✏️ Edit</a>
            <a href="delete_post.php?id=<?= $post['id'] ?>" onclick="return confirm('Are you sure you want to delete this post?')">🗑️ Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
