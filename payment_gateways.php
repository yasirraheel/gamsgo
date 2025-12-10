<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$db = 'u559276167_gamsgo';
$user = 'u559276167_gamsgo';
$pass = 'Gamsgo@123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

function isAdmin() {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function ensureSchema($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS payment_gateways (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gateway_name VARCHAR(100) NOT NULL,
        gateway_id VARCHAR(255) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        fee_type ENUM('fixed', 'percentage') DEFAULT 'percentage',
        fee_value DECIMAL(10,2) DEFAULT 0.00,
        description TEXT,
        instructions TEXT,
        logo_url VARCHAR(500),
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
}

ensureSchema($pdo);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $stmt = $pdo->query("SELECT * FROM payment_gateways ORDER BY sort_order ASC, id ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'get':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM payment_gateways WHERE id = ?");
        $stmt->execute([$id]);
        $gateway = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($gateway ?: ['error' => 'Gateway not found']);
        break;

    case 'create':
        if (!isAdmin()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO payment_gateways 
            (gateway_name, gateway_id, is_active, fee_type, fee_value, description, instructions, logo_url, sort_order) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['gateway_name'] ?? '',
            $data['gateway_id'] ?? '',
            $data['is_active'] ?? 1,
            $data['fee_type'] ?? 'percentage',
            $data['fee_value'] ?? 0.00,
            $data['description'] ?? '',
            $data['instructions'] ?? '',
            $data['logo_url'] ?? '',
            $data['sort_order'] ?? 0
        ]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'update':
        if (!isAdmin()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE payment_gateways SET 
            gateway_name = ?, gateway_id = ?, is_active = ?, fee_type = ?, 
            fee_value = ?, description = ?, instructions = ?, logo_url = ?, sort_order = ?
            WHERE id = ?");
        $stmt->execute([
            $data['gateway_name'] ?? '',
            $data['gateway_id'] ?? '',
            $data['is_active'] ?? 1,
            $data['fee_type'] ?? 'percentage',
            $data['fee_value'] ?? 0.00,
            $data['description'] ?? '',
            $data['instructions'] ?? '',
            $data['logo_url'] ?? '',
            $data['sort_order'] ?? 0,
            $id
        ]);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        if (!isAdmin()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM payment_gateways WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'toggle_active':
        if (!isAdmin()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE payment_gateways SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
