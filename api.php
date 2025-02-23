<?php
header('Content-Type: application/json');
require 'db_connect.php';

$action = $_GET['action'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch($action) {
        case 'get_channels':
            $stmt = $pdo->query("SELECT * FROM channels");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'get_channel':
            $code = $_GET['code'];
            $stmt = $pdo->prepare("SELECT * FROM channels WHERE code = ?");
            $stmt->execute([$code]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            break;

        case 'add_channel':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO channels (code, name, link, icon) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['code'], $data['name'], $data['link'], $data['icon']]);
            echo json_encode(['status' => 'success']);
            break;

        case 'delete_channel':
            $id = $_GET['id'];
            $stmt = $pdo->prepare("DELETE FROM channels WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success']);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
