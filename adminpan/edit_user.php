<?php
session_start();
require '../config.php';

// Проверка авторизации администратора
if (!isset($_SESSION['id_user']) || $_SESSION['login'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Получение данных пользователя
$user_id = (int)($_GET['id'] ?? 0);
$user = $mysqli->query("SELECT * FROM users WHERE user_id = $user_id")->fetch_assoc();

if (!$user) {
    header("Location: admin.php?error=user_not_found");
    exit();
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $mysqli->real_escape_string(trim($_POST['full_name']));
    $phone = $mysqli->real_escape_string(trim($_POST['phone']));
    $email = $mysqli->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password'] ?? '');

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET 
                full_name = '$full_name', 
                phone = '$phone', 
                email = '$email', 
                password = '$hashed_password' 
                WHERE user_id = $user_id";
    } else {
        $sql = "UPDATE users SET 
                full_name = '$full_name', 
                phone = '$phone', 
                email = '$email' 
                WHERE user_id = $user_id";
    }

    $mysqli->query($sql);
    header("Location: admin.php?success=user_updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #333; }
        .error { color: red; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Редактирование пользователя</h1>
        <a href="admin.php" class="back">← Назад</a>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error">Ошибка при обновлении</div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" value="<?= $user['login'] ?>" disabled>
            </div>
            
            <div class="form-group">
                <label>ФИО:</label>
                <input type="text" name="full_name" value="<?= $user['full_name'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Телефон:</label>
                <input type="text" name="phone" value="<?= $user['phone'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Новый пароль:</label>
                <input type="password" name="password" placeholder="Оставьте пустым">
            </div>
            
            <button type="submit">Сохранить</button>
        </form>
    </div>
</body>
</html>