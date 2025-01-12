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
            <?php if (!empty($user_data['avatar_image'])): ?>
                <div class="avatar-container">
                    <img src="data:image/jpeg;base64,<?php echo $user_data['avatar_image']; ?>" 
                         alt="Avatar" class="profile-avatar">
                </div>
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($user_data['first_name']); ?></h2>
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
                        <label for="files">Изображения (макс. 5MB):</label>
                        <input type="file" id="files" name="files[]" multiple accept="image/jpeg,image/png,image/gif">
                        <div class="progress-bar" style="display: none;">
                            <div class="progress"></div>
                        </div>
                        <div id="uploadProgress"></div>
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
                                        <img src="data:<?php echo $file['file_type']; ?>;base64,<?php echo $file['file_content']; ?>" 
                                             alt="Image" class="post-image">
                                    <?php else: ?>
                                        <a href="data:<?php echo $file['file_type']; ?>;base64,<?php echo $file['file_content']; ?>" 
                                           download="<?php echo htmlspecialchars($file['file_name']); ?>">
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

    <script>
    document.getElementById('files').onchange = async function(e) {
        const preview = document.createElement('div');
        preview.className = 'upload-preview';
        
        // Очищаем предыдущий предпросмотр
        const oldPreview = document.querySelector('.upload-preview');
        if (oldPreview) oldPreview.remove();
        
        for (let file of this.files) {
            // Проверяем тип файла
            if (!file.type.startsWith('image/')) {
                continue;
            }
            
            // Проверяем размер
            if (file.size > 5 * 1024 * 1024) {
                alert('Файл ' + file.name + ' слишком большой. Максимальный размер 5MB');
                continue;
            }
            
            // Создаем контейнер для изображения
            const imgContainer = document.createElement('div');
            imgContainer.className = 'preview-container';
            
            const img = document.createElement('img');
            img.className = 'upload-preview-image';
            
            // Добавляем кнопку удаления
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-preview';
            deleteBtn.innerHTML = '×';
            deleteBtn.onclick = function(e) {
                e.preventDefault();
                imgContainer.remove();
            };
            
            // Сжимаем изображение
            const compressedImage = await compressImage(file);
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(compressedImage);
            
            imgContainer.appendChild(img);
            imgContainer.appendChild(deleteBtn);
            preview.appendChild(imgContainer);
        }
        
        this.parentNode.appendChild(preview);
    };

    // Функция сжатия изображения
    async function compressImage(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    
                    // Максимальные размеры
                    const MAX_WIDTH = 1200;
                    const MAX_HEIGHT = 1200;
                    
                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    canvas.toBlob((blob) => {
                        resolve(new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        }));
                    }, 'image/jpeg', 0.7); // Качество 0.7
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Добавляем обработку отправки формы
    document.querySelector('form').onsubmit = function(e) {
        const progressBar = document.querySelector('.progress-bar');
        const progress = document.querySelector('.progress');
        
        progressBar.style.display = 'block';
        let width = 0;
        
        const interval = setInterval(() => {
            if (width >= 100) {
                clearInterval(interval);
            } else {
                width++;
                progress.style.width = width + '%';
            }
        }, 10);
    };
    </script>
</body>
</html> 