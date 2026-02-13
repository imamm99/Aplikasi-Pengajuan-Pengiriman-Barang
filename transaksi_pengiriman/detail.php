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
    redirect('transaksi_pengiriman/index.php');

// Updated SQL: Direct select, no joins needed for sender/receiver info as they are now in the table
$stmt = mysqli_prepare($connection, "SELECT p.* FROM `pengajuan_pengiriman` p WHERE p.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$transaksi_pengiriman = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$transaksi_pengiriman)
    redirect('transaksi_pengiriman/index.php');

// Access Control
if ($_SESSION['role'] !== 'admin' && $transaksi_pengiriman['user_id'] != $_SESSION['user_id']) {
    redirect('transaksi_pengiriman/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_selesai'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    if ($transaksi_pengiriman['status_pengiriman'] !== 'Selesai') {
        $updateStmt = mysqli_prepare($connection, "UPDATE `pengajuan_pengiriman` SET `status_pengiriman` = 'Selesai' WHERE `id` = ?");
        mysqli_stmt_bind_param($updateStmt, "i", $id);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
        $transaksi_pengiriman['status_pengiriman'] = 'Selesai';
    }
    redirect($_SERVER['REQUEST_URI']);
}

$details = mysqli_query($connection, "SELECT d.*, b.nama_barang, b.berat_kg, (d.qty_barang * b.berat_kg) as subtotal_berat, k.nama_kategori 
    FROM `detail_pengajuan` d
    JOIN `barang` b ON d.barang_id = b.id
    JOIN `kategori_barang` k ON b.kategori_id = k.id
    WHERE d.pengajuan_id = $id");

$csrfToken = generateCSRFToken();

// Handle success/error messages
$success = '';
$error = '';
if (isset($_GET['msg']) && $_GET['msg'] === 'completed') {
    $success = 'Pengajuan berhasil diselesaikan! Terima kasih telah mengkonfirmasi penerimaan barang.';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid_status') {
        $error = 'Status pengajuan tidak valid untuk diselesaikan.';
    } elseif ($_GET['error'] === 'update_failed') {
        $error = 'Gagal mengupdate status pengajuan.';
    }
}
?>
<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Detail Pengajuan Pengiriman #<?= $transaksi_pengiriman['id'] ?></h2>
    <div>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
        <?php if (!hasRole('admin') && $transaksi_pengiriman['status_pengiriman'] === 'Dikirim'): ?>
            <a href="complete_shipment.php?id=<?= $id ?>" class="btn btn-success"
                onclick="return confirm('Konfirmasi barang sudah diterima dan selesaikan pengajuan ini?')">
                <i class="fas fa-check-circle"></i> Barang Diterima
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Nomor Resi:
                <?= htmlspecialchars($transaksi_pengiriman['nomor_resi']) ?>
            </h3>
            <div>
                <?php
                $badge_color = 'secondary'; // Pending
                if ($transaksi_pengiriman['status_pengiriman'] === 'Disetujui')
                    $badge_color = 'success';
                elseif ($transaksi_pengiriman['status_pengiriman'] === 'Ditolak')
                    $badge_color = 'danger';
                elseif ($transaksi_pengiriman['status_pengiriman'] === 'Revisi')
                    $badge_color = 'warning';
                elseif ($transaksi_pengiriman['status_pengiriman'] === 'Proses')
                    $badge_color = 'info';
                elseif ($transaksi_pengiriman['status_pengiriman'] === 'Dikirim')
                    $badge_color = 'primary';
                elseif ($transaksi_pengiriman['status_pengiriman'] === 'Selesai')
                    $badge_color = 'success';
                ?>
                <span class="badge bg-<?= $badge_color ?> fs-5">
                    <?= strtoupper(htmlspecialchars($transaksi_pengiriman['status_pengiriman'])) ?>
                </span>
            </div>
        </div>
        <small class="text-muted">Diajukan pada:
            <?= date('d F Y', strtotime($transaksi_pengiriman['tanggal_pengajuan'])) ?>
        </small>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 border-end">
                <h5 class="text-primary"><i class="align-middle" data-feather="user"></i> Pengirim</h5>
                <hr>
                <p class="mb-1"><strong>
                        <?= htmlspecialchars($transaksi_pengiriman['nama_pengirim']) ?>
                    </strong></p>
                <p class="mb-1">
                    <?= nl2br(htmlspecialchars($transaksi_pengiriman['alamat_pengirim'])) ?>
                </p>
                <p>Telp:
                    <?= htmlspecialchars($transaksi_pengiriman['no_telepon_pengirim']) ?>
                </p>
            </div>
            <div class="col-md-6">
                <h5 class="text-success"><i class="align-middle" data-feather="map-pin"></i> Penerima</h5>
                <hr>
                <p class="mb-1"><strong>
                        <?= htmlspecialchars($transaksi_pengiriman['nama_penerima']) ?>
                    </strong></p>
                <p class="mb-1">
                    <?= nl2br(htmlspecialchars($transaksi_pengiriman['alamat_penerima'])) ?>
                </p>
                <p class="mb-1">Kota Tujuan: <strong>
                        <?= htmlspecialchars($transaksi_pengiriman['kota_tujuan']) ?>
                    </strong></p>
                <p>Telp:
                    <?= htmlspecialchars($transaksi_pengiriman['no_telepon_penerima']) ?>
                </p>
            </div>
        </div>
        <hr>
        <p><strong>Keterangan:</strong>
            <?= htmlspecialchars($transaksi_pengiriman['keterangan'] ?: '-') ?>
        </p>
        <?php if (!empty($transaksi_pengiriman['catatan_admin'])): ?>
            <div class="alert alert-<?= $transaksi_pengiriman['status_pengiriman'] === 'Ditolak' ? 'danger' : 'warning' ?>">
                <strong>Catatan Admin:</strong><br>
                <?= nl2br(htmlspecialchars($transaksi_pengiriman['catatan_admin'])) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<h3 class="mt-4">Item Barang</h3>
<?php if (mysqli_num_rows($details) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Kategori</th>
                    <th>Qty</th>
                    <th>Subtotal Berat</th>
                    <th>Catatan Barang</th>
                    <?php if ($transaksi_pengiriman['status_pengiriman'] !== 'Selesai'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($detail = mysqli_fetch_assoc($details)): ?>
                    <tr>
                        <td>
                            <?= $no++ ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($detail['nama_barang']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($detail['nama_kategori']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($detail['qty_barang']) ?>
                        </td>
                        <td>
                            <?= number_format((float) ($detail['subtotal_berat'] ?? 0), 0, ',', '.') ?> kg
                        </td>
                        <td>
                            <?= htmlspecialchars($detail['catatan_barang'] ?? '-') ?>
                        </td>

                        <?php if ($transaksi_pengiriman['status_pengiriman'] !== 'Selesai'): ?>
                            <td>
                                <a href="detaildelete.php?id=<?= $detail['id'] ?>&master_id=<?= $id; ?>"
                                    class="btn btn-sm btn-danger" onclick="return confirm('Hapus item ini?')">Hapus</a>
                            </td>
                        <?php endif; ?>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Belum ada data detail.</div>
<?php endif; ?>
<a href="index.php" class="btn btn-secondary">Kembali ke Daftar</a>
<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>