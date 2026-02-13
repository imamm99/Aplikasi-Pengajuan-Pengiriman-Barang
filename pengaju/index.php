<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
session_start();
require_once '../lib/functions.php';
require_once '../lib/auth.php';
requireAuth();

// Ensure only pengaju can access
if (getUserRole() !== 'pengaju') {
    redirect('../login.php');
}

require_once '../config/database.php';
$user_id = $_SESSION['user_id'];

// Get status counts for pengaju dashboard
$statusQuery = "SELECT status_pengiriman, COUNT(*) as count FROM pengajuan_pengiriman WHERE user_id = ? GROUP BY status_pengiriman";
$stmt = mysqli_prepare($connection, $statusQuery);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$statusResult = mysqli_stmt_get_result($stmt);

// Build status counts array
$statusCounts = [];
$allStatuses = ['Pending', 'Revisi', 'Ditolak', 'Disetujui', 'Proses', 'Dikirim', 'Selesai'];
foreach ($allStatuses as $status) {
    $statusCounts[$status] = 0;
}
while ($row = mysqli_fetch_assoc($statusResult)) {
    $statusCounts[$row['status_pengiriman']] = $row['count'];
}
$totalSubmissions = array_sum($statusCounts);

// Get recent submissions for pengaju
$recentQuery = "SELECT * FROM pengajuan_pengiriman WHERE user_id = ? ORDER BY id DESC LIMIT 5";
$stmt = mysqli_prepare($connection, $recentQuery);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$recentResult = mysqli_stmt_get_result($stmt);
?>
<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<!-- Pengaju Dashboard -->
<h1 class="h3 mb-3"><strong>Dashboard</strong> Pengaju</h1>

<!-- Status Overview Cards -->
<div class="row mb-4">
    <!-- Total Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-primary text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-list-alt fa-3x opacity-75"></i>
                    </div>
                    <div class="flex-grow-1 ms-3 text-end">
                        <h6 class="mb-1 text-white-50">Total Pengajuan</h6>
                        <h2 class="mb-0 fw-bold">
                            <?= $totalSubmissions ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-light border-start border-secondary border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock fa-2x text-secondary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pending</h6>
                        <h3 class="mb-0">
                            <?= $statusCounts['Pending'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revisi Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-light border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-edit fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Revisi</h6>
                        <h3 class="mb-0">
                            <?= $statusCounts['Revisi'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ditolak Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-light border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Ditolak</h6>
                        <h3 class="mb-0">
                            <?= $statusCounts['Ditolak'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disetujui Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-light border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Disetujui</h6>
                        <h3 class="mb-0">
                            <?= $statusCounts['Disetujui'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proses Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-light border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cog fa-2x text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Proses</h6>
                        <h3 class="mb-0">
                            <?= $statusCounts['Proses'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dikirim Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-light border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-truck fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Dikirim</h6>
                        <h3 class="mb-0">
                            <?= $statusCounts['Dikirim'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selesai Card -->
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
        <div class="card bg-light border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-double fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Selesai</h6>
                        <h3 class="mb-0">
                            <?= $statusCounts['Selesai'] ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Submissions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Pengajuan Terbaru</h5>
                    <a href="../transaksi_pengiriman/index.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($recentResult) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Resi</th>
                                    <th>Tanggal</th>
                                    <th>Penerima</th>
                                    <th>Tujuan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($recentResult)): ?>
                                    <tr>
                                        <td><strong>
                                                <?= htmlspecialchars($row['nomor_resi']) ?>
                                            </strong></td>
                                        <td>
                                            <?= $row['tanggal_pengajuan'] ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['nama_penerima']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($row['kota_tujuan']) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_color = 'secondary';
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
                                        <td>
                                            <a href="../transaksi_pengiriman/detail.php?id=<?= $row['id'] ?>"
                                                class="btn btn-sm btn-info">Detail</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        Belum ada pengajuan pengiriman.
                        <a href="../transaksi_pengiriman/add.php" class="alert-link">Buat pengajuan baru</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>