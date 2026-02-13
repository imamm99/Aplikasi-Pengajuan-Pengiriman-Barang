<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('kategori_barang');

require_once '../config/database.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori_post = trim($_POST['nama_kategori'] ?? '');
    if (empty($nama_kategori_post)) {
        $error = "Nama Kategori wajib diisi.";
    }
    if (!$error) {
        $stmt = mysqli_prepare($connection, "INSERT INTO `kategori_barang` (nama_kategori) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $nama_kategori_post);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Kategori Barang berhasil ditambahkan.";
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

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<?php include '../views/'.$THEME.'/topnav.php'; ?>
<?php include '../views/'.$THEME.'/upper_block.php'; ?>


            <h2>Tambah Kategori Barang</h2>
            <?php if ($error): ?>
                <?= showAlert($error, 'danger') ?>
            <?php endif; ?>
            <?php if ($success): ?>
                <?= showAlert($success, 'success') ?>
                <a href="index.php" class="btn btn-secondary">Kembali ke Daftar</a>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori*</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            <?php endif; ?>

<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>
