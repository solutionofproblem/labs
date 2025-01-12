<?php
require_once '../auth/check_auth.php';
require_once '../config/database.php';
require_once '../models/User.php';

// Проверяем права администратора или владельца профиля
if (!isAdmin() && !checkUserAccess($_GET['id'])) {
    header("Location: ../login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';
$messageType = '';

// Получаем данные пользователя
$user_data = $user->getUserById($_GET['id']);

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->user_id = $_GET['id'];
    $user->email = $_POST['email'];
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];
    
    if ($user->update()) {
        $message = "Данные успешно обновлены";
        $messageType = "success";
        $user_data = $user->getUserById($_GET['id']); // Обновляем данные для отображения
    } else {
        $message = "Ошибка при обновлении данных";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактирование пользователя</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        form {
            margin: 20px;
        }
        input {
            display: block;
            margin: 10px 0;
            padding: 5px;
        }
    </style>
</head>
<body>
    <h2>Редактирование пользователя</h2>
    
    <?php if($message): ?>
        <div class="<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>">
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>">
        <button type="submit">Сохранить</button>
    </form>
    
    <p>
        <?php if(isAdmin()): ?>
            <a href="users.php">Вернуться к списку пользователей</a>
        <?php else: ?>
            <a href="../profile.php">Вернуться в профиль</a>
        <?php endif; ?>
    </p>
</body>
</html> 