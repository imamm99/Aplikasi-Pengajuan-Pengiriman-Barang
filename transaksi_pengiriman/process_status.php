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

// Only Admin can change status this way
if (!hasRole('admin')) {
    die('Access Denied: Admin only.');
}

$id = (int) ($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';
$catatan_admin = $_POST['catatan_admin'] ?? '';

// Only allow these status values for admin
$allowed_statuses = ['Disetujui', 'Ditolak', 'Revisi', 'Proses', 'Dikirim'];

// Get redirect URL from referer or default to index.php
$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'index.php';
// Ensure redirect is within the same application
if (strpos($redirect_url, $_SERVER['HTTP_HOST']) === false) {
    $redirect_url = 'index.php';
}

if (!$id || !in_array($status, $allowed_statuses)) {
    header('Location: ' . $redirect_url);
    exit();
}

// Fetch current status
$stmt = mysqli_prepare($connection, "SELECT status_pengiriman FROM `pengajuan_pengiriman` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    header('Location: ' . $redirect_url);
    exit();
}

// Validate status transitions
$current = $row['status_pengiriman'];
$valid = false;

// Pending → Disetujui/Ditolak/Revisi
if ($current === 'Pending' && in_array($status, ['Disetujui', 'Ditolak', 'Revisi'])) {
    $valid = true;
}
// Disetujui → Proses
elseif ($current === 'Disetujui' && $status === 'Proses') {
    $valid = true;
}
// Proses → Dikirim (Admin ships the package)
elseif ($current === 'Proses' && $status === 'Dikirim') {
    $valid = true;
}

if ($valid) {
    // Start transaction
    mysqli_begin_transaction($connection);

    try {
        // Update status and catatan_admin
        $updateStmt = mysqli_prepare($connection, "UPDATE `pengajuan_pengiriman` SET `status_pengiriman` = ?, `catatan_admin` = ? WHERE `id` = ?");
        mysqli_stmt_bind_param($updateStmt, "ssi", $status, $catatan_admin, $id);

        if (!mysqli_stmt_execute($updateStmt)) {
            throw new Exception("Error updating status: " . mysqli_error($connection));
        }
        mysqli_stmt_close($updateStmt);

        // If status is 'Dikirim', reduce stock
        if ($status === 'Dikirim') {
            // Get items
            $itemStmt = mysqli_prepare($connection, "SELECT barang_id, qty_barang FROM `detail_pengajuan` WHERE pengajuan_id = ?");
            mysqli_stmt_bind_param($itemStmt, "i", $id);
            mysqli_stmt_execute($itemStmt);
            $itemsResult = mysqli_stmt_get_result($itemStmt);

            while ($item = mysqli_fetch_assoc($itemsResult)) {
                // Update stock
                $stockStmt = mysqli_prepare($connection, "UPDATE `barang` SET `stok` = `stok` - ? WHERE `id` = ?");
                mysqli_stmt_bind_param($stockStmt, "ii", $item['qty_barang'], $item['barang_id']);

                if (!mysqli_stmt_execute($stockStmt)) {
                    throw new Exception("Error updating stock for item ID " . $item['barang_id']);
                }
                mysqli_stmt_close($stockStmt);
            }
            mysqli_stmt_close($itemStmt);
        }

        // Commit transaction
        mysqli_commit($connection);

        // Redirect back to referring page with success message
        $separator = strpos($redirect_url, '?') !== false ? '&' : '?';
        header('Location: ' . $redirect_url . $separator . 'msg=status_updated');
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($connection);
        die("Transaction failed: " . $e->getMessage());
    }
} else {
    // If not valid, redirect without updating
    $separator = strpos($redirect_url, '?') !== false ? '&' : '?';
    header('Location: ' . $redirect_url . $separator . 'msg=invalid_transition');
    exit();
}
?>