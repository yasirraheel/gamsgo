<?php
// Settings API: Site configuration management (admin only)
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
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY DEFAULT 1,
        site_name VARCHAR(255) DEFAULT 'DigiMarket',
        site_description TEXT,
        mobile_number VARCHAR(50),
        whatsapp_number VARCHAR(50),
        paypal_id VARCHAR(255),
        facebook_url VARCHAR(500),
        twitter_url VARCHAR(500),
        instagram_url VARCHAR(500),
        linkedin_url VARCHAR(500),
        youtube_url VARCHAR(500),
        meta_title VARCHAR(255),
        meta_description TEXT,
        meta_keywords TEXT,
        og_image_url VARCHAR(500),
        favicon_url VARCHAR(500),
        logo_url VARCHAR(500),
        support_email VARCHAR(255),
        currency_symbol VARCHAR(10) DEFAULT '$',
        timezone VARCHAR(50) DEFAULT 'UTC',
        maintenance_mode BOOLEAN DEFAULT 0,
        google_analytics_id VARCHAR(100),
        facebook_pixel_id VARCHAR(100),
        custom_analytics_code TEXT,
        terms_url VARCHAR(500),
        privacy_url VARCHAR(500),
        refund_policy_url VARCHAR(500),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CHECK (id = 1)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo = db();
    $pdo->exec($sql);
    
    // Add custom_analytics_code column if it doesn't exist (for existing tables)
    try {
        $pdo->exec("ALTER TABLE settings ADD COLUMN custom_analytics_code TEXT");
    } catch (PDOException $e) {
        // Column already exists, ignore error
    }
    
    // Insert default settings if not exists
    $pdo = db();
    $check = $pdo->query("SELECT COUNT(*) as c FROM settings WHERE id = 1")->fetch();
    if ($check['c'] == 0) {
        $pdo->exec("INSERT INTO settings (id, site_name, site_description) VALUES (1, 'DigiMarket', 'Premium Digital Assets Marketplace')");
    }
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

// Get settings (public access)
if ($action === 'get') {
    $pdo = db();
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1 LIMIT 1");
    $settings = $stmt->fetch();
    
    if (!$settings) {
        respond(['error' => 'Settings not found'], 404);
    }
    
    // Convert boolean values
    $settings['maintenance_mode'] = (bool)$settings['maintenance_mode'];
    
    respond($settings);
}

// Update settings (admin only)
if ($action === 'update') {
    if (!isAdmin()) {
        respond(['error' => 'Admin access required'], 403);
    }
    
    $data = jsonBody();
    $pdo = db();
    
    // Build dynamic update query based on provided fields
    $allowedFields = [
        'site_name', 'site_description', 'mobile_number', 'whatsapp_number',
        'paypal_id', 'facebook_url', 'twitter_url', 'instagram_url',
        'linkedin_url', 'youtube_url', 'meta_title', 'meta_description',
        'meta_keywords', 'og_image_url', 'favicon_url', 'logo_url',
        'support_email', 'currency_symbol', 'timezone', 'maintenance_mode',
        'google_analytics_id', 'facebook_pixel_id', 'custom_analytics_code',
        'terms_url', 'privacy_url', 'refund_policy_url'
    ];
    
    $updates = [];
    $values = [];
    
    foreach ($allowedFields as $field) {
        if (array_key_exists($field, $data)) {
            $updates[] = "{$field} = ?";
            $values[] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        respond(['error' => 'No valid fields to update'], 400);
    }
    
    $sql = "UPDATE settings SET " . implode(', ', $updates) . " WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    
    respond(['success' => true, 'message' => 'Settings updated successfully']);
}

// Reset to defaults (admin only)
if ($action === 'reset') {
    if (!isAdmin()) {
        respond(['error' => 'Admin access required'], 403);
    }
    
    $pdo = db();
    $pdo->exec("DELETE FROM settings WHERE id = 1");
    $pdo->exec("INSERT INTO settings (id, site_name, site_description) VALUES (1, 'DigiMarket', 'Premium Digital Assets Marketplace')");
    
    respond(['success' => true, 'message' => 'Settings reset to defaults']);
}

respond(['error' => 'Invalid action'], 400);
