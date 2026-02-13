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

$id = (int) ($_GET['id'] ?? 0);
if (!$id)
    redirect('index.php');

$stmt = mysqli_prepare($connection, "SELECT * FROM penerima WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row)
    redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    $nama = trim($_POST['nama_penerima'] ?? '');
    $alamat = trim($_POST['alamat_penerima'] ?? '');
    $telepon = trim($_POST['no_telepon'] ?? '');

    if (empty($nama) || empty($alamat) || empty($telepon)) {
        $error = "Semua field wajib diisi.";
    }

    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE penerima SET nama_penerima = ?, alamat_penerima = ?, no_telepon = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $nama, $alamat, $telepon, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            redirect('index.php');
        } else {
            $error = "Gagal memperbarui data.";
        }
    }
}
$csrfToken = generateCSRFToken();
?>
<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<h2>Edit Penerima</h2>
<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <div class="mb-3">
        <label class="form-label">Nama Penerima*</label>
        <input type="text" name="nama_penerima" class="form-control"
            value="<?= htmlspecialchars($row['nama_penerima']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Alamat*</label>
        <textarea name="alamat_penerima" class="form-control"
            required><?= htmlspecialchars($row['alamat_penerima']) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">No. Telepon*</label>
        <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($row['no_telepon']) ?>"
            required>
    </div>
    <button type="submit" class="btn btn-primary">Perbarui</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>