<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Post.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$post = new Post($db);

// Получаем данные пользователя и его посты
$user_data = $user->getUserById($_GET['id']);
if (!$user_data) {
    header('Location: index.php');
    exit();
}

$user_posts = $post->getUserPosts($_GET['id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Профиль <?php echo htmlspecialchars($user_data['username']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Главная</a>
            <?php if (isLoggedIn()): ?>
                <a href="profile.php">Мой профиль</a>
                <a href="edit_profile.php" class="edit-btn">Редактировать профиль</a>
                <?php if(isAdmin()): ?>
                    <a href="admin/users.php">Управление пользователями</a>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти</a>
                <a href="register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="profile">
        <div class="profile-info">
            <h2>Профиль пользователя <?php echo htmlspecialchars($user_data['username']); ?></h2>
            <p>Имя: <?php echo htmlspecialchars($user_data['first_name']); ?></p>
            <p>Фамилия: <?php echo htmlspecialchars($user_data['last_name']); ?></p>
        </div>

        <div class="profile-wall">
            <div class="posts-wall">
                <?php if (empty($user_posts)): ?>
                    <p class="no-posts">У пользователя пока нет постов</p>
                <?php else: ?>
                    <?php foreach ($user_posts as $post): ?>
                        <article class="post-card">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <div class="post-meta">
                                <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?>
                                | <?php echo htmlspecialchars($post['category']); ?>
                            </div>
                            <div class="post-content">
                                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                            </div>
                            <?php if (!empty($post['files'])): ?>
                                <div class="post-files">
                                    <?php foreach ($post['files'] as $file): ?>
                                        <?php if (in_array($file['file_type'], ['image/jpeg', 'image/png', 'image/gif'])): ?>
                                            <img src="<?php echo htmlspecialchars($file['file_path']); ?>" alt="Image">
                                        <?php else: ?>
                                            <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank">
                                                <?php echo htmlspecialchars($file['file_name']); ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 