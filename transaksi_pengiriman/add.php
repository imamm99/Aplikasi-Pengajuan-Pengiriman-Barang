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

$error = $success = '';

// Fetch Barang list for dropdown (with weight)
$barang_options = [];
$q_barang = mysqli_query($connection, "SELECT id, nama_barang, berat_kg FROM barang ORDER BY nama_barang ASC");
while ($row = mysqli_fetch_assoc($q_barang)) {
    $barang_options[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? ''))
        die('Invalid CSRF token.');

    // 1. Validate Master Data
    $nama_pengirim = trim($_POST['nama_pengirim'] ?? '');
    $alamat_pengirim = trim($_POST['alamat_pengirim'] ?? '');
    $no_telepon_pengirim = trim($_POST['no_telepon_pengirim'] ?? '');
    $nama_penerima = trim($_POST['nama_penerima'] ?? '');
    $alamat_penerima = trim($_POST['alamat_penerima'] ?? '');
    $no_telepon_penerima = trim($_POST['no_telepon_penerima'] ?? '');
    $kota_tujuan = trim($_POST['kota_tujuan'] ?? '');
    $tanggal_pengajuan = trim($_POST['tanggal_pengajuan'] ?? '');
    $items = $_POST['items'] ?? [];

    if (empty($nama_pengirim) || empty($nama_penerima) || empty($items)) {
        $error = "Data pengirim, penerima, dan minimal satu barang wajib diisi.";
    }

    if (!$error) {
        mysqli_begin_transaction($connection);
        try {
            // 2. Insert Master (Pengajuan)
            $nomor_resi = 'REG' . date('YmdHis') . rand(100, 999);
            $user_id = $_SESSION['user_id'];
            $status = 'Pending';
            $keterangan = $_POST['keterangan'] ?? '';
            $total_item = count($items);

            $stmt = mysqli_prepare($connection, "INSERT INTO `pengajuan_pengiriman` 
                (`nomor_resi`, `tanggal_pengajuan`, `user_id`, 
                 `nama_pengirim`, `alamat_pengirim`, `no_telepon_pengirim`,
                 `nama_penerima`, `alamat_penerima`, `no_telepon_penerima`, `kota_tujuan`,
                 `keterangan`, `total_item`, `status_pengiriman`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param(
                $stmt,
                "ssissssssssis",
                $nomor_resi,
                $tanggal_pengajuan,
                $user_id,
                $nama_pengirim,
                $alamat_pengirim,
                $no_telepon_pengirim,
                $nama_penerima,
                $alamat_penerima,
                $no_telepon_penerima,
                $kota_tujuan,
                $keterangan,
                $total_item,
                $status
            );
            mysqli_stmt_execute($stmt);
            $pengajuan_id = mysqli_insert_id($connection);
            mysqli_stmt_close($stmt);

            // 3. Validate stock availability
            $stock_errors = [];
            foreach ($items as $item) {
                $barang_id = (int) $item['barang_id'];
                $qty = (int) $item['qty'];

                if ($barang_id > 0 && $qty > 0) {
                    $stock_stmt = mysqli_prepare($connection, "SELECT nama_barang, stok FROM barang WHERE id = ?");
                    mysqli_stmt_bind_param($stock_stmt, "i", $barang_id);
                    mysqli_stmt_execute($stock_stmt);
                    $stock_result = mysqli_stmt_get_result($stock_stmt);
                    $barang_data = mysqli_fetch_assoc($stock_result);
                    mysqli_stmt_close($stock_stmt);

                    if ($barang_data && $barang_data['stok'] < $qty) {
                        $stock_errors[] = "{$barang_data['nama_barang']}: Stok tersedia {$barang_data['stok']}, diminta {$qty}";
                    }
                }
            }

            // If stock insufficient, rollback and throw error
            if (!empty($stock_errors)) {
                mysqli_rollback($connection);
                throw new Exception("Stok tidak mencukupi:\n" . implode("\n", $stock_errors));
            }

            // 4. Insert Details (Items)
            $stmt_detail = mysqli_prepare($connection, "INSERT INTO `detail_pengajuan` (`pengajuan_id`, `barang_id`, `qty_barang`, `catatan`) VALUES (?, ?, ?, ?)");

            foreach ($items as $item) {
                $barang_id = (int) $item['barang_id'];
                $qty = (int) $item['qty'];
                $catatan = trim($item['catatan']);

                if ($barang_id > 0 && $qty > 0) {
                    mysqli_stmt_bind_param($stmt_detail, "iiis", $pengajuan_id, $barang_id, $qty, $catatan);
                    mysqli_stmt_execute($stmt_detail);
                }
            }
            mysqli_stmt_close($stmt_detail);

            mysqli_commit($connection);
            header("Location: detail.php?id=$pengajuan_id");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($connection);
            $error = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}
$csrfToken = generateCSRFToken();
?>
<?php include '../views/' . $THEME . '/header.php'; ?>
<?php include '../views/' . $THEME . '/sidebar.php'; ?>
<?php include '../views/' . $THEME . '/topnav.php'; ?>
<?php include '../views/' . $THEME . '/upper_block.php'; ?>

<h2>Buat Pengajuan Pengiriman Baru</h2>
<?php if ($error): ?>
    <?= showAlert($error, 'danger') ?>
<?php endif; ?>

<form method="POST" id="formPengajuan">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="row">
        <!-- Kolom Kiri: Data Identitas -->
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Informasi Pengiriman</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pengajuan" class="form-control" value="<?= date('Y-m-d') ?>"
                            required>
                    </div>

                    <h6 class="text-primary mt-3">Pengirim</h6>
                    <div class="mb-2">
                        <input type="text" name="nama_pengirim" class="form-control form-control-sm"
                            placeholder="Nama Pengirim" required>
                    </div>
                    <div class="mb-2">
                        <textarea name="alamat_pengirim" class="form-control form-control-sm"
                            placeholder="Alamat Pengirim" rows="2" required></textarea>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="no_telepon_pengirim" class="form-control form-control-sm"
                            placeholder="No. Telp Pengirim" required>
                    </div>

                    <h6 class="text-success mt-3">Penerima</h6>
                    <div class="mb-2">
                        <input type="text" name="nama_penerima" class="form-control form-control-sm"
                            placeholder="Nama Penerima" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="kota_tujuan" class="form-control form-control-sm"
                            placeholder="Kota Tujuan / Kecamatan" required>
                    </div>
                    <div class="mb-2">
                        <textarea name="alamat_penerima" class="form-control form-control-sm"
                            placeholder="Alamat Lengkap Penerima" rows="2" required></textarea>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="no_telepon_penerima" class="form-control form-control-sm"
                            placeholder="No. Telp Penerima" required>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control form-control-sm"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Barang -->
        <div class="col-md-7">
            <div class="card mb-3 h-100">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <span>Daftar Barang</span>
                    <button type="button" class="btn btn-sm btn-light" onclick="addItemRow()">+ Tambah Barang</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Barang</th>
                                    <th width="15%">Qty</th>
                                    <th width="20%">Berat (Kg)</th>
                                    <th width="25%">Catatan</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows dynamic -->
                            </tbody>
                            <tfoot class="table-light font-weight-bold">
                                <tr>
                                    <td colspan="2" class="text-end">Total Berat Est:</td>
                                    <td colspan="3"><span id="grandTotalWeight">0.00</span> Kg</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="p-3 text-muted small text-center" id="emptyMsg">
                        Belum ada barang dipilih. Klik "+ Tambah Barang".
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100 btn-lg">Simpan Pengajuan</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const barangList = <?= json_encode($barang_options) ?>;

    function addItemRow() {
        document.getElementById('emptyMsg').style.display = 'none';
        const tbody = document.querySelector('#itemsTable tbody');
        const index = tbody.rows.length;

        let optionsHtml = '<option value="">-- Pilih --</option>';
        barangList.forEach(b => {
            optionsHtml += `<option value="${b.id}" data-berat="${b.berat_kg}">${b.nama_barang}</option>`;
        });

        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td>
            <select name="items[${index}][barang_id]" class="form-select form-select-sm item-select" required onchange="calcRow(this)">
                ${optionsHtml}
            </select>
        </td>
        <td>
            <input type="number" name="items[${index}][qty]" class="form-control form-control-sm item-qty" min="1" value="1" required oninput="calcRow(this)">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-weight" value="0.00" readonly>
        </td>
        <td>
            <input type="text" name="items[${index}][catatan]" class="form-control form-control-sm" placeholder="Warna/Ket">
        </td>
        <td>
            <button type="button" class="btn btn-xs btn-danger" onclick="removeRow(this)">x</button>
        </td>
    `;
        tbody.appendChild(tr);
    }

    function removeRow(btn) {
        const row = btn.closest('tr');
        row.remove();
        updateGrandTotal();
        if (document.querySelector('#itemsTable tbody').rows.length === 0) {
            document.getElementById('emptyMsg').style.display = 'block';
        }
    }

    function calcRow(el) {
        const row = el.closest('tr');
        const select = row.querySelector('.item-select');
        const qtyInput = row.querySelector('.item-qty');
        const weightInput = row.querySelector('.item-weight');

        const selectedOption = select.options[select.selectedIndex];
        const unitWeight = parseFloat(selectedOption.getAttribute('data-berat')) || 0;
        const qty = parseFloat(qtyInput.value) || 0;

        const totalRowWeight = unitWeight * qty;
        weightInput.value = totalRowWeight.toFixed(2);

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-weight').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotalWeight').innerText = total.toFixed(2);
    }

    // Init with one row
    document.addEventListener('DOMContentLoaded', addItemRow);
</script>

<?php include '../views/' . $THEME . '/lower_block.php'; ?>
<?php include '../views/' . $THEME . '/footer.php'; ?>