<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('barang');

require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id)
    redirect('barang/index.php');

$stmt = mysqli_prepare($connection, "SELECT id, kode_barang, nama_barang, kategori_id, berat_kg, stok, deskripsi FROM `barang` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$barang = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$barang) {
    redirect('barang/index.php');
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang_post = trim($_POST['kode_barang'] ?? '');
    $nama_barang_post = trim($_POST['nama_barang'] ?? '');
    $kategori_id_post = trim($_POST['kategori_id'] ?? '');
    $berat_kg_post = (float) ($_POST['berat_kg'] ?? 0);
    $stok_post = (int) ($_POST['stok'] ?? 0);
    $deskripsi_post = trim($_POST['deskripsi'] ?? '');
    if (empty($kode_barang_post) || empty($nama_barang_post) || empty($kategori_id_post)) {
        $error = "Kode Barang dan Nama Barang dan Kategori Id wajib diisi.";
    }
    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE `barang` SET `kode_barang` = ?, `nama_barang` = ?, `kategori_id` = ?, `berat_kg` = ?, `stok` = ?, `deskripsi` = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssdissi", $kode_barang_post, $nama_barang_post, $kategori_id_post, $berat_kg_post, $stok_post, $deskripsi_post, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Barang berhasil diperbarui.";
            mysqli_stmt_close($stmt);
            $stmt = mysqli_prepare($connection, "SELECT id, kode_barang, nama_barang, kategori_id, berat_kg, stok, deskripsi FROM `barang` WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
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

<h2>Edit Barang</h2>
<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>
<?php if ($success): ?>
    <?= showAlert($success, 'success') ?>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Kode Barang*</label>
        <input type="text" name="kode_barang" class="form-control"
            value="<?= htmlspecialchars($barang['kode_barang']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Barang*</label>
        <input type="text" name="nama_barang" class="form-control"
            value="<?= htmlspecialchars($barang['nama_barang']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Kategori*</label>
        <?php echo dropdownFromTable('kategori_barang', 'id', 'nama_kategori', $barang['kategori_id'], 'kategori_id', ''); ?>
        <script>
            // Disable selection
            document.getElementsByName('kategori_id')[0].style.pointerEvents = 'none';
            document.getElementsByName('kategori_id')[0].style.backgroundColor = '#e9ecef';
        </script>
    </div>
    <div class="mb-3">
        <label class="form-label">Berat (Kg)*</label>
        <input type="number" step="0.01" name="berat_kg" class="form-control"
            value="<?= htmlspecialchars($barang['berat_kg']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Stok*</label>
        <input type="number" name="stok" class="form-control" value="<?= htmlspecialchars($barang['stok']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control"
            rows="3"><?= htmlspecialchars($barang['deskripsi']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Perbarui</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>


<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>