<?php
// config.php - DB connection and shared utilities

session_start();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'pms_db';
const DB_USER = 'root';
const DB_PASS = ''; // <-- set your password

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        $options
    );
} catch (PDOException $e) {
    die("DB connection failed: " . htmlspecialchars($e->getMessage()));
}

// helpers
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function current_user($pdo) {
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT id, email, full_name, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
