<?php
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'auth/check_auth.php';

// Если пользователь уже авторизован, перенаправляем на профиль
if (isLoggedIn()) {
    header("Location: profile.php");
    exit;
}

$page_title = 'Вход';
$error = '';

// Обрабатываем форму до подключения header.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $login_result = $user->login($_POST['username'], $_POST['password']);
    
    if($login_result) {
        $_SESSION['user_id'] = $login_result['user_id'];
        $_SESSION['username'] = $login_result['username'];
        $_SESSION['role_id'] = $login_result['role_id'];
        
        header("Location: profile.php");
        exit;
    } else {
        $error = "Неверное имя пользователя или пароль";
    }
}

// Подключаем header после обработки формы
require_once 'includes/header.php';
?>

<div class="auth-container">
    <h2>Вход в систему</h2>
    
    <?php if ($error): ?>
        <div class="auth-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Войти</button>
    </form>
    
    <div class="auth-links">
        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</div> 