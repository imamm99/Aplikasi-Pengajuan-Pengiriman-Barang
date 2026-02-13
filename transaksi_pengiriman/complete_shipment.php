<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
require_once '../config/database.php';

requireAuth();
requireModuleAccess('transaksi_pengiriman');

// Only pengaju can complete shipments
if (hasRole('admin')) {
    redirect('index.php');
}

$id = (int) ($_GET['id'] ?? 0);

if (!$id) {
    redirect('index.php');
}

// Fetch shipment and verify ownership
$stmt = mysqli_prepare($connection, "SELECT status_pengiriman, user_id FROM `pengajuan_pengiriman` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    redirect('index.php');
}

// Verify ownership
if ($row['user_id'] != $_SESSION['user_id']) {
    redirect('index.php');
}

// Only allow completion if status is Dikirim
if ($row['status_pengiriman'] !== 'Dikirim') {
    header('Location: detail.php?id=' . $id . '&error=invalid_status');
    exit();
}

// Update to Selesai
$updateStmt = mysqli_prepare($connection, "UPDATE `pengajuan_pengiriman` SET `status_pengiriman` = 'Selesai' WHERE `id` = ?");
mysqli_stmt_bind_param($updateStmt, "i", $id);

if (mysqli_stmt_execute($updateStmt)) {
    mysqli_stmt_close($updateStmt);
    header('Location: detail.php?id=' . $id . '&msg=completed');
    exit();
} else {
    mysqli_stmt_close($updateStmt);
    header('Location: detail.php?id=' . $id . '&error=update_failed');
    exit();
}
?>