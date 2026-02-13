<?php
// Script to update existing database to new 3-status system
require_once 'config/database.php';

try {
    // First, update any 'Dikirim' or 'Batal' records to 'Selesai' or 'Pending'
    mysqli_query($connection, "UPDATE pengajuan_pengiriman SET status_pengiriman = 'Selesai' WHERE status_pengiriman = 'Dikirim'");
    mysqli_query($connection, "UPDATE pengajuan_pengiriman SET status_pengiriman = 'Pending' WHERE status_pengiriman = 'Batal'");

    // Then alter the column to the new enum
    mysqli_query($connection, "ALTER TABLE pengajuan_pengiriman MODIFY COLUMN status_pengiriman ENUM('Pending','Proses','Selesai') NOT NULL DEFAULT 'Pending'");

    echo "Success: Status column updated to 3 values (Pending, Proses, Selesai)";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>