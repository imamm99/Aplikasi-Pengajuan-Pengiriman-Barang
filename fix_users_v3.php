<?php
require_once 'config/database.php';

// Force update
$users = [
    'gudang' => 'gudang',
    'logistik' => 'logistik',
    'admin' => 'admin'
];

foreach ($users as $username => $role) {
    echo "Updating $username to role '$role'...<br>";
    $query = "UPDATE users SET role = '$role' WHERE username = '$username'";
    if (mysqli_query($connection, $query)) {
        echo "Success.<br>";
    } else {
        echo "Error: " . mysqli_error($connection) . "<br>";
    }
}
echo "Done. Please check debug_login_v2.php again.";
?>