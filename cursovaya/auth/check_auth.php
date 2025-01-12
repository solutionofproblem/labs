<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;
}

function checkUserAccess($user_id) {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id;
}
?> 