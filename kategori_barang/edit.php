<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('kategori_barang');

require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id)
    redirect('index.php');

$stmt = mysqli_prepare($connection, "SELECT id, nama_kategori FROM `kategori_barang` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kategori_barang = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$kategori_barang) {
    redirect('index.php');
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori_post = trim($_POST['nama_kategori'] ?? '');
    if (empty($nama_kategori_post)) {
        $error = "Nama Kategori wajib diisi.";
    }
    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE `kategori_barang` SET `nama_kategori` = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nama_kategori_post, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Kategori Barang berhasil diperbarui.";
            mysqli_stmt_close($stmt);
            $stmt = mysqli_prepare($connection, "SELECT id, nama_kategori FROM `kategori_barang` WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $kategori_barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>";
        } else {
            $error = "Gagal memperbarui: " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<h2>Edit Kategori Barang</h2>
<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>
<?php if ($success): ?>
    <?= showAlert($success, 'success') ?>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Nama Kategori*</label>
        <input type="text" name="nama_kategori" class="form-control"
            value="<?= htmlspecialchars($kategori_barang['nama_kategori']) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Perbarui</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>


<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>