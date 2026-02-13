<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();
requireModuleAccess('transaksi_pengiriman');
require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id)
    redirect('index.php');

// Fetch existing data
$stmt = mysqli_prepare($connection, "SELECT * FROM `pengajuan_pengiriman` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$transaksi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$transaksi)
    redirect('index.php');

// Access Control
if ($_SESSION['role'] !== 'admin' && $transaksi['user_id'] != $_SESSION['user_id']) {
    redirect('index.php');
}

// Pengaju can only edit if status is Pending or Revisi
if ($_SESSION['role'] !== 'admin' && !in_array($transaksi['status_pengiriman'], ['Pending', 'Revisi'])) {
    redirect('index.php');
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? ''))
        die('Invalid CSRF token.');

    // Sender Data
    $nama_pengirim = trim($_POST['nama_pengirim'] ?? '');
    $alamat_pengirim = trim($_POST['alamat_pengirim'] ?? '');
    $no_telepon_pengirim = trim($_POST['no_telepon_pengirim'] ?? '');

    // Receiver Data
    $nama_penerima = trim($_POST['nama_penerima'] ?? '');
    $alamat_penerima = trim($_POST['alamat_penerima'] ?? '');
    $no_telepon_penerima = trim($_POST['no_telepon_penerima'] ?? '');
    $kota_tujuan = trim($_POST['kota_tujuan'] ?? '');

    $tanggal_pengajuan = trim($_POST['tanggal_pengajuan'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (empty($nama_pengirim) || empty($nama_penerima) || empty($kota_tujuan)) {
        $error = "Semua field wajib diisi.";
    }

    if (!$error) {
        $sql = "UPDATE `pengajuan_pengiriman` SET 
                `tanggal_pengajuan`=?, 
                `nama_pengirim`=?, `alamat_pengirim`=?, `no_telepon_pengirim`=?,
                `nama_penerima`=?, `alamat_penerima`=?, `no_telepon_penerima`=?, `kota_tujuan`=?,
                `keterangan`=?
                WHERE `id`=?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sssssssssi",
            $tanggal_pengajuan,
            $nama_pengirim,
            $alamat_pengirim,
            $no_telepon_pengirim,
            $nama_penerima,
            $alamat_penerima,
            $no_telepon_penerima,
            $kota_tujuan,
            $keterangan,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: detail.php?id=$id");
            exit();
        } else {
            $error = "Gagal mengupdate data: " . mysqli_error($connection);
        }
        mysqli_stmt_close($stmt);
    }
}
$csrfToken = generateCSRFToken();
?>
<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<h2>Edit Pengajuan Pengiriman #<?= $transaksi['nomor_resi'] ?></h2>
<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="mb-3">
        <label class="form-label">Tanggal Pengajuan*</label>
        <input type="date" name="tanggal_pengajuan" class="form-control"
            value="<?= date('Y-m-d', strtotime($transaksi['tanggal_pengajuan'])) ?>" required>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5 class="text-primary">Data Pengirim</h5>
            <div class="mb-3">
                <label class="form-label">Nama Pengirim</label>
                <input type="text" name="nama_pengirim" class="form-control"
                    value="<?= htmlspecialchars($transaksi['nama_pengirim']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Pengirim</label>
                <textarea name="alamat_pengirim" class="form-control" rows="2"
                    required><?= htmlspecialchars($transaksi['alamat_pengirim']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">No. Telepon Pengirim</label>
                <input type="text" name="no_telepon_pengirim" class="form-control"
                    value="<?= htmlspecialchars($transaksi['no_telepon_pengirim']) ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <h5 class="text-success">Data Penerima</h5>
            <div class="mb-3">
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="nama_penerima" class="form-control"
                    value="<?= htmlspecialchars($transaksi['nama_penerima']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kota Tujuan</label>
                <input type="text" name="kota_tujuan" class="form-control"
                    value="<?= htmlspecialchars($transaksi['kota_tujuan']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Penerima</label>
                <textarea name="alamat_penerima" class="form-control" rows="2"
                    required><?= htmlspecialchars($transaksi['alamat_penerima']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">No. Telepon Penerima</label>
                <input type="text" name="no_telepon_penerima" class="form-control"
                    value="<?= htmlspecialchars($transaksi['no_telepon_penerima']) ?>" required>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" class="form-control"><?= htmlspecialchars($transaksi['keterangan']) ?></textarea>
    </div>

    <div class="d-flex justify-content-between">
        <a href="detail.php?id=<?= $id ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>