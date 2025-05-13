<?php
$host = 'MySQL-8.0';
$dbname = 'cleaning_portal';
$username = 'root';
$password = '';

// Создаем подключение
$mysqli = new mysqli($host, $username, $password, $dbname);

// Проверяем подключение
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Устанавливаем кодировку
$mysqli->set_charset("utf8");
?>