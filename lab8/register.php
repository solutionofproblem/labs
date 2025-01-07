<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <meta charset="UTF-8">
    <style>
        .error { color: red; }
        .form-group { margin-bottom: 15px; }
        form { max-width: 400px; margin: 20px auto; }
    </style>
</head>
<body>
<?php
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Валидация данных
    if (empty($_POST['fullname'])) {
        $errors['fullname'] = "ФИО обязательно для заполнения";
    }
    if (empty($_POST['login'])) {
        $errors['login'] = "Логин обязателен для заполнения";
    }
    if (empty($_POST['password'])) {
        $errors['password'] = "Пароль обязателен для заполнения";
    }
    if (empty($_POST['birthdate'])) {
        $errors['birthdate'] = "Дата рождения обязательна для заполнения";
    }
    
    // Если нет ошибок, обрабатываем данные
    if (empty($errors)) {
        // Здесь код для сохранения данных в базу данных
        echo "<div style='color: green;'>Регистрация успешно завершена!</div>";
    }
}
?>

<form method="POST" action="">
    <div class="form-group">
        <label for="fullname">ФИО:</label>
        <input type="text" name="fullname" id="fullname" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
        <?php if (isset($errors['fullname'])) echo "<div class='error'>" . $errors['fullname'] . "</div>"; ?>
    </div>

    <div class="form-group">
        <label for="login">Логин:</label>
        <input type="text" name="login" id="login" value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
        <?php if (isset($errors['login'])) echo "<div class='error'>" . $errors['login'] . "</div>"; ?>
    </div>

    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password">
        <?php if (isset($errors['password'])) echo "<div class='error'>" . $errors['password'] . "</div>"; ?>
    </div>

    <div class="form-group">
        <label for="birthdate">Дата рождения:</label>
        <input type="date" name="birthdate" id="birthdate" value="<?php echo isset($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : ''; ?>">
        <?php if (isset($errors['birthdate'])) echo "<div class='error'>" . $errors['birthdate'] . "</div>"; ?>
    </div>

    <div class="form-group">
        <input type="submit" value="Зарегистрироваться">
    </div>
</form>
</body>
</html>  