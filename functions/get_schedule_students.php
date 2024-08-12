<?php
header('Content-Type: application/json');

if (!isset($_GET['schedule_id'])) {
    echo json_encode(['error' => 'schedule_id not provided']);
    exit;
}

$schedule_id = $_GET['schedule_id'];

$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT s.fullname, s.phone 
                           FROM students s
                           JOIN group_members gm ON s.idstudent = gm.idstudent
                           WHERE gm.idschedule = :schedule_id");
    $stmt->execute(['schedule_id' => $schedule_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($students);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
