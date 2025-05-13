<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
</head>
<body>
<div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <h1>Клининг-сервис</h1>
    <br><br><br>

    <form method="post" style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
        <h2>Войти</h2>
        <p>Ваш логин:
            <input type="text" name="login" placeholder="логин" required>
        </p>
        <p>Ваш пароль:
            <input type="password" name="password" placeholder="пароль" required>
        </p>
        <br>
        <input name="submit" type="submit" class="button" value="Войти">
    </form>

    <br>
    <a href="./registration.php">Зарегистрироваться</a>
</div>
</body>
</html>

<?php 
// Обработка формы авторизации
if (isset($_POST["submit"]) && isset($_POST["login"]) && isset($_POST["password"])) {    
    $conn = new mysqli("MySQL-8.0", "root", "", "cleaning_portal");

    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    $log = $conn->real_escape_string($_POST["login"]);
    $pass = $_POST["password"];

    $sql = mysqli_query($conn, "SELECT user_id, login, password FROM users WHERE login='$log'");
    
    if (!$sql) {
        die("Ошибка запроса: " . $conn->error);
    }
    
    $data = mysqli_fetch_assoc($sql);

    if ($data && password_verify($pass, $data["password"])) {
        $_SESSION['id_user'] = $data['user_id'];
        $_SESSION['login'] = $data['login']; // Сохраняем логин в сессии
        
        if ($log === 'admin') {
            header("Location: ../adminpan/admin.php");
        } else {
            header("Location: ../user/create_request.php");
        }
        exit();
    } else {
        die("Неверный логин или пароль");
    }

    $conn->close();
}
?>