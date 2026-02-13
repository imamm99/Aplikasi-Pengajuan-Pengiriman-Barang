<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('barang');

require_once '../config/database.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang_post = trim($_POST['kode_barang'] ?? '');
    $nama_barang_post = trim($_POST['nama_barang'] ?? '');
    $kategori_id_post = trim($_POST['kategori_id'] ?? '');
    $berat_kg_post = trim($_POST['berat_kg'] ?? '');
    $stok_post = (int) ($_POST['stok'] ?? 0);
    $deskripsi_post = trim($_POST['deskripsi'] ?? '');

    if (empty($kode_barang_post) || empty($nama_barang_post) || empty($kategori_id_post)) {
        $error = "Kode Barang, Nama Barang, dan Kategori wajib diisi.";
    }

    if (!$error) {
        $stmt = mysqli_prepare($connection, "INSERT INTO `barang` (kode_barang, nama_barang, kategori_id, berat_kg, stok, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssidss", $kode_barang_post, $nama_barang_post, $kategori_id_post, $berat_kg_post, $stok_post, $deskripsi_post);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Barang berhasil ditambahkan.";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>";
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>


<h2>Tambah Barang</h2>
<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>
<?php if ($success): ?>
    <?= showAlert($success, 'success') ?>
    <a href="index.php" class="btn btn-secondary">Kembali ke Daftar</a>
<?php else: ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Kode Barang*</label>
            <input type="text" name="kode_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Barang*</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori*</label>
            <?php
            echo dropdownFromTable('kategori_barang', 'id', 'nama_kategori', '', 'kategori_id', '-- Pilih Kategori --', 'nama_kategori');
            ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Berat (Kg)</label>
            <input type="number" step="any" name="berat_kg" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Stok Awal</label>
            <input type="number" name="stok" class="form-control" value="0" min="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
<?php endif; ?>

<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>