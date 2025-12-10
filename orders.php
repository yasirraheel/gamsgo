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

function isLoggedIn() {
    return isset($_SESSION['user']['email']);
}

function ensureSchema($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        products JSON NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_gateway_id INT NOT NULL,
        payment_gateway_name VARCHAR(100),
        country VARCHAR(100) NULL,
        city VARCHAR(100) NULL,
        postal_code VARCHAR(20) NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        expiry_date DATE NULL,
        admin_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_email (user_email),
        INDEX idx_status (status)
    )");
}

ensureSchema($pdo);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields (shipping address not required for digital products)
        if (empty($data['products']) || empty($data['total_amount']) || empty($data['payment_gateway_id'])) {
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        // Get gateway name
        $stmt = $pdo->prepare("SELECT gateway_name FROM payment_gateways WHERE id = ?");
        $stmt->execute([$data['payment_gateway_id']]);
        $gateway = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("INSERT INTO orders 
            (user_email, products, total_amount, payment_gateway_id, payment_gateway_name, country, city, postal_code, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $_SESSION['user']['email'],
            json_encode($data['products']),
            $data['total_amount'],
            $data['payment_gateway_id'],
            $gateway['gateway_name'] ?? 'Unknown',
            $data['country'] ?? null,
            $data['city'] ?? null,
            $data['postal_code'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'order_id' => $pdo->lastInsertId()]);
        break;

    case 'list_user':
        if (!isLoggedIn()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_email = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user']['email']]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON products
        foreach ($orders as &$order) {
            $order['products'] = json_decode($order['products'], true);
        }
        
        echo json_encode($orders);
        break;

    case 'list_all':
        if (!isAdmin()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $status = $_GET['status'] ?? 'all';
        
        if ($status === 'all') {
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
            $stmt->execute([$status]);
        }
        
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON products
        foreach ($orders as &$order) {
            $order['products'] = json_decode($order['products'], true);
        }
        
        echo json_encode($orders);
        break;

    case 'get':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            echo json_encode(['error' => 'Order not found']);
            exit;
        }
        
        // Check authorization
        if (!isAdmin() && (!isLoggedIn() || $order['user_email'] !== $_SESSION['user']['email'])) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $order['products'] = json_decode($order['products'], true);
        echo json_encode($order);
        break;

    case 'approve':
        if (!isAdmin()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $expiry_date = $data['expiry_date'] ?? null;
        $admin_notes = $data['admin_notes'] ?? '';
        
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE orders SET status = 'approved', expiry_date = ?, admin_notes = ? WHERE id = ?");
        $stmt->execute([$expiry_date, $admin_notes, $id]);
        
        echo json_encode(['success' => true]);
        break;

    case 'reject':
        if (!isAdmin()) {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $admin_notes = $data['admin_notes'] ?? '';
        
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE orders SET status = 'rejected', admin_notes = ? WHERE id = ?");
        $stmt->execute([$admin_notes, $id]);
        
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
        
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
