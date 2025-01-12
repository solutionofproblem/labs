<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$users = $user->getAllUsers();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Главная</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-left">
                <a href="index.php">Главная</a>
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php">Мой профиль</a>
                <?php endif; ?>
            </div>
            <div class="nav-right">
                <?php if (isLoggedIn()): ?>
                    <?php if(isAdmin()): ?>
                        <a href="admin/users.php">Управление</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-logout">Выйти</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Войти</a>
                    <a href="register.php" class="btn-register">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="users-grid">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <div class="user-avatar">
                    <?php if (!empty($user['avatar_image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo $user['avatar_image']; ?>" 
                             alt="<?php echo htmlspecialchars($user['username']); ?>" 
                             class="avatar-image">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <h2 class="user-name"><?php echo htmlspecialchars($user['username']); ?></h2>
                <a href="view_profile.php?id=<?php echo $user['user_id']; ?>" 
                   class="view-profile-btn">
                    Просмотреть профиль
                </a>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html> 