<?php
$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $idchoreographer = $_POST['idchoreographer'];
        $textreview = $_POST['textreview'];

        $stmt = $pdo->prepare("INSERT INTO reviews (idchoreographer, textreview) VALUES (:idchoreographer, :textreview)");
        $stmt->execute(['idchoreographer' => $idchoreographer, 'textreview' => $textreview]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
