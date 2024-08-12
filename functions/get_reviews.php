<?php
$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $reviewsStmt = $pdo->prepare("SELECT r.*, c.namechoreographer 
    FROM reviews r
    JOIN choreographers c ON r.idchoreographer = c.idchoreographer
    WHERE r.approval = 'Одобрено'
    ORDER BY r.idreview DESC
    LIMIT 3");

    $reviewsStmt->execute();
    $reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($reviews) {
        echo json_encode($reviews);
    } else {
        echo json_encode(['success' => false, 'message' => 'No reviews found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
