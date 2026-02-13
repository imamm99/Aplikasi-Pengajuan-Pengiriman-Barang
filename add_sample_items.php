<?php
require_once 'config/database.php';

$items = [
    ['MSE01', 'Logitech MX Master 3', 1, 0.15, 'Wireless Mouse'],
    ['KBD01', 'Mechanical Keyboard RGB', 1, 1.20, 'Gaming Keyboard'],
    ['MON01', 'Monitor LG 27 Inch', 1, 5.50, 'Full HD Monitor']
];

$stmt = mysqli_prepare($connection, "INSERT INTO barang (kode_barang, nama_barang, kategori_id, berat_kg, deskripsi) VALUES (?, ?, ?, ?, ?)");

foreach ($items as $item) {
    mysqli_stmt_bind_param($stmt, "ssids", $item[0], $item[1], $item[2], $item[3], $item[4]);
    mysqli_stmt_execute($stmt);
}

mysqli_stmt_close($stmt);
echo "Success: Added 3 new electronic items (Mouse, Keyboard, Monitor)";
?>