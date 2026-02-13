<?php
require_once 'config/database.php';

try {
    // Update status enum to include Dikirim
    mysqli_query($connection, "ALTER TABLE pengajuan_pengiriman MODIFY COLUMN status_pengiriman ENUM('Pending','Revisi','Disetujui','Ditolak','Proses','Dikirim','Selesai') NOT NULL DEFAULT 'Pending'");

    echo "Success: Added 'Dikirim' status to workflow<br>";
    echo "New workflow: Pending → Disetujui → Proses → Dikirim → Selesai";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>