<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Добавляем колонку avatar_image
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar_image LONGTEXT COMMENT 'Base64 encoded avatar image'");
    
    // Добавляем колонку bio
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT COMMENT 'User biography'");
    
    // Добавляем колонку social_links
    $db->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS social_links JSON COMMENT 'Social media links'");
    
    echo "База данных успешно обновлена!";
} catch(PDOException $e) {
    echo "Ошибка обновления базы данных: " . $e->getMessage();
} 