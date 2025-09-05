<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'gudang'; // Pastikan sidebar gudang yang aktif

// --- PROSES SIMPAN REKAPAN ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_rekapan'])) {
    $permintaan_id = $_POST['permintaan_id'];
    $tanggal_realisasi = $_POST['tanggal_realisasi'];
    $realisasi = (float)$_POST['realisasi']; // Ubah ke float
    $quota_permintaan = (float)$_POST['quota_permintaan']; // Ambil dari hidden input
    $jenis_bbm = $_POST['jenis_bbm'];
    
    // Hitung sisa
    $sisa = $quota_permintaan - $realisasi;

    if (!empty($permintaan_id) && !empty($tanggal_realisasi) && isset($realisasi)) {
        $query = "INSERT INTO tb_rekapan_bbm (permintaan_id, tanggal_realisasi, realisasi, sisa, jenis_bbm, sumber_input) VALUES (?, ?, ?, ?, ?, 'gudang')";
        $stmt = mysqli_prepare($conn, $query);
        // Tipe data: i=integer, s=string, d=double
        mysqli_stmt_bind_param($stmt, "isdds", $permintaan_id, $tanggal_realisasi, $realisasi, $sisa, $jenis_bbm);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: gudang.php?status=sukses_rekap");
        } else {
            header("Location: tambah_rekapan_gudang.php?status=gagal_rekap");
        }
        mysqli_stmt_close($stmt);
        exit();
    }
}

// --- AMBIL DATA TRANSAKSI YANG BELUM DI REKAP OLEH 'GUDANG' ---
$transaksi_tersedia = [];
$query_tersedia = "SELECT p.id, p.no_transaksi, k.jenis_kendaraan, k.no_plat
                   FROM tb_permintaan_bbm p
                   JOIN tb_master_kendaraan k ON p.kendaraan_id = k.id
                   WHERE p.id NOT IN (SELECT permintaan_id FROM tb_rekapan_bbm WHERE sumber_input = 'gudang' AND permintaan_id IS NOT NULL)
                   ORDER BY p.tanggal DESC, p.id DESC";
$result_tersedia = mysqli_query($conn, $query_tersedia);
if ($result_tersedia) {
    while($row = mysqli_fetch_assoc($result_tersedia)) {
        $transaksi_tersedia[] = $row;
    }
}


// --- LOGIKA CARI TRANSAKSI BERDASARKAN ID DARI DROPDOWN ---
$data_permintaan_cari = null;
if (isset($_GET['permintaan_id']) && !empty($_GET['permintaan_id'])) {
    $permintaan_id_cari = (int)$_GET['permintaan_id'];
    $query_cari = "SELECT p.id, p.no_transaksi, p.tanggal, p.quota_permintaan, p.jenis_bbm, k.no_plat, k.jenis_kendaraan 
                   FROM tb_permintaan_bbm p
                   JOIN tb_master_kendaraan k ON p.kendaraan_id = k.id
                   WHERE p.id = ? LIMIT 1";
    $stmt_cari = mysqli_prepare($conn, $query_cari);
    mysqli_stmt_bind_param($stmt_cari, "i", $permintaan_id_cari);
    mysqli_stmt_execute($stmt_cari);
    $result_cari = mysqli_stmt_get_result($stmt_cari);

    if (mysqli_num_rows($result_cari) > 0) {
        $data_permintaan_cari = mysqli_fetch_assoc($result_cari);
    } else {
        $pesan_error_cari = "ID Permintaan tidak valid.";
    }
    mysqli_stmt_close($stmt_cari);
}

