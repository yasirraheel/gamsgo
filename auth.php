<?php
// Auth API: configure your MySQL credentials below.
// This file returns JSON responses for signup/login/logout/me.
// Include via fetch calls from the frontend.

session_start();

// TODO: set your database credentials.
$DB_HOST = 'YOUR_DB_HOST';
$DB_NAME = 'YOUR_DB_NAME';
$DB_USER = 'YOUR_DB_USER';
$DB_PASS = 'YOUR_DB_PASSWORD';

if ($DB_HOST === 'YOUR_DB_HOST') {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Please set DB credentials in auth.php']);
    exit;
}

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
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(190) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin','user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

$action = $_GET['action'] ?? '';

if (!in_array($action, ['signup', 'login', 'logout', 'me'], true)) {
    respond(['error' => 'Invalid action'], 400);
}

ensureSchema();

if ($action === 'me') {
    if (!empty($_SESSION['user'])) {
        respond(['email' => $_SESSION['user']['email'], 'role' => $_SESSION['user']['role']]);
    }
    respond(['authenticated' => false]);
}

if ($action === 'logout') {
    session_destroy();
    respond(['ok' => true]);
}

$data = jsonBody();
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(['error' => 'Valid email required'], 400);
}
if (strlen($password) < 6) {
    respond(['error' => 'Password must be at least 6 characters'], 400);
}

if ($action === 'signup') {
    $pdo = db();
    // Determine role: default user. Allow admin only if none exists yet.
    $requestedRole = ($data['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
    $hasAdmin = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE role='admin'")->fetch()['c'] > 0;
    $role = ($requestedRole === 'admin' && !$hasAdmin) ? 'admin' : 'user';

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        respond(['error' => 'Email already registered'], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)');
    $insert->execute([$email, $hash, $role]);

    $_SESSION['user'] = ['email' => $email, 'role' => $role];
    respond(['email' => $email, 'role' => $role]);
}

if ($action === 'login') {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        respond(['error' => 'Invalid credentials'], 401);
    }
    $_SESSION['user'] = ['email' => $email, 'role' => $row['role']];
    respond(['email' => $email, 'role' => $row['role']]);
}

respond(['error' => 'Unhandled action'], 400);
