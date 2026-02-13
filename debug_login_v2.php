<?php
header('Content-Type: text/plain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

echo "START_DEBUG_V2\n";

echo "Check 1: Database\n";
$query = "SELECT * FROM users";
$res = mysqli_query($connection, $query);

if (!$res) {
    echo "Query Error: " . mysqli_error($connection) . "\n";
} else {
    $count = mysqli_num_rows($res);
    echo "User Count: $count\n";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "User: " . $row['username'] . " [Role: " . $row['role'] . "]\n";
    }
}

echo "Check 2: Menu Config\n";
$path = __DIR__ . '/config/menu.json';
echo "Path: $path\n";
if (file_exists($path)) {
    echo "File Exists\n";
    $json = file_get_contents($path);
    echo "Content Preview: " . substr($json, 0, 50) . "...\n";
    $data = json_decode($json, true);
    if ($data === null) {
        echo "JSON Decode Error: " . json_last_error_msg() . "\n";
    } else {
        echo "JSON Valid. Roles: " . implode(', ', array_keys($data['roles'] ?? [])) . "\n";
    }
} else {
    echo "File NOT Found\n";
}

echo "END_DEBUG_V2\n";
?>