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

// Проверяем права на редактирование
if (!$post_data || ($post_data['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
    header('Location: profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_file'])) {
        // Удаление файла
        $post->deleteFile($_POST['delete_file']);
        header('Location: edit_post.php?id=' . $_GET['id']);
        exit();
    } else {
        // Обновление поста
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

        if ($post->updatePost(
            $_GET['id'],
            $_SESSION['user_id'],
            trim($_POST['title']),
            trim($_POST['content']),
            $_POST['category'],
            $files
        )) {
            header('Location: profile.php');
            exit();
        }
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
        <form method="POST" enctype="multipart/form-data">
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

            <!-- Текущие изображения -->
            <?php if (!empty($post_data['files'])): ?>
                <div class="current-files">
                    <h3>Текущие изображения:</h3>
                    <div class="files-grid">
                        <?php foreach ($post_data['files'] as $file): ?>
                            <div class="file-item">
                                <?php if (in_array($file['file_type'], ['image/jpeg', 'image/png', 'image/gif'])): ?>
                                    <img src="data:<?php echo $file['file_type']; ?>;base64,<?php echo $file['file_content']; ?>" 
                                         alt="Image" class="post-image">
                                    <form method="POST" class="delete-file-form">
                                        <input type="hidden" name="delete_file" value="<?php echo $file['file_id']; ?>">
                                        <button type="submit" class="delete-file">Удалить</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Загрузка новых изображений -->
            <div>
                <label for="files">Добавить изображения (макс. 5MB):</label>
                <input type="file" id="files" name="files[]" multiple accept="image/jpeg,image/png,image/gif">
                <div class="progress-bar" style="display: none;">
                    <div class="progress"></div>
                </div>
                <div id="uploadProgress"></div>
            </div>
            
            <div class="form-actions">
                <button type="submit">Сохранить изменения</button>
                <a href="profile.php" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>

    <script>
    // Тот же JavaScript код для предпросмотра и сжатия изображений, что и в profile.php
    </script>
</body>
</html> 