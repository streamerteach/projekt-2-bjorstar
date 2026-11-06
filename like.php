<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
requireLogin();

// Save a like
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['liked_username'])) {
    $likedUsername = $_POST['liked_username'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$likedUsername]);
    $likedUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($likedUser && $likedUser['id'] != $_SESSION['user_id']) {
        $insert = $conn->prepare("INSERT IGNORE INTO likes (user_id, liked_user_id) VALUES (?, ?)");
        $insert->execute([$_SESSION['user_id'], $likedUser['id']]);
    }
}

header("Location: index.php");
exit;