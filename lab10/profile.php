<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Post.php';

// Проверяем авторизацию
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Получаем данные пользователя
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$post = new Post($db);
$user_data = $user->getUserById($_SESSION['user_id']);
$user_posts = $post->getUserPosts($_SESSION['user_id']);

// В начале файла после получения данных пользователя
if (isset($_POST['delete_post'])) {
    $post_id = $_POST['delete_post'];
    if ($post->deletePost($post_id, $_SESSION['user_id'])) {
        // Обновляем список постов после удаления
        $user_posts = $post->getUserPosts($_SESSION['user_id']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Профиль</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Главная</a>
            <a href="edit_profile.php" class="edit-btn">Редактировать профиль</a>
            <?php if(isAdmin()): ?>
                <a href="admin/users.php">Управление пользователями</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout">Выйти</a>
        </nav>
    </header>

    <div class="profile">
        <div class="profile-info">
            <h2>Профиль пользователя</h2>
            <p>Имя пользователя: <?php echo htmlspecialchars($user_data['username']); ?></p>
            <p>Email: <?php echo htmlspecialchars($user_data['email']); ?></p>
            <p>Имя: <?php echo htmlspecialchars($user_data['first_name']); ?></p>
            <p>Фамилия: <?php echo htmlspecialchars($user_data['last_name']); ?></p>
        </div>

        <div class="profile-wall">
            <div class="create-post">
                <h3>Создать новый пост</h3>
                <form action="create_post.php" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="title">Заголовок:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div>
                        <label for="content">Содержание:</label>
                        <textarea id="content" name="content" required></textarea>
                    </div>
                    <div>
                        <label for="category">Категория:</label>
                        <select id="category" name="category" required>
                            <?php foreach ($post->getAllCategories() as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="files">Файлы:</label>
                        <input type="file" id="files" name="files[]" multiple>
                    </div>
                    <button type="submit">Опубликовать</button>
                </form>
            </div>

            <div class="posts-wall">
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
                        
                        <?php if ($post['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                            <div class="post-actions">
                                <a href="edit_post.php?id=<?php echo $post['post_id']; ?>" class="btn-edit">Редактировать</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');">
                                    <input type="hidden" name="delete_post" value="<?php echo $post['post_id']; ?>">
                                    <button type="submit" class="btn-delete">Удалить</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html> 