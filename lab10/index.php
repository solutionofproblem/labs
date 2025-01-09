<?php
require_once 'auth/check_auth.php';
require_once 'config/database.php';
require_once 'models/User.php';

$page_title = 'Главная';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$users = $user->getAllUsers();

require_once 'includes/header.php';
?>

<main>
    <div class="users-grid">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p>
                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </p>
                <a href="view_profile.php?id=<?php echo $user['user_id']; ?>" class="btn">
                    Просмотреть профиль
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html> 