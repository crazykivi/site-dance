<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dance-studio";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Получаем данные из JSON тела запроса
$input = json_decode(file_get_contents('php://input'), true);

$fullName = $conn->real_escape_string($input['fullName']);
$birthDate = $conn->real_escape_string($input['birthDate']);
$phone = $conn->real_escape_string($input['phone']);
$forWhom = $conn->real_escape_string(implode(', ', $input['forWhom']));
$dancedBefore = $conn->real_escape_string($input['dancedBefore']);
$directions = $conn->real_escape_string($input['directions']);
$additional = $conn->real_escape_string($input['additional']);

$sql = "INSERT INTO registrations (fullName, birthDate, phone, forWhom, dancedBefore, idstyles, additional)
VALUES ('$fullName', '$birthDate', '$phone', '$forWhom', '$dancedBefore', '$directions', '$additional')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => "Заявка оставлена"]);
} else {
    echo json_encode(['error' => "Ошибка базы данных: " . $conn->error]);
}

$conn->close();