$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rekapan Gudang - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-6 flex items-center gap-4 border-b border-blue-700">
                <img src="img/sier.jpeg" alt="Logo Perusahaan" class="h-10">
                <span class="text-xl font-bold">SI-BBM</span>
            </div>
            <nav class="flex-1 p-4">
                <ul>
                    <li class="mb-2"><a href="admin.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-gear-six text-xl"></i>Administrasi</a></li>
                    <li class="mb-2"><a href="kendaraan.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-truck text-xl"></i>Operasional Kendaraan</a></li>
                    <li class="mb-2"><a href="gudang.php" class="flex items-center gap-3 bg-blue-900 rounded-md p-3 text-sm font-semibold"><i class="ph-fill ph-warehouse text-xl"></i>Operasional Gudang</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <main class="flex-1 p-8 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Rekapan Realisasi Gudang</h1>
                    <p class="text-gray-500">Input realisasi BBM untuk gudang.</p>
                </div>
                 <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2"><img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="User Avatar" class="w-10 h-10 rounded-full"><span class="font-semibold text-gray-700"><?php echo htmlspecialchars($nama_user); ?></span></div>
                    <a href="logout.php" class="text-sm text-red-500 hover:underline">Logout</a>
                </div>
            </header>

            <?php if ($status === 'gagal_rekap'): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">Gagal! Terjadi kesalahan saat menyimpan data.</div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-md max-w-4xl mx-auto">
                <form action="tambah_rekapan_gudang.php" method="GET" class="mb-6 pb-6 border-b">
                    <h3 class="font-bold text-lg mb-4">Langkah 1: Pilih Transaksi Permintaan</h3>
                    <div>
                        <label for="permintaan_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih No. Transaksi (hanya yang belum direkap gudang)</label>
                        <select id="permintaan_id" name="permintaan_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="this.form.submit()">
                            <option value="">-- Pilih Transaksi --</option>
                            <?php if (empty($transaksi_tersedia)): ?>
                                <option value="" disabled>Tidak ada permintaan baru untuk direkap</option>
                            <?php else: ?>
                                <?php foreach ($transaksi_tersedia as $trx): ?>
                                    <option value="<?= $trx['id'] ?>" <?= (isset($_GET['permintaan_id']) && $_GET['permintaan_id'] == $trx['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($trx['no_transaksi'] . ' | ' . $trx['jenis_kendaraan'] . ' (' . $trx['no_plat'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                     <?php if (isset($pesan_error_cari)): ?>
                        <p class="text-red-500 text-sm mt-2"><?= $pesan_error_cari ?></p>
                    <?php endif; ?>
                </form>

                <?php if ($data_permintaan_cari): ?>
                <form action="tambah_rekapan_gudang.php" method="POST">
                    <h3 class="font-bold text-lg mb-4">Langkah 2: Isi Detail Realisasi</h3>
                    <input type="hidden" name="permintaan_id" value="<?= $data_permintaan_cari['id'] ?>">
                    <input type="hidden" name="jenis_bbm" value="<?= htmlspecialchars($data_permintaan_cari['jenis_bbm']) ?>">
                    <input type="hidden" name="quota_permintaan" value="<?= $data_permintaan_cari['quota_permintaan'] ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label class="block text-sm font-medium text-gray-500">Kendaraan</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800"><?= htmlspecialchars($data_permintaan_cari['jenis_kendaraan'] . ' (' . $data_permintaan_cari['no_plat'] . ')') ?></p>
                        </div>
                         <div class="bg-gray-50 p-4 rounded-md">
                            <label class="block text-sm font-medium text-gray-500">Quota Permintaan</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800"><?= htmlspecialchars(number_format($data_permintaan_cari['quota_permintaan'])) . ' Liter' ?></p>
                        </div>
                        <div>
                            <label for="tanggal_realisasi" class="block text-sm font-medium text-gray-700">Tanggal Realisasi</label>
                            <input type="date" id="tanggal_realisasi" name="tanggal_realisasi" value="<?= date('Y-m-d') ?>" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label for="realisasi" class="block text-sm font-medium text-gray-700">Realisasi (Liter)</label>
                            <input type="number" step="0.01" id="realisasi" name="realisasi" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Masukkan jumlah liter" required>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-6">
                        <a href="gudang.php" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Operasional Gudang</a>
                        <button type="submit" name="simpan_rekapan" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Rekapan</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>

            <footer class="text-center mt-12 py-4">
                <p class="text-xs text-gray-400">&copy; <?php echo date("Y"); ?> SI-BBM | Dibuat oleh @rezalbar 231080200163</p>
            </footer>
        </main>
    </div>

</body>
</html>
<?php
if (isset($conn)) mysqli_close($conn);
?>

