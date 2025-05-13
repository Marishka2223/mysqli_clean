<?php
session_start();
require '../config.php';

// Проверка авторизации администратора
if (!isset($_SESSION['id_user']) || $_SESSION['login'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Получение данных заявки
$request_id = (int)($_GET['id'] ?? 0);
$request = $mysqli->query("
    SELECT r.*, u.full_name, u.login 
    FROM requests r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.request_id = $request_id
")->fetch_assoc();

if (!$request) {
    header("Location: admin.php");
    exit();
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $mysqli->real_escape_string($_POST['address'] ?? $request['address']);
    $phone = $mysqli->real_escape_string($_POST['phone'] ?? $request['phone']);
    $service_type = $mysqli->real_escape_string($_POST['service_type'] ?? $request['service_type']);
    $custom_service = $mysqli->real_escape_string($_POST['custom_service'] ?? $request['custom_service']);
    $payment_type = $mysqli->real_escape_string($_POST['payment_type'] ?? $request['payment_type']);
    $desired_datetime = $mysqli->real_escape_string($_POST['desired_datetime'] ?? $request['desired_datetime']);
    $status = $mysqli->real_escape_string($_POST['status'] ?? $request['status']);
    $admin_comment = $mysqli->real_escape_string($_POST['admin_comment'] ?? $request['admin_comment']);

    $sql = "UPDATE requests SET 
            address = '$address', 
            phone = '$phone', 
            service_type = '$service_type', 
            custom_service = '$custom_service', 
            payment_type = '$payment_type', 
            desired_datetime = '$desired_datetime', 
            status = '$status', 
            admin_comment = '$admin_comment' 
            WHERE request_id = $request_id";
    
    $mysqli->query($sql);
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование заявки #<?= $request['request_id'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; }
        textarea { height: 100px; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        #custom-service-group { display: none; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceType = document.querySelector('select[name="service_type"]');
            const customService = document.getElementById('custom-service-group');
            
            function toggleService() {
                customService.style.display = serviceType.value === 'other' ? 'block' : 'none';
            }
            
            toggleService();
            serviceType.addEventListener('change', toggleService);
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Редактирование заявки #<?= $request['request_id'] ?></h1>
        <a href="admin.php" class="back">← Назад</a>
        
        <form method="post">
            <div class="form-group">
                <label>Адрес:</label>
                <input type="text" name="address" value="<?= $request['address'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Телефон:</label>
                <input type="text" name="phone" value="<?= $request['phone'] ?>" required>
            </div>
            
            <div class="form-group">
                <label>Тип услуги:</label>
                <select name="service_type" required>
                    <option value="general" <?= $request['service_type'] == 'general' ? 'selected' : '' ?>>Генеральная уборка</option>
                    <option value="deep" <?= $request['service_type'] == 'deep' ? 'selected' : '' ?>>Глубокая уборка</option>
                    <option value="post_construction" <?= $request['service_type'] == 'post_construction' ? 'selected' : '' ?>>Послестроительная уборка</option>
                    <option value="carpet" <?= $request['service_type'] == 'carpet' ? 'selected' : '' ?>>Химчистка ковров</option>
                    <option value="other" <?= $request['service_type'] == 'other' ? 'selected' : '' ?>>Другое</option>
                </select>
            </div>
            
            <div class="form-group" id="custom-service-group">
                <label>Описание услуги:</label>
                <input type="text" name="custom_service" value="<?= $request['custom_service'] ?>">
            </div>
            
            <div class="form-group">
                <label>Способ оплаты:</label>
                <select name="payment_type" required>
                    <option value="cash" <?= $request['payment_type'] == 'cash' ? 'selected' : '' ?>>Наличные</option>
                    <option value="card" <?= $request['payment_type'] == 'card' ? 'selected' : '' ?>>Карта</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дата/время:</label>
                <input type="datetime-local" name="desired_datetime" value="<?= date('Y-m-d\TH:i', strtotime($request['desired_datetime'])) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Статус:</label>
                <select name="status" required>
                    <option value="new" <?= $request['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                    <option value="in_progress" <?= $request['status'] == 'in_progress' ? 'selected' : '' ?>>В процессе</option>
                    <option value="completed" <?= $request['status'] == 'completed' ? 'selected' : '' ?>>Завершена</option>
                    <option value="canceled" <?= $request['status'] == 'canceled' ? 'selected' : '' ?>>Отменена</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Комментарий:</label>
                <textarea name="admin_comment"><?= $request['admin_comment'] ?></textarea>
            </div>
            
            <button type="submit">Сохранить</button>
        </form>
    </div>
</body>
</html>