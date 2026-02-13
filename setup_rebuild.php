<?php
require_once 'config/database.php';

// Disable foreign key checks temporarily
mysqli_query($connection, "SET FOREIGN_KEY_CHECKS=0");

// Read schema
$sql = file_get_contents('database.sql');

// Multi_query execute
if (mysqli_multi_query($connection, $sql)) {
    do {
        // Consumer results
        if ($res = mysqli_store_result($connection)) {
            mysqli_free_result($res);
        }
    } while (mysqli_more_results($connection) && mysqli_next_result($connection));
    echo "Database Schema Reset Successfully.<br>";
} else {
    echo "Error resetting schema: " . mysqli_error($connection) . "<br>";
}

mysqli_query($connection, "SET FOREIGN_KEY_CHECKS=1");

// Seed Users
$users = [
    ['username' => 'admin', 'password' => '123', 'role' => 'admin'],
    ['username' => 'pengaju', 'password' => '123', 'role' => 'pengaju']
];

foreach ($users as $u) {
    $passParams = password_hash($u['password'], PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($connection, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $u['username'], $passParams, $u['role']);
    if (mysqli_stmt_execute($stmt)) {
        echo "User '{$u['username']}' created.<br>";
    } else {
        echo "Error creating user '{$u['username']}': " . mysqli_error($connection) . "<br>";
    }
}

echo "Setup Complete.";
?>