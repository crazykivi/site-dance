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

$result = $connection->query("SELECT styles.namestyles, schedule.dayschedule, schedule.timeschedule, choreographers.namechoreographer
                              FROM schedule 
                              JOIN styles ON styles.idstyles = schedule.idstyles
                              JOIN choreographers ON choreographers.idchoreographer = schedule.idchoreographer");
$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}

echo json_encode($schedules);

$connection->close();
?>
