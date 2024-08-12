<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dance-studio";

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Запрос для получения уникальных направлений
$sql = "SELECT idstyles, namestyles FROM styles";
$result = $conn->query($sql);
$directions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        //$directions[] = $row['namestyles'];
        $directions[] = [
            'id' => $row['idstyles'],
            'name' => $row['namestyles']
        ];
    }
    echo json_encode($directions);
} else {
    echo json_encode([]);
}

$conn->close();
