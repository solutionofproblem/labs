<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/Post.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$post = new Post($db);

if (!isset($_GET['id'])) {
    header('Location: profile.php');
    exit();
}

$post_data = $post->getPostById($_GET['id']);

// Проверяем, принадлежит ли пост пользователю или является ли пользователь админом
if (!$post_data || ($post_data['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
    header('Location: profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($post->updatePost(
        $_GET['id'],
        $_SESSION['user_id'],
        trim($_POST['title']),
        trim($_POST['content']),
        $_POST['category']
    )) {
        header('Location: profile.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактировать пост</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Главная</a>
            <a href="profile.php">Мой профиль</a>
            <a href="logout.php" class="btn-logout">Выйти</a>
        </nav>
    </header>

    <div class="edit-post">
        <h2>Редактировать пост</h2>
        <form method="POST">
            <div>
                <label for="title">Заголовок:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post_data['title']); ?>" required>
            </div>
            
            <div>
                <label for="content">Содержание:</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($post_data['content']); ?></textarea>
            </div>
            
            <div>
                <label for="category">Категория:</label>
                <select id="category" name="category" required>
                    <?php foreach ($post->getAllCategories() as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>"
                            <?php echo ($post_data['category'] == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit">Сохранить изменения</button>
                <a href="profile.php" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html> 