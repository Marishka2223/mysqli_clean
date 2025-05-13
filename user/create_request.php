<?php
session_start();
require '../config.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Получение заявок пользователя (упрощенный вариант без bind_param)
$userId = $_SESSION['id_user'];
$query = "SELECT * FROM requests WHERE user_id = '" . $mysqli->real_escape_string($userId) . "' ORDER BY desired_datetime DESC";
$result = $mysqli->query($query);

$requests = [];
if ($result) {
    $requests = $result->fetch_all(MYSQLI_ASSOC);
}

$serviceTypes = [
    'general' => 'Общий клининг',
    'deep' => 'Генеральная уборка',
    'post_construction' => 'Послестроительная уборка',
    'carpet' => 'Химчистка ковров и мебели',
    'other' => 'Иная'
];

$statuses = [
    'new' => 'Новая',
    'in_progress' => 'В работе',
    'completed' => 'Выполнено',
    'canceled' => 'Отменено'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .request-card { border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .status { padding: 3px 8px; border-radius: 4px; color: white; font-size: 0.9em; }
        .new { background: #4CAF50; } .in_progress { background: #2196F3; }
        .completed { background: #9E9E9E; } .canceled { background: #f44336; }
        .comment { margin-top: 8px; color: #555; font-style: italic; }
        header { text-align: right; margin-bottom: 20px; }
        .success { color: green; padding: 10px; background: #e8f5e9; margin: 10px 0; }
        .new-request-btn { display: inline-block; margin-bottom: 20px; padding: 8px 15px; 
                          background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <header><a href="../session_destroy.php">Выйти</a></header>
    
    <h2>Мои заявки</h2>
    <?php if (isset($_GET['success'])): ?>
        <div class="success">Заявка успешно создана!</div>
    <?php endif; ?>
    
    <a href="new_request.php" class="new-request-btn">➕ Новая заявка</a>

    <?php foreach ($requests as $request): ?>
        <div class="request-card">
            <p><strong>Адрес:</strong> <?= htmlspecialchars($request['address']) ?></p>
            <p><strong>Услуга:</strong> 
                <?= $serviceTypes[$request['service_type']] ?>
                <?= $request['service_type'] === 'other' ? ': ' . htmlspecialchars($request['custom_service']) : '' ?>
            </p>
            <p><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($request['desired_datetime'])) ?></p>
            <p>
                <strong>Статус:</strong> 
                <span class="status <?= $request['status'] ?>">
                    <?= $statuses[$request['status']] ?>
                </span>
            </p>
            <?php if (!empty($request['admin_comment']) && $request['status'] !== 'new'): ?>
                <p class="comment">
                    <strong>Комментарий администратора:</strong> 
                    <?= htmlspecialchars($request['admin_comment']) ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>