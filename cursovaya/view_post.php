<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/Post.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$post = new Post($db);

$post_data = $post->getPostById($_GET['id']);

if (!$post_data) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($post_data['title']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Главная</a>
            <?php if (isLoggedIn()): ?>
                <a href="profile.php">Мой профиль</a>
                <a href="logout.php">Выйти</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <article class="post-full">
            <h1><?php echo htmlspecialchars($post_data['title']); ?></h1>
            <div class="post-meta">
                Автор: <a href="view_profile.php?id=<?php echo $post_data['user_id']; ?>">
                    <?php echo htmlspecialchars($post_data['username']); ?>
                </a>
                | <?php echo date('d.m.Y H:i', strtotime($post_data['created_at'])); ?>
            </div>
            
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post_data['content'])); ?>
            </div>

            <?php if (!empty($post_data['files'])): ?>
                <div class="post-files">
                    <?php foreach ($post_data['files'] as $file): ?>
                        <?php if (in_array($file['file_type'], ['image/jpeg', 'image/png', 'image/gif'])): ?>
                            <div class="image-file">
                                <img src="data:<?php echo $file['file_type']; ?>;base64,<?php echo $file['file_content']; ?>" 
                                     alt="Image" class="post-image">
                            </div>
                        <?php else: ?>
                            <div class="document-file">
                                <a href="data:<?php echo $file['file_type']; ?>;base64,<?php echo $file['file_content']; ?>" 
                                   download="<?php echo htmlspecialchars($file['file_name']); ?>">
                                    <?php echo htmlspecialchars($file['file_name']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    </main>
</body>
</html> 