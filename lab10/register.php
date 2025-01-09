<?php
require_once 'config/database.php';
require_once 'models/User.php';

$page_title = 'Регистрация';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];
    
    $result = $user->register();
    if($result['success']) {
        header("Location: login.php");
        exit;
    } else {
        $error = $result['message'];
    }
}

require_once 'includes/header.php';
?>

<div class="auth-container">
    <h2>Регистрация</h2>
    
    <?php if ($error): ?>
        <div class="auth-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="auth-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="first_name">Имя:</label>
            <input type="text" id="first_name" name="first_name">
        </div>
        
        <div class="form-group">
            <label for="last_name">Фамилия:</label>
            <input type="text" id="last_name" name="last_name">
        </div>
        
        <button type="submit">Зарегистрироваться</button>
    </form>
    
    <div class="auth-links">
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</div> 