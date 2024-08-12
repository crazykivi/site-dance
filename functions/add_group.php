<?php
$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $idstyles = $_POST['idstyles'];
        $idchoreographer = $_POST['idchoreographer'];
        $event_day = $_POST['event_day'];

        $stmt = $pdo->prepare("INSERT INTO `groups` (idstyles, idchoreographer, `the day of the event`) VALUES (:idstyles, :idchoreographer, :event_day)");
        $stmt->execute(['idstyles' => $idstyles, 'idchoreographer' => $idchoreographer, 'event_day' => $event_day]);

        $idgroup = $pdo->lastInsertId();

        // Получение данных для отображения
        $groupStmt = $pdo->prepare("SELECT g.*, c.namechoreographer AS instructor_name, s.namestyles FROM `groups` g
                                    JOIN choreographers c ON g.idchoreographer = c.idchoreographer
                                    JOIN styles s ON g.idstyles = s.idstyles
                                    WHERE g.idgroup = :idgroup");
        $groupStmt->execute(['idgroup' => $idgroup]);
        $group = $groupStmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'idgroup' => $idgroup, 'namestyles' => $group['namestyles'], 'instructor_name' => $group['instructor_name']]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
