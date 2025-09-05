<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'admin';

// 1. Validasi dan ambil ID kendaraan dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID Kendaraan tidak valid.");
}
$kendaraan_id = (int)$_GET['id'];

// 2. Ambil detail master kendaraan menggunakan metode yang lebih kompatibel
$kendaraan = null;
$query_kendaraan = "SELECT no_plat, jenis_kendaraan, quota_bbm FROM tb_master_kendaraan WHERE id = ? LIMIT 1";
$stmt_kendaraan = mysqli_prepare($conn, $query_kendaraan);
mysqli_stmt_bind_param($stmt_kendaraan, "i", $kendaraan_id);
mysqli_stmt_execute($stmt_kendaraan);
mysqli_stmt_bind_result($stmt_kendaraan, $no_plat, $jenis_kendaraan, $quota_bbm);

if (mysqli_stmt_fetch($stmt_kendaraan)) {
    $kendaraan = [
        'no_plat' => $no_plat,
        'jenis_kendaraan' => $jenis_kendaraan,
        'quota_bbm' => $quota_bbm
    ];
} else {
    die("Data kendaraan tidak ditemukan.");
}
mysqli_stmt_close($stmt_kendaraan);

// 3. Ambil semua data transaksi untuk kendaraan ini
$transaksi = [];
$total_realisasi = 0;
$query_transaksi = "SELECT 
                        p.tanggal, 
                        p.no_transaksi, 
                        p.jenis_bbm,
                        p.quota_permintaan, 
                        r.realisasi
                    FROM tb_permintaan_bbm p
                    LEFT JOIN tb_rekapan_bbm r ON p.id = r.permintaan_id AND r.sumber_input = 'kendaraan'
                    WHERE p.kendaraan_id = ?
                    ORDER BY p.tanggal DESC, p.id DESC";
$stmt_transaksi = mysqli_prepare($conn, $query_transaksi);
mysqli_stmt_bind_param($stmt_transaksi, "i", $kendaraan_id);
mysqli_stmt_execute($stmt_transaksi);
mysqli_stmt_bind_result($stmt_transaksi, $tanggal, $no_transaksi, $jenis_bbm, $quota_permintaan, $realisasi);

while (mysqli_stmt_fetch($stmt_transaksi)) {
    $transaksi[] = [
        'tanggal' => $tanggal,
        'no_transaksi' => $no_transaksi,
        'jenis_bbm' => $jenis_bbm,
        'quota_permintaan' => $quota_permintaan,
        'realisasi' => $realisasi
    ];
    $total_realisasi += $realisasi ?? 0;
}
mysqli_stmt_close($stmt_transaksi);

// Hitung sisa quota tahunan
$sisa_quota_tahunan = $kendaraan['quota_bbm'] - $total_realisasi;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi: <?= htmlspecialchars($kendaraan['jenis_kendaraan'] . ' - ' . $kendaraan['no_plat']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        <aside class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-6 flex items-center gap-4 border-b border-blue-700">
                <img src="img/sier.jpeg" alt="Logo Perusahaan" class="h-10">
                <span class="text-xl font-bold">SI-BBM</span>
            </div>
            <nav class="flex-1 p-4">
                <ul>
                    <li class="mb-2"><a href="admin.php" class="flex items-center gap-3 bg-blue-900 rounded-md p-3 text-sm font-semibold"><i class="ph-fill ph-gear-six text-xl"></i>Administrasi</a></li>
                    <li class="mb-2"><a href="kendaraan.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-truck text-xl"></i>Operasional Kendaraan</a></li>
                    <li class="mb-2"><a href="gudang.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-warehouse text-xl"></i>Operasional Gudang</a></li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Transaksi Kendaraan</h1>
                    <p class="text-gray-500 text-lg"><?= htmlspecialchars($kendaraan['jenis_kendaraan'] . ' (' . $kendaraan['no_plat'] . ')') ?></p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2"><img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="User Avatar" class="w-10 h-10 rounded-full"><span class="font-semibold text-gray-700"><?php echo htmlspecialchars($nama_user); ?></span></div>
                    <a href="logout.php" class="text-sm text-red-500 hover:underline">Logout</a>
                </div>
            </header>

            <!-- Kartu Ringkasan Kuota Tahunan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <p class="text-sm text-gray-500">QUOTA TAHUNAN</p>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($kendaraan['quota_bbm'], 0, ',', '.'); ?> Liter</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <p class="text-sm text-gray-500">TOTAL REALISASI</p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($total_realisasi, 0, ',', '.'); ?> Liter</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <p class="text-sm text-gray-500">SISA QUOTA TAHUNAN</p>
                    <p class="text-2xl font-bold <?= $sisa_quota_tahunan < 0 ? 'text-red-600' : 'text-blue-600' ?>">
                        <?= number_format($sisa_quota_tahunan, 0, ',', '.'); ?> Liter
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal</th>
                                <th class="py-3 px-4 text-left">No Transaksi</th>
                                <th class="py-3 px-4 text-left">Jenis BBM</th>
                                <th class="py-3 px-4 text-right">Quota Permintaan</th>
                                <th class="py-3 px-4 text-right">Realisasi</th>
                                <th class="py-3 px-4 text-right">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transaksi)): ?>
                                <tr><td colspan="6" class="py-4 px-4 text-center text-gray-400">Belum ada data transaksi untuk kendaraan ini.</td></tr>
                            <?php else: 
                                $total_quota_permintaan_display = 0;
                                $total_realisasi_display = 0;
                                foreach ($transaksi as $data):
                                    $realisasi = $data['realisasi'] ?? null;
                                    $sisa = ($realisasi !== null) ? $data['quota_permintaan'] - $realisasi : null;
                                    
                                    $total_quota_permintaan_display += $data['quota_permintaan'];
                                    $total_realisasi_display += $realisasi ?? 0;
                                ?>
                                <tr class="border-b">
                                    <td class="py-3 px-4"><?= htmlspecialchars(date('d M Y', strtotime($data['tanggal']))); ?></td>
                                    <td class="py-3 px-4 font-mono"><?= htmlspecialchars($data['no_transaksi']); ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($data['jenis_bbm']); ?></td>
                                    <td class="py-3 px-4 text-right"><?= number_format($data['quota_permintaan'], 0, ',', '.'); ?> L</td>
                                    <td class="py-3 px-4 text-right font-semibold <?= ($realisasi !== null) ? 'text-green-600' : 'text-gray-500' ?>">
                                        <?= ($realisasi !== null) ? number_format($realisasi, 0, ',', '.') . ' L' : 'Belum Direkap'; ?>
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold <?= ($sisa !== null && $sisa < 0) ? 'text-red-600' : '' ?>">
                                        <?= ($sisa !== null) ? number_format($sisa, 0, ',', '.') . ' L' : '-'; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="font-bold bg-gray-100">
                             <tr>
                                <td colspan="3" class="py-3 px-4 text-right">Total Transaksi di Halaman Ini</td>
                                <td class="py-3 px-4 text-right"><?= number_format($total_quota_permintaan_display, 0, ',', '.'); ?> L</td>
                                <td class="py-3 px-4 text-right"><?= number_format($total_realisasi_display, 0, ',', '.'); ?> L</td>
                                <td class="py-3 px-4 text-right text-blue-600"><?= number_format($total_quota_permintaan_display - $total_realisasi_display, 0, ',', '.'); ?> L</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-6">
                    <a href="kendaraan1.php" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Manajemen Kendaraan</a>
                </div>
            </div>
        </main>
    </div>

</body>
</html>

