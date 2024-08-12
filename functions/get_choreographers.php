<?php
$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $choreographersStmt = $pdo->prepare("SELECT idchoreographer, namechoreographer AS name FROM choreographers");
    $choreographersStmt->execute();
    $choreographers = $choreographersStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($choreographers);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
