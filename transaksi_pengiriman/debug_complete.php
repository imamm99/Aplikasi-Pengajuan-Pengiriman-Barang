<?php
// Debug file to test complete_shipment functionality
error_reporting(E_ALL);
ini_display_errors = 1;

echo "<h2>Testing complete_shipment.php</h2>";

// Test 1: Check if file exists
$file = __DIR__ . '/complete_shipment.php';
echo "<p>File exists: " . (file_exists($file) ? "YES" : "NO") . "</p>";
echo "<p>File path: $file</p>";

// Test 2: Check session
session_start();
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "</p>";
echo "<p>Role: " . ($_SESSION['role'] ?? 'NOT SET') . "</p>";

// Test 3: Check database connection
require_once '../config/database.php';
echo "<p>Database connected: " . (isset($connection) && $connection ? "YES" : "NO") . "</p>";

// Test 4: Check if ID parameter works
$id = (int)($_GET['id'] ?? 0);
echo "<p>ID from GET: $id</p>";

if ($id > 0) {
    $stmt = mysqli_prepare($connection, "SELECT id, status_pengiriman, user_id FROM pengajuan_pengiriman WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row) {
        echo "<p>Shipment found!</p>";
        echo "<p>Status: " . $row['status_pengiriman'] . "</p>";
        echo "<p>User ID: " . $row['user_id'] . "</p>";
    } else {
        echo "<p>Shipment NOT found</p>";
    }
    mysqli_stmt_close($stmt);
}

echo "<hr>";
echo "<p><a href='complete_shipment.php?id=$id'>Try complete_shipment.php</a></p>";
?>
