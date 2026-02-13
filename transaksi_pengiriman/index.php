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
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';

// Get list of submissions
if ($role === 'admin') {
    $result = mysqli_query($connection, "SELECT * FROM `pengajuan_pengiriman` ORDER BY id DESC");
} else {
    $stmt = mysqli_prepare($connection, "SELECT * FROM `pengajuan_pengiriman` WHERE user_id = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>
<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Daftar Pengajuan Pengiriman</h2>
    <a href="add.php" class="btn btn-primary">+ Tambah Pengajuan Pengiriman</a>
</div>
<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Resi</th>
                    <th>Tanggal</th>
                    <th>Pengirim</th>
                    <th>Penerima</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                    <th>Item</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['nomor_resi']) ?></strong></td>
                        <td><?= $row['tanggal_pengajuan'] ?></td>
                        <td>
                            <?= htmlspecialchars($row['nama_pengirim']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($row['no_telepon_pengirim']) ?></small>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['nama_penerima']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($row['no_telepon_penerima']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($row['kota_tujuan']) ?></td>
                        <td>
                            <?php
                            $badge_color = 'secondary'; // Pending
                            if ($row['status_pengiriman'] === 'Disetujui')
                                $badge_color = 'success';
                            elseif ($row['status_pengiriman'] === 'Ditolak')
                                $badge_color = 'danger';
                            elseif ($row['status_pengiriman'] === 'Revisi')
                                $badge_color = 'warning';
                            elseif ($row['status_pengiriman'] === 'Proses')
                                $badge_color = 'info';
                            elseif ($row['status_pengiriman'] === 'Dikirim')
                                $badge_color = 'primary';
                            elseif ($row['status_pengiriman'] === 'Selesai')
                                $badge_color = 'success';
                            ?>
                            <span class="badge bg-<?= $badge_color ?>">
                                <?= ucfirst(htmlspecialchars($row['status_pengiriman'])) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['total_item']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                            <?php if (!hasRole('admin') && in_array($row['status_pengiriman'], ['Pending', 'Revisi'])): ?>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus pengajuan pengiriman ini?')">Hapus</a>
                            <?php endif; ?>

                            <?php if (hasRole('admin')): ?>
                                <?php if ($row['status_pengiriman'] == 'Pending'): ?>
                                    <button class="btn btn-sm btn-success" onclick="approveShipment(<?= $row['id'] ?>)">Setujui</button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectShipment(<?= $row['id'] ?>)">Tolak</button>
                                    <button class="btn btn-sm btn-warning" onclick="reviseShipment(<?= $row['id'] ?>)">Minta
                                        Revisi</button>
                                <?php elseif ($row['status_pengiriman'] == 'Disetujui'): ?>
                                    <a href="process_status.php?id=<?= $row['id'] ?>&status=Proses"
                                        class="btn btn-sm btn-primary">Proses</a>
                                <?php elseif ($row['status_pengiriman'] == 'Proses'): ?>
                                    <a href="process_status.php?id=<?= $row['id'] ?>&status=Dikirim"
                                        class="btn btn-sm btn-primary">Dikirim</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($row['status_pengiriman'] == 'Dikirim'): ?>
                                    <a href="complete_shipment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success"
                                        onclick="return confirm('Konfirmasi barang sudah diterima?')">Barang Diterima</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Belum ada data pengajuan pengiriman.</div>
<?php endif; ?>

<!-- Modal for Admin Notes -->
<div class="modal fade" id="adminNotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="adminNotesForm" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Catatan Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan_admin" id="catatanAdmin" rows="3"
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function approveShipment(id) {
        if (confirm('Setujui pengajuan ini?')) {
            window.location.href = 'process_status.php?id=' + id + '&status=Disetujui';
        }
    }

    function rejectShipment(id) {
        showModal(id, 'Ditolak', 'Alasan Penolakan');
    }

    function reviseShipment(id) {
        showModal(id, 'Revisi', 'Catatan Revisi');
    }

    function showModal(id, status, title) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('catatanAdmin').value = '';
        document.getElementById('adminNotesForm').action = 'process_status.php?id=' + id + '&status=' + status;
        new bootstrap.Modal(document.getElementById('adminNotesModal')).show();
    }
</script>

<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>