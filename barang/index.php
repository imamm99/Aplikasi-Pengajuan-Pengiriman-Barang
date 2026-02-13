<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('barang');

require_once '../config/database.php';

$result = mysqli_query($connection, "SELECT * FROM `barang` ORDER BY id DESC");
?>

<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Daftar Barang</h2>
    <a href="add.php" class="btn btn-primary">+ Tambah Barang</a>
</div>

<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori Id</th>
                    <th>Berat (Kg)</th>
                    <th>Stok</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['kategori_id']) ?></td>
                        <td><?= htmlspecialchars($row['berat_kg']) ?></td>
                        <td>
                            <span
                                class="badge bg-<?= $row['stok'] == 0 ? 'danger' : ($row['stok'] < 10 ? 'warning' : 'success') ?>">
                                <?= htmlspecialchars($row['stok']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Belum ada data barang.</div>
<?php endif; ?>


<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>