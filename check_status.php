<?php
require_once '../config/database.php';

$id = 11; // Change this to your shipment ID

$stmt = mysqli_prepare($connection, "SELECT id, nomor_resi, status_pengiriman, user_id FROM pengajuan_pengiriman WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row) {
    echo "<h2>Shipment #$id Status</h2>";
    echo "<p><strong>Nomor Resi:</strong> " . $row['nomor_resi'] . "</p>";
    echo "<p><strong>Current Status:</strong> <span style='color: red; font-size: 20px;'>" . $row['status_pengiriman'] . "</span></p>";
    echo "<p><strong>User ID:</strong> " . $row['user_id'] . "</p>";

    echo "<hr>";
    echo "<h3>What needs to happen:</h3>";

    if ($row['status_pengiriman'] === 'Pending') {
        echo "<p>❌ Status is Pending. Admin needs to APPROVE it first.</p>";
    } elseif ($row['status_pengiriman'] === 'Disetujui') {
        echo "<p>⚠️ Status is Disetujui. Admin needs to click 'Proses' button.</p>";
    } elseif ($row['status_pengiriman'] === 'Proses') {
        echo "<p>⚠️ Status is Proses. Admin needs to click 'Dikirim' button.</p>";
        echo "<p><a href='../transaksi_pengiriman/process_status.php?id=$id&status=Dikirim' style='background: blue; color: white; padding: 10px; text-decoration: none;'>Click here to mark as Dikirim (Admin only)</a></p>";
    } elseif ($row['status_pengiriman'] === 'Dikirim') {
        echo "<p>✅ Status is Dikirim. Pengaju can now click 'Barang Diterima' button.</p>";
        echo "<p><a href='../transaksi_pengiriman/complete_shipment.php?id=$id' style='background: green; color: white; padding: 10px; text-decoration: none;'>Click here to complete (Pengaju only)</a></p>";
    } elseif ($row['status_pengiriman'] === 'Selesai') {
        echo "<p>✅ Status is already Selesai. Nothing to do.</p>";
    }
} else {
    echo "<p>Shipment #$id not found!</p>";
}

mysqli_stmt_close($stmt);
?>