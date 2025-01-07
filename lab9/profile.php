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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Профиль</title>
    <style>
        .profile {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="profile">
        <h2>Профиль пользователя</h2>
        <p>Имя пользователя: <?php echo htmlspecialchars($user_data['username']); ?></p>
        <p>Email: <?php echo htmlspecialchars($user_data['email']); ?></p>
        <p>Имя: <?php echo htmlspecialchars($user_data['first_name']); ?></p>
        <p>Фамилия: <?php echo htmlspecialchars($user_data['last_name']); ?></p>
        
        <a href="logout.php">Выйти</a>

        <?php if(isAdmin()): ?>
            <p><a href="admin/users.php">Управление пользователями</a></p>
        <?php endif; ?>
    </div>
</body>
</html> 