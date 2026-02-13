<?php
require_once 'config/database.php';

try {
    // Add stok column
    mysqli_query($connection, "ALTER TABLE barang ADD COLUMN stok INT(11) NOT NULL DEFAULT 0 AFTER berat_kg");

    // Update existing items with sample stock
    $updates = [
        ['LPT01', 10],
        ['HP01', 25],
        ['MSE01', 50],
        ['KBD01', 15],
        ['MON01', 8]
    ];

    $stmt = mysqli_prepare($connection, "UPDATE barang SET stok = ? WHERE kode_barang = ?");
    foreach ($updates as $update) {
        mysqli_stmt_bind_param($stmt, "is", $update[1], $update[0]);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);

    echo "Success: Added stok column and updated sample data<br>";
    echo "Stock values: Laptop(10), Samsung(25), Mouse(50), Keyboard(15), Monitor(8)";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>