<?php
header('Content-Type: application/json');

$host = 'localhost'; // Адрес сервера
$database = 'dance-studio'; // Имя базы данных
$user = 'root'; // Имя пользователя
$password = ''; // Пароль

$connection = new mysqli($host, $user, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$result = $connection->query("SELECT namestyles, descriptionstyles FROM styles");
$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode($schedules);

$connection->close();
