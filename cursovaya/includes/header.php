<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../auth/check_auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($page_title) ? $page_title . ' - Портфолио на стене' : 'Портфолио на стене'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="nav-left">
                <a href="<?php echo BASE_URL; ?>/index.php">Главная</a>
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/profile.php">Мой профиль</a>
                <?php endif; ?>
            </div>
            
            <div class="site-title">Портфолио на стене</div>
            
            <div class="nav-right">
                <?php if (isLoggedIn()): ?>
                    <?php if(isAdmin()): ?>
                        <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn-admin">Управление</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-logout">Выйти</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login.php" class="btn-login">Войти</a>
                    <a href="<?php echo BASE_URL; ?>/register.php" class="btn-register">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
</body>
</html> 