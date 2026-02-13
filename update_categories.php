<?php
require_once 'config/database.php';

try {
    // Clear existing categories and items
    mysqli_query($connection, "DELETE FROM barang");
    mysqli_query($connection, "DELETE FROM kategori_barang");
    mysqli_query($connection, "ALTER TABLE kategori_barang AUTO_INCREMENT = 1");
    mysqli_query($connection, "ALTER TABLE barang AUTO_INCREMENT = 1");

    // Insert new specific categories
    $categories = [
        'Laptop',
        'Smartphone',
        'Mouse & Keyboard',
        'Monitor',
        'Audio & Speaker',
        'Aksesoris Elektronik'
    ];

    $stmt = mysqli_prepare($connection, "INSERT INTO kategori_barang (nama_kategori) VALUES (?)");
    foreach ($categories as $cat) {
        mysqli_stmt_bind_param($stmt, "s", $cat);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);

    // Insert items with correct categories
    $items = [
        ['LPT01', 'Laptop Asus', 1, 2.50, 'Laptop Gaming'],
        ['HP01', 'Samsung S24', 2, 0.20, 'Smartphone Flagship'],
        ['MSE01', 'Logitech MX Master 3', 3, 0.15, 'Wireless Mouse'],
        ['KBD01', 'Mechanical Keyboard RGB', 3, 1.20, 'Gaming Keyboard'],
        ['MON01', 'Monitor LG 27 Inch', 4, 5.50, 'Full HD Monitor']
    ];

    $stmt = mysqli_prepare($connection, "INSERT INTO barang (kode_barang, nama_barang, kategori_id, berat_kg, deskripsi) VALUES (?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        mysqli_stmt_bind_param($stmt, "ssids", $item[0], $item[1], $item[2], $item[3], $item[4]);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);

    echo "Success: Updated categories to specific electronics types<br>";
    echo "Categories: Laptop, Smartphone, Mouse & Keyboard, Monitor, Audio & Speaker, Aksesoris Elektronik<br>";
    echo "Items updated with correct categories";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>