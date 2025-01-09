<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/Post.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $post = new Post($db);
    
    $files = isset($_FILES['files']) ? $_FILES['files'] : [];
    
    if ($post->create(
        $_SESSION['user_id'],
        $_POST['title'],
        $_POST['content'],
        $_POST['category'],
        $files
    )) {
        header('Location: profile.php');
        exit();
    }
}

header('Location: profile.php?error=1');
exit(); 