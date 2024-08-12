<?php
$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $experience = $_POST['experience'];

        $stmt = $pdo->prepare("INSERT INTO choreographers (namechoreographer, experiencechoreographer) VALUES (:name, :experience)");
        $stmt->execute(['name' => $name, 'experience' => $experience]);
        
        $idchoreographer = $pdo->lastInsertId();
        
        echo json_encode(['success' => true, 'idchoreographer' => $idchoreographer]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
