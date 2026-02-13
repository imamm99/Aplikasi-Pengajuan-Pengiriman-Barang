<?php
ini_set('display_errors', 1);
require_once 'config/database.php';
try {
    mysqli_query($connection, "ALTER TABLE barang ADD COLUMN berat_kg DECIMAL(10,2) DEFAULT 1.00");
    echo "Success: Column berat_kg added.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>