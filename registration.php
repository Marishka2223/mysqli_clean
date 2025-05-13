<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>КиноМан - Регистрация</title>
</head>
<body>
    <header class="header" style="display: flex; align-items: center; justify-content: center">
    </header>
    <main style="display: flex; align-items: center; justify-content: center; flex-direction: column; text-decoration: none;">
        <h1>Регистрация</h1>
        <form method="post" id="form">
            <div class="contBlur">
                <input type="text" name="login" id="login" placeholder="Логин" pattern="^[A-Za-z]{3,}" required>
            </div>
            <br>
            <div>
                <input type="text" name="name" id="name" placeholder="Имя" required>
            </div>
            <br>
            <div>
                <input type="email" name="email" id="email" placeholder="Почта" required>
            </div>
            <br>
            <div>
                <input type="tel" name="phone" id="phone" pattern="/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/" placeholder="+7(XXX)-XXX-XX-XX" required>
            </div>
            <br>
            <div>
                <input type="password" name="password" id="password" placeholder="Пароль" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$" required>
            </div>
            <br>
            <br>
            <div>
                <input type="submit" value="Зарегистрироваться">
            </div>
        </form>

        <section id="links">
            <a class="reg" href="./index.php"><h2 class="text t2">Войти</h2></a>
        </section>

        <section id="err">
        </section>
    </main>
    <footer></footer>
</body>
</html>

<?php

if(isset($_POST['login']) && isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['password']) && isset($_POST['email'])){


    $conn = new mysqli("MySQL-8.0","root","","cleaning_portal");

    if($conn->connect_error){
        die("Ошибка:".$conn->connect_error);
    }

$login=$conn->real_escape_string($_POST["login"]);
$name=$conn->real_escape_string($_POST["name"]);
$email=$conn->real_escape_string($_POST["email"]);
$phone=$conn->real_escape_string($_POST["phone"]);
$password=$conn->real_escape_string($_POST["password"]);
$hashed_pass = password_hash($password, PASSWORD_DEFAULT);


$sql="INSERT INTO users (login, password, full_name, phone, email) VALUES ('$login', '$hashed_pass', '$name', '$phone','$email')";
if($conn->query($sql)){
    header("Location: index.php");
}
else{
    echo "Ошибка: ".$conn->error;
}
$conn->close();
}