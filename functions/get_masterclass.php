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

$currentDate = date('Y-m-d H:i:s');
$result = $connection->query("SELECT styles.namestyles, masterclass.namemasterclass, masterclass.descriptionmasterclass, masterclass.dateventmasterclass, masterclass.nameimgmasterclass
                              FROM masterclass 
                              JOIN styles ON styles.idstyles = masterclass.idstyles
                              WHERE masterclass.dateventmasterclass > '$currentDate'");
$masterclasses = [];
while ($row = $result->fetch_assoc()) {
    // Формирование полного пути к изображению
    $imagePath = 'img/master-class/' . $row['nameimgmasterclass'];
    // Добавление пути к изображению в массив
    $row['imagePath'] = $imagePath;
    $masterclasses[] = $row;
}

echo json_encode($masterclasses);

$connection->close();
?>