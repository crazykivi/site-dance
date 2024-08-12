<?php
header('Content-Type: application/json');

// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "dance-studio");

// Проверка соединения
if ($mysqli->connect_error) {
    die(json_encode(['error' => 'Ошибка подключения к базе данных: ' . $mysqli->connect_error]));
}

// Запрос к базе данных для получения списка хореографов и их стилей
$sql = "
    SELECT 
        c.namechoreographer AS name, 
        c.experiencechoreographer AS experience,
        COALESCE(GROUP_CONCAT(s.namestyles SEPARATOR ', '), 'Стили не указаны') AS styles
    FROM choreographers c
    LEFT JOIN choreographers_style cs ON c.idchoreographer = cs.idchoreographer
    LEFT JOIN styles s ON cs.idstyles = s.idstyles
    GROUP BY c.idchoreographer
";
$result = $mysqli->query($sql);

$instructors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
}

// Закрытие соединения с базой данных
$mysqli->close();

echo json_encode($instructors);
