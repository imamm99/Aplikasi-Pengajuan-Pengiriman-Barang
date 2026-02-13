<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('transaksi_pengiriman');
require_once '../config/database.php';

// Get redirect URL from referer or default to index.php
$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'index.php';
// Ensure redirect is within the same application
if (strpos($redirect_url, $_SERVER['HTTP_HOST']) === false) {
    $redirect_url = 'index.php';
}

$id = (int) ($_GET['id'] ?? 0);
if ($id) {
    // Check ownership before delete
    $checkStmt = mysqli_prepare($connection, "SELECT user_id FROM `pengajuan_pengiriman` WHERE id = ?");
    mysqli_stmt_bind_param($checkStmt, "i", $id);
    mysqli_stmt_execute($checkStmt);
    $res = mysqli_stmt_get_result($checkStmt);
    $data = mysqli_fetch_assoc($res);
    mysqli_stmt_close($checkStmt);

    if ($data) {
        if ($_SESSION['role'] === 'admin' || $data['user_id'] == $_SESSION['user_id']) {
            $stmt = mysqli_prepare($connection, "DELETE FROM `pengajuan_pengiriman` WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

// Redirect back to referring page with success message
$separator = strpos($redirect_url, '?') !== false ? '&' : '?';
header('Location: ' . $redirect_url . $separator . 'msg=deleted');
exit();
?>