<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'gudang';
$is_admin = ($_SESSION['role'] === 'admin');


// --- AMBIL DATA PERMINTAAN UNTUK TABEL REFERENSI ---
$permintaan_bbm = [];
$query_permintaan = "SELECT p.*, k.no_plat, k.jenis_kendaraan 
                     FROM tb_permintaan_bbm p
                     JOIN tb_master_kendaraan k ON p.kendaraan_id = k.id
                     ORDER BY p.tanggal DESC, p.id DESC";
$result_permintaan = mysqli_query($conn, $query_permintaan);
if ($result_permintaan) {
    while ($row = mysqli_fetch_assoc($result_permintaan)) {
        $permintaan_bbm[] = $row;
    }
}

// --- AMBIL DATA REKAPAN UNTUK TABEL UTAMA ---
$rekapan_gudang = [];
$query_rekapan = "SELECT r.*, p.no_transaksi, p.quota_permintaan, k.no_plat, k.jenis_kendaraan
                  FROM tb_rekapan_bbm r
                  JOIN tb_permintaan_bbm p ON r.permintaan_id = p.id
                  JOIN tb_master_kendaraan k ON p.kendaraan_id = k.id
                  WHERE r.sumber_input = 'gudang'
                  ORDER BY r.tanggal_realisasi DESC, r.id DESC";
$result_rekapan = mysqli_query($conn, $query_rekapan);
if($result_rekapan) {
    while($row = mysqli_fetch_assoc($result_rekapan)) {
        $rekapan_gudang[] = $row;
    }
}

$status = $_GET['status'] ?? '';
$status_message = $_SESSION['status_message'] ?? null;
unset($_SESSION['status_message']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operasional Gudang - SI-BBM</title>
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
                    <h1 class="text-2xl font-bold text-gray-800">Operasional Gudang</h1>
                    <p class="text-gray-500">Rekapan Realisasi BBM Gudang.</p>
                </div>
               <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2"><img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="User Avatar" class="w-10 h-10 rounded-full"><span class="font-semibold text-gray-700"><?php echo htmlspecialchars($nama_user); ?></span></div>
                    <a href="logout.php" class="text-sm text-red-500 hover:underline">Logout</a>
                </div>
            </header>

            <!-- Notifikasi -->
            <?php if ($status === 'sukses_rekap'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">Berhasil! Data rekapan telah disimpan.</div>
            <?php endif; ?>
            <?php if ($status_message): ?>
                <div class="mb-4 px-4 py-3 rounded-lg <?= $status_message['type'] === 'sukses' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?>" role="alert">
                    <?= htmlspecialchars($status_message['message']) ?>
                </div>
            <?php endif; ?>

            <!-- Tabel Data Permintaan BBM (Referensi) -->
            <div class="bg-white p-6 rounded-xl shadow-md mb-8">
               <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold text-lg">Referensi: Data Permintaan BBM</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">No Transaksi</th>
                                <th class="py-3 px-4 text-left">Tanggal</th>
                                <th class="py-3 px-4 text-left">Kendaraan</th>
                                <th class="py-3 px-4 text-right">Quota Permintaan</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php if (empty($permintaan_bbm)): ?>
                                <tr><td colspan="4" class="py-4 px-4 text-center text-gray-400">Belum ada data permintaan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($permintaan_bbm as $data): ?>
                                <tr class="border-b">
                                    <td class="py-3 px-4 font-mono"><?= htmlspecialchars($data['no_transaksi']); ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars(date('d M Y', strtotime($data['tanggal']))); ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($data['jenis_kendaraan'] . ' (' . $data['no_plat'] . ')'); ?></td>
                                    <td class="py-3 px-4 text-right font-semibold"><?= htmlspecialchars(number_format($data['quota_permintaan'])) . ' L'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tabel Data Rekapan Gudang -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold text-lg">Data Rekapan Gudang</h2>
                    <?php if ($is_admin): ?>
                    <a href="tambah_rekapan_gudang.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Tambah Rekapan</a>
                    <?php endif; ?>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal</th>
                                <th class="py-3 px-4 text-left">No Transaksi</th>
                                <th class="py-3 px-4 text-left">Kendaraan</th>
                                <th class="py-3 px-4 text-right">Quota</th>
                                <th class="py-3 px-4 text-right">Realisasi</th>
                                <th class="py-3 px-4 text-right">Sisa</th>
                                <th class="py-3 px-4 text-left">Jenis BBM</th>
                                <?php if ($is_admin): ?><th class="py-3 px-4 text-center">Aksi</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                           <?php if (empty($rekapan_gudang)): ?>
                                <tr><td colspan="<?= $is_admin ? 8 : 7 ?>" class="py-4 px-4 text-center text-gray-400">Belum ada data rekapan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($rekapan_gudang as $data): ?>
                                <tr class="border-b">
                                    <td class="py-3 px-4"><?= htmlspecialchars(date('d M Y', strtotime($data['tanggal_realisasi']))); ?></td>
                                    <td class="py-3 px-4 font-mono"><?= htmlspecialchars($data['no_transaksi']); ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($data['jenis_kendaraan'] . ' (' . $data['no_plat'] . ')'); ?></td>
                                    <td class="py-3 px-4 text-right"><?= htmlspecialchars(number_format($data['quota_permintaan'])) . ' L'; ?></td>
                                    <td class="py-3 px-4 text-right font-bold text-green-600"><?= htmlspecialchars(number_format($data['realisasi'])) . ' L'; ?></td>
                                    <td class="py-3 px-4 text-right font-bold <?= $data['sisa'] < 0 ? 'text-red-600' : '' ?>"><?= htmlspecialchars(number_format($data['sisa'])) . ' L'; ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($data['jenis_bbm']); ?></td>
                                    <?php if ($is_admin): ?>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <a href="edit_rekapan_gudang.php?id=<?= $data['id'] ?>" class="font-medium text-blue-600 hover:underline">Edit</a>
                                        <a href="hapus_rekapan_gudang.php?id=<?= $data['id'] ?>&sumber=gudang" class="font-medium text-red-600 hover:underline" onclick="return confirm('Yakin ingin menghapus data rekapan ini?')">Hapus</a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="text-center mt-12 py-4">
                <p class="text-xs text-gray-400">&copy; <?php echo date("Y"); ?> SI-BBM | Dibuat oleh @Magang UMSIDA</p>
            </footer>
        </main>
    </div>

</body>
</html>
<?php
if (isset($conn)) mysqli_close($conn);
?>

