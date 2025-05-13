<?php
session_start();
require '../config.php';

// Проверка авторизации и прав администратора
if (!isset($_SESSION['id_user']) || $_SESSION['login'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Получение списка пользователей
$users = $mysqli->query("SELECT * FROM users")->fetch_all(MYSQLI_ASSOC);

// Получение списка заявок с именами пользователей
$requests = $mysqli->query("
    SELECT r.*, u.full_name, u.login 
    FROM requests r 
    JOIN users u ON r.user_id = u.user_id
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .logout { float: right; text-decoration: none; color: #333; padding: 5px 10px; border: 1px solid #ccc; }
        .edit { color: #2196F3; text-decoration: none; }
        .edit:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Админ-панель</h1>

        <a href="../session_destroy.php" class="logout">Выйти</a>
        
        <h2>Пользователи</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>ФИО</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= $user['login'] ?></td>
                    <td><?= $user['full_name'] ?></td>
                    <td><?= $user['phone'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="edit">Редактировать</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Заявки</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Адрес</th>
                    <th>Телефон</th>
                    <th>Тип услуги</th>
                    <th>Способ оплаты</th>
                    <th>Дата/время</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= $request['request_id'] ?></td>
                    <td><?= $request['full_name'] ?> (<?= $request['login'] ?>)</td>
                    <td><?= $request['address'] ?></td>
                    <td><?= $request['phone'] ?></td>
                    <td>
                        <?= $request['service_type'] ?>
                        <?php if ($request['service_type'] == 'other' && $request['custom_service']): ?>
                            (<?= $request['custom_service'] ?>)
                        <?php endif; ?>
                    </td>
                    <td><?= $request['payment_type'] ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($request['desired_datetime'])) ?></td>
                    <td><?= $request['status'] ?></td>
                    <td>
                        <a href="edit_request.php?id=<?= $request['request_id'] ?>" class="edit">Редактировать</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>