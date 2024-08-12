<?php
$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['group_id'])) {
        $groupId = $_GET['group_id'];
        $stmt = $pdo->prepare("SELECT s.fullname, s.phone FROM group_members gm
                               JOIN students s ON gm.idstudent = s.idstudent
                               WHERE gm.idgroup = :groupId");
        $stmt->execute(['groupId' => $groupId]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($students);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
