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
$social_links = json_decode($user_data['social_links'] ?? '{}', true);
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
            <?php if (!empty($user_data['avatar_image'])): ?>
                <div class="avatar-container">
                    <img src="data:image/jpeg;base64,<?php echo $user_data['avatar_image']; ?>" 
                         alt="Avatar" class="profile-avatar">
                </div>
            <?php endif; ?>

            <h2>Профиль пользователя <?php echo htmlspecialchars($user_data['username']); ?></h2>
            <p>Имя: <?php echo htmlspecialchars($user_data['first_name']); ?></p>
            <p>Фамилия: <?php echo htmlspecialchars($user_data['last_name']); ?></p>
            
            <?php if (!empty($user_data['bio'])): ?>
                <div class="bio-section">
                    <h3>О себе</h3>
                    <p><?php echo nl2br(htmlspecialchars($user_data['bio'])); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($social_links)): ?>
                <div class="social-links-section">
                    <h3>Социальные сети</h3>
                    <?php foreach ($social_links as $platform => $url): ?>
                        <?php if (!empty($url)): ?>
                            <a href="<?php echo htmlspecialchars($url); ?>" 
                               target="_blank" 
                               class="social-link <?php echo $platform; ?>">
                                <?php echo ucfirst($platform); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                                            <img src="data:<?php echo $file['file_type']; ?>;base64,<?php echo $file['file_content']; ?>" 
                                                 alt="Image" class="post-image">
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