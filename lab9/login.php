<?php
require_once 'config/database.php';
require_once 'models/User.php';

session_start();
$message = '';
$messageType = '';

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
        $message = "Неверное имя пользователя или пароль";
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
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
    <h2>Вход в систему</h2>
    
    <?php if($message): ?>
        <div class="<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Войти</button>
    </form>
    
    <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
</body>
</html> 