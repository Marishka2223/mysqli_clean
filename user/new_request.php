<?php
session_start();
require '../config.php';

// Проверка авторизации
if (empty($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
}

// Получаем телефон пользователя (максимально упрощенно)
$user_id = $_SESSION['id_user'];
$user_phone = $mysqli->query("SELECT phone FROM users WHERE user_id = '$user_id'")->fetch_object()->phone ?? '';

// Обработка формы
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем данные формы
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $service_type = $_POST['service_type'];
    $custom_service = trim($_POST['custom_service'] ?? '');
    $payment_type = $_POST['payment_type'];
    $datetime = $_POST['datetime'];
    
    // Проверяем ошибки
    if (empty($address)) $error = "Укажите адрес";
    elseif (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) $error = "Неверный формат телефона";
    elseif ($service_type == 'other' && empty($custom_service)) $error = "Опишите услугу";
    elseif (strtotime($datetime) < time()) $error = "Укажите будущую дату";
    
    // Если нет ошибок - сохраняем
    if (empty($error)) {
        $sql = "INSERT INTO requests (user_id, address, phone, service_type, custom_service, payment_type, desired_datetime)
                VALUES ('$user_id', '$address', '$phone', '$service_type', '$custom_service', '$payment_type', '$datetime')";
        
        if ($mysqli->query($sql)) {
            header("Location: requests.php?success=1");
            exit();
        } else {
            $error = "Ошибка при сохранении заявки";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая заявка</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 0 auto; padding: 20px; }
        .error { color: red; margin: 10px 0; }
        input, select, textarea { width: 100%; padding: 10px; margin: 5px 0; }
        button { background: #4CAF50; color: white; border: none; padding: 10px; cursor: pointer; }
        .hidden { display: none; }
    </style>
    <script>
        function toggleService() {
            document.getElementById('custom-service').style.display = 
                (document.getElementById('service_type').value == 'other') ? 'block' : 'none';
        }
        window.onload = toggleService;
    </script>
</head>
<body>
    <h1>Новая заявка</h1>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <input type="text" name="address" placeholder="Адрес" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        
        <input type="tel" name="phone" placeholder="Телефон (+7(XXX)-XXX-XX-XX)" 
               pattern="^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$" required
               value="<?= htmlspecialchars(($_POST['phone'] ?? $user_phone)) ?>">
        
        <select id="service_type" name="service_type" onchange="toggleService()" required>
            <option value="">Выберите услугу</option>
            <option value="general">Общий клининг</option>
            <option value="deep">Генеральная уборка</option>
            <option value="post_construction">Послестроительная уборка</option>
            <option value="carpet">Химчистка ковров</option>
            <option value="other">Другая услуга</option>
        </select>
        
        <div id="custom-service" class="hidden">
            <textarea name="custom_service" placeholder="Опишите услугу"><?= htmlspecialchars($_POST['custom_service'] ?? '') ?></textarea>
        </div>
        
        <input type="datetime-local" name="datetime" required value="<?= htmlspecialchars($_POST['datetime'] ?? '') ?>">
        
        <select name="payment_type" required>
            <option value="">Способ оплаты</option>
            <option value="cash">Наличные</option>
            <option value="card">Карта</option>
        </select>
        
        <button type="submit">Создать заявку</button>
    </form>
    
    <p><a href="requests.php">← Мои заявки</a></p>
</body>
</html>