<?php
include 'config.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if ($post && $post['image']) unlink("uploads/" . $post['image']);

$stmt = $conn->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);

header("Location: dashboard.php");
