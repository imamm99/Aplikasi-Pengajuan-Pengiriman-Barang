<?php
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';

requireAuth();
requireModuleAccess('pelanggan');

require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id)
    redirect('index.php');

$stmt = mysqli_prepare($connection, "SELECT id, nama_pelanggan, alamat, no_telepon, email FROM `pelanggan` WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pelanggan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$pelanggan) {
    redirect('index.php');
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan_post = trim($_POST['nama_pelanggan'] ?? '');
    $alamat_post = trim($_POST['alamat'] ?? '');
    $no_telepon_post = trim($_POST['no_telepon'] ?? '');
    $email_post = trim($_POST['email'] ?? '');
    if (empty($nama_pelanggan_post) || empty($alamat_post) || empty($no_telepon_post)) {
        $error = "Nama Pelanggan dan Alamat dan No Telepon wajib diisi.";
    }
    if (!$error) {
        $stmt = mysqli_prepare($connection, "UPDATE `pelanggan` SET `nama_pelanggan` = ?, `alamat` = ?, `no_telepon` = ?, `email` = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nama_pelanggan_post, $alamat_post, $no_telepon_post, $email_post, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Pelanggan berhasil diperbarui.";
            mysqli_stmt_close($stmt);
            $stmt = mysqli_prepare($connection, "SELECT id, nama_pelanggan, alamat, no_telepon, email FROM `pelanggan` WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $pelanggan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
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

<h2>Edit Pelanggan</h2>
<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>
<?php if ($success): ?>
    <?= showAlert($success, 'success') ?>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Nama Pelanggan*</label>
        <input type="text" name="nama_pelanggan" class="form-control"
            value="<?= htmlspecialchars($pelanggan['nama_pelanggan']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Alamat*</label>
        <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($pelanggan['alamat']) ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">No Telepon*</label>
        <input type="text" name="no_telepon" class="form-control"
            value="<?= htmlspecialchars($pelanggan['no_telepon']) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($pelanggan['email']) ?>">
    </div>
    <button type="submit" class="btn btn-primary">Perbarui</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>


<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>