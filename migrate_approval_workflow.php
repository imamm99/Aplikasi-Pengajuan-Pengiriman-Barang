<?php
require_once 'config/database.php';

try {
    // Add catatan_admin column
    mysqli_query($connection, "ALTER TABLE pengajuan_pengiriman ADD COLUMN catatan_admin TEXT DEFAULT NULL AFTER status_pengiriman");

    // Update status enum
    mysqli_query($connection, "ALTER TABLE pengajuan_pengiriman MODIFY COLUMN status_pengiriman ENUM('Pending','Revisi','Disetujui','Ditolak','Proses','Selesai') NOT NULL DEFAULT 'Pending'");

    echo "Success: Updated status enum to include approval workflow<br>";
    echo "New statuses: Pending, Revisi, Disetujui, Ditolak, Proses, Selesai<br>";
    echo "Added catatan_admin field for admin notes";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>