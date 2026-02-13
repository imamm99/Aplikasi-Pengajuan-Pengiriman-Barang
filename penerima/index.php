<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('penerima');
require_once '../config/database.php';

$result = mysqli_query($connection, "SELECT * FROM penerima ORDER BY id DESC");
?>
<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Penerima</h2>
    <a href="add.php" class="btn btn-primary">+ Tambah Penerima</a>
</div>

<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Penerima</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <?= $row['id'] ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['nama_penerima']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['alamat_penerima']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['no_telepon']) ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Hapus penerima ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Belum ada data penerima.</div>
<?php endif; ?>

<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>