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
    
    $files = [];
    if(isset($_FILES['files'])) {
        foreach($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            if($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                $files[] = [
                    'name' => $_FILES['files']['name'][$key],
                    'type' => $_FILES['files']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'content' => base64_encode(file_get_contents($tmp_name))
                ];
            }
        }
    }
    
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