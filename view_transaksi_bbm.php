<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'admin';

// Validasi input dari URL
if (!isset($_GET['jenis']) || empty($_GET['jenis'])) {
    die("Jenis BBM tidak ditemukan.");
}
$jenis_bbm = mysqli_real_escape_string($conn, $_GET['jenis']);

// 1. Ambil harga BBM dari tabel master
$harga_bbm = 0;
$query_harga = "SELECT harga FROM tb_master_bbm WHERE nama_bbm = '$jenis_bbm' LIMIT 1";
$result_harga = mysqli_query($conn, $query_harga);
if ($result_harga && mysqli_num_rows($result_harga) > 0) {
    $harga_bbm = mysqli_fetch_assoc($result_harga)['harga'];
} else {
    die("Harga untuk jenis BBM ini tidak ditemukan di master data.");
}

// 2. Ambil semua data permintaan yang sesuai dengan jenis BBM
$transaksi = [];
$total_keseluruhan = 0;
$query_transaksi = "SELECT p.tanggal, p.no_transaksi, p.quota_permintaan, k.jenis_kendaraan, k.no_plat
                    FROM tb_permintaan_bbm p
                    JOIN tb_master_kendaraan k ON p.kendaraan_id = k.id
                    WHERE p.jenis_bbm = '$jenis_bbm'
                    ORDER BY p.tanggal DESC, p.id DESC";
$result_transaksi = mysqli_query($conn, $query_transaksi);
if ($result_transaksi) {
    while ($row = mysqli_fetch_assoc($result_transaksi)) {
        $transaksi[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Transaksi <?= htmlspecialchars($jenis_bbm) ?> - SI-BBM</title>
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
                    <h1 class="text-2xl font-bold text-gray-800">Rincian Transaksi <?= htmlspecialchars($jenis_bbm) ?></h1>
                    <p class="text-gray-500">Harga per Liter: <span class="font-semibold">Rp <?= number_format($harga_bbm, 0, ',', '.') ?></span></p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2"><img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="User Avatar" class="w-10 h-10 rounded-full"><span class="font-semibold text-gray-700"><?php echo htmlspecialchars($nama_user); ?></span></div>
                    <a href="logout.php" class="text-sm text-red-500 hover:underline">Logout</a>
                </div>
            </header>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal</th>
                                <th class="py-3 px-4 text-left">No Transaksi</th>
                                <th class="py-3 px-4 text-left">Kendaraan</th>
                                <th class="py-3 px-4 text-right">Quota (Liter)</th>
                                <th class="py-3 px-4 text-right">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transaksi)): ?>
                                <tr><td colspan="5" class="py-4 px-4 text-center text-gray-400">Belum ada data transaksi untuk jenis BBM ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($transaksi as $data): ?>
                                <?php
                                    $subtotal = $data['quota_permintaan'] * $harga_bbm;
                                    $total_keseluruhan += $subtotal;
                                ?>
                                <tr class="border-b">
                                    <td class="py-3 px-4"><?= htmlspecialchars(date('d M Y', strtotime($data['tanggal']))); ?></td>
                                    <td class="py-3 px-4 font-mono"><?= htmlspecialchars($data['no_transaksi']); ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($data['jenis_kendaraan'] . ' (' . $data['no_plat'] . ')'); ?></td>
                                    <td class="py-3 px-4 text-right"><?= number_format($data['quota_permintaan'], 0, ',', '.'); ?> L</td>
                                    <td class="py-3 px-4 text-right">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="font-bold bg-gray-100">
                             <tr>
                                <td colspan="4" class="py-3 px-4 text-right">Total Keseluruhan</td>
                                <td class="py-3 px-4 text-right text-blue-600">Rp <?= number_format($total_keseluruhan, 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-6">
                    <a href="bbm.php" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Manajemen BBM</a>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
