<?php
require_once '../auth/check_auth.php';
require_once '../config/database.php';
require_once '../models/User.php';

// Проверяем права администратора
if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Обработка изменения видимости
if (isset($_POST['toggle_visibility']) && isset($_POST['user_id'])) {
    $user->toggleVisibility($_POST['user_id']);
}

// Получаем список всех пользователей, включая скрытые
$users = $user->getAllUsers(true);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="../index.php">Главная</a>
            <a href="../profile.php">Мой профиль</a>
            <a href="../logout.php" class="btn-logout">Выйти</a>
        </nav>
    </header>

    <div class="admin-panel">
        <h2>Управление пользователями</h2>
        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя пользователя</th>
                        <th>Имя</th>
                        <th>Фамилия</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                            <td>
                                <?php echo $user['is_hidden'] ? 'Скрыт' : 'Виден'; ?>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" name="toggle_visibility" class="btn">
                                        <?php echo $user['is_hidden'] ? 'Показать' : 'Скрыть'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 