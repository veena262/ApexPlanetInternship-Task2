<?php
$host = 'localhost';
$db = 'blog_app';
$user = 'root';
$pass = ''; // adjust as necessary

$conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start(); // Start session here
?>
