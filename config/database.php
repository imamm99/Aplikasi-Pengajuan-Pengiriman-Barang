<?php
require_once __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Jakarta');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
// Now access vars via $_ENV or getenv()
$DB_HOST = $_ENV['DB_HOST'];
$DB_PORT = (int) ($_ENV['DB_PORT']);
$DB_NAME = $_ENV['DB_NAME'];
$DB_USER = $_ENV['DB_USER'];
$DB_PASS = $_ENV['DB_PASSWORD'] ?? '';
$BASE_PATH = $_ENV['BASE_PATH'] ?? '';
$THEME = $_ENV['THEME'] ?? '';

// Define BASE_URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . "://" . $host . $BASE_PATH);

$connection = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>