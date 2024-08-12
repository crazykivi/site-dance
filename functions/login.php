<?php
/*
$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Установка режима ошибки PDO на исключение
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Не удалось подключиться к базе данных: " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['encrypted-key'])) {
    $encryptedKey = $_POST['encrypted-key'];
    // Подготовка SQL запроса для поиска ключа
    $stmt = $pdo->prepare("SELECT keystaff FROM staff WHERE keystaff = :encryptedKey");
    $stmt->execute(['encryptedKey' => $encryptedKey]);

    // Проверяем, найден ли ключ
    if ($stmt->rowCount() > 0) {
        // Ключ найден, авторизация успешна
        echo json_encode(['success' => true]);
    } else {
        // Ключ не найден, авторизация неудачна
        echo json_encode(['success' => false]);
    }
} else {
    // Нет данных или ошибка в данных
    http_response_code(400); // Некорректный запрос
    echo json_encode(['success' => false, 'message' => 'Некорректный запрос.']);
}*/
session_start(); // Начало сессии или продолжение уже существующей

$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Не удалось подключиться к базе данных: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['encrypted-key'])) {
    $encryptedKey = $_POST['encrypted-key'];
    $stmt = $pdo->prepare("SELECT keystaff FROM staff WHERE keystaff = :encryptedKey");
    $stmt->execute(['encryptedKey' => $encryptedKey]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['user_authenticated'] = true; // Записываем в сессию, что пользователь авторизован
        $_SESSION['user_key'] = $encryptedKey; // Также можно сохранить ключ в сессии, если это необходимо

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Некорректный запрос.']);
}
