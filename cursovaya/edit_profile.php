<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/User.php';

// Проверяем авторизацию
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Получаем данные пользователя
$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user_data = $user->getUserById($_SESSION['user_id']);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update_data = [
        'user_id' => $_SESSION['user_id'],
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
        'bio' => trim($_POST['bio'])

        
    ];

    // Обработка загруженного аватара
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $image_info = getimagesize($_FILES['avatar']['tmp_name']);
        if ($image_info !== false) {
            $update_data['avatar_image'] = base64_encode(file_get_contents($_FILES['avatar']['tmp_name']));
        }
    }

    // Обработка нового пароля
    if (!empty($_POST['new_password'])) {
        $update_data['new_password'] = $_POST['new_password'];
    }

    if ($user->updateProfile($update_data)) {
        $success = "Профиль успешно обновлен";
        $user_data = $user->getUserById($_SESSION['user_id']);
    } else {
        $error = "Ошибка при обновлении профиля";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактирование профиля</title>
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

    <div class="edit-profile">
        <h2>Редактирование профиля</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="avatar-section">
                <?php if (!empty($user_data['avatar_image'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo $user_data['avatar_image']; ?>" 
                         alt="Avatar" class="current-avatar">
                <?php endif; ?>
                <div class="form-group">
                    <label for="avatar">Аватар:</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="first_name">Имя:</label>
                <input type="text" id="first_name" name="first_name" 
                       value="<?php echo htmlspecialchars($user_data['first_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" id="last_name" name="last_name" 
                       value="<?php echo htmlspecialchars($user_data['last_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="bio">О себе:</label>
                <textarea id="bio" name="bio"><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="new_password">Новый пароль (оставьте пустым, если не хотите менять):</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-actions">
                <button type="submit">Сохранить изменения</button>
                <a href="profile.php" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html> 