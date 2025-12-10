<?php
// Products API: CRUD operations for marketplace items
session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'u559276167_gamsgo';
$DB_USER = 'u559276167_gamsgo';
$DB_PASS = 'Gamsgo@123';

header('Content-Type: application/json');

function db() {
    static $pdo = null;
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    if ($pdo === null) {
        $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function ensureSchema() {
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id VARCHAR(36) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        service_type VARCHAR(100) NOT NULL,
        account_type ENUM('Private', 'Shared') NOT NULL,
        original_price DECIMAL(10,2) NOT NULL,
        discounted_price DECIMAL(10,2) NOT NULL,
        description TEXT,
        features JSON,
        requirements VARCHAR(255),
        is_hot BOOLEAN DEFAULT 0,
        icon VARCHAR(100),
        stock INT DEFAULT 0,
        is_visible BOOLEAN DEFAULT 1,
        validity_months INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    db()->exec($sql);
}

function jsonBody() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function isAdmin() {
    return !empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

$action = $_GET['action'] ?? '';

ensureSchema();

// Get all products
if ($action === 'list') {
    $pdo = db();
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
    
    // Convert JSON strings back to arrays
    foreach ($products as &$product) {
        $product['features'] = json_decode($product['features'] ?? '[]', true);
        $product['isHot'] = (bool)$product['is_hot'];
        $product['isVisible'] = (bool)$product['is_visible'];
        $product['accountType'] = $product['account_type'];
        $product['serviceType'] = $product['service_type'];
        $product['originalPrice'] = (float)$product['original_price'];
        $product['discountedPrice'] = (float)$product['discounted_price'];
        $product['stock'] = (int)$product['stock'];
    }
    
    respond($products);
}

// Create product (admin only)
if ($action === 'create') {
    if (!isAdmin()) {
        respond(['error' => 'Admin access required'], 403);
    }
    
    $data = jsonBody();
    $pdo = db();
    
    $id = $data['id'] ?? uniqid('prod_', true);
    $features = json_encode($data['features'] ?? []);
    
    $stmt = $pdo->prepare("INSERT INTO products (id, name, service_type, account_type, original_price, discounted_price, description, features, requirements, is_hot, icon, stock, is_visible, validity_months) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $id,
        $data['name'],
        $data['serviceType'] ?? '',
        $data['accountType'] ?? 'Private',
        $data['originalPrice'] ?? 0,
        $data['discountedPrice'] ?? 0,
        $data['description'] ?? '',
        $features,
        $data['requirements'] ?? '',
        $data['isHot'] ?? false,
        $data['icon'] ?? 'fa-box',
        $data['stock'] ?? 0,
        $data['isVisible'] ?? true,
        $data['validityMonths'] ?? 1
    ]);
    
    respond(['success' => true, 'id' => $id]);
}

// Update product (admin only)
if ($action === 'update') {
    if (!isAdmin()) {
        respond(['error' => 'Admin access required'], 403);
    }
    
    $data = jsonBody();
    $pdo = db();
    
    $features = json_encode($data['features'] ?? []);
    
    $stmt = $pdo->prepare("UPDATE products SET name=?, service_type=?, account_type=?, original_price=?, discounted_price=?, description=?, features=?, requirements=?, is_hot=?, icon=?, stock=?, is_visible=?, validity_months=? WHERE id=?");
    
    $stmt->execute([
        $data['name'],
        $data['serviceType'] ?? '',
        $data['accountType'] ?? 'Private',
        $data['originalPrice'] ?? 0,
        $data['discountedPrice'] ?? 0,
        $data['description'] ?? '',
        $features,
        $data['requirements'] ?? '',
        $data['isHot'] ?? false,
        $data['icon'] ?? 'fa-box',
        $data['stock'] ?? 0,
        $data['isVisible'] ?? true,
        $data['validityMonths'] ?? 1,
        $data['id']
    ]);
    
    respond(['success' => true]);
}

// Delete product (admin only)
if ($action === 'delete') {
    if (!isAdmin()) {
        respond(['error' => 'Admin access required'], 403);
    }
    
    $id = $_GET['id'] ?? '';
    if (!$id) {
        respond(['error' => 'Product ID required'], 400);
    }
    
    $pdo = db();
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    respond(['success' => true]);
}

// Toggle visibility (admin only)
if ($action === 'toggle_visible') {
    if (!isAdmin()) {
        respond(['error' => 'Admin access required'], 403);
    }
    
    $id = $_GET['id'] ?? '';
    if (!$id) {
        respond(['error' => 'Product ID required'], 400);
    }
    
    $pdo = db();
    $stmt = $pdo->prepare("UPDATE products SET is_visible = NOT is_visible WHERE id = ?");
    $stmt->execute([$id]);
    
    respond(['success' => true]);
}

respond(['error' => 'Invalid action'], 400);
