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
    $user->user_id = $_SESSION['user_id'];
    $user->username = trim($_POST['username']);
    $user->email = trim($_POST['email']);
    $user->first_name = trim($_POST['first_name']);
    $user->last_name = trim($_POST['last_name']);
    
    // Проверка на заполнение обязательных полей
    if (empty($user->username) || empty($user->email)) {
        $error = "Имя пользователя и email обязательны для заполнения";
    } else {
        // Проверяем, не занят ли email другим пользователем
        if ($user->email !== $user_data['email'] && $user->emailExists($user->email)) {
            $error = "Этот email уже используется";
        } else {
            // Если указан новый пароль
            if (!empty($_POST['new_password'])) {
                if (strlen($_POST['new_password']) < 6) {
                    $error = "Пароль должен быть не менее 6 символов";
                } else {
                    $user->password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                }
            }
            
            if (!isset($error) && $user->update()) {
                $success = "Профиль успешно обновлен";
                // Обновляем данные пользователя после успешного обновления
                $user_data = $user->getUserById($_SESSION['user_id']);
            } else {
                $error = "Ошибка при обновлении профиля";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактирование профиля</title>
    <style>
        .profile-form {
            margin: 20px;
            max-width: 500px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="profile-form">
        <h2>Редактирование профиля</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="first_name">Имя:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="new_password">Новый пароль (оставьте пустым, если не хотите менять):</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            
            <div class="form-group">
                <button type="submit">Сохранить изменения</button>
                <a href="profile.php">Вернуться к профилю</a>
            </div>
        </form>
    </div>
</body>
</html> 