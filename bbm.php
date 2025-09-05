<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'admin';
$is_admin = ($_SESSION['role'] === 'admin');

// --- AMBIL SEMUA DATA BBM ---
$data_bbm = [];
$query = "SELECT * FROM tb_master_bbm ORDER BY nama_bbm ASC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_bbm[] = $row;
    }
}

// Ambil notifikasi dari session jika ada
$status_message = $_SESSION['status_message'] ?? null;
unset($_SESSION['status_message']); // Hapus setelah diambil

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen BBM - SI-BBM</title>
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
                    <li class="mb-2"><a href="admin.php" class="flex items-center gap-3 bg-blue-900 rounded-md p-3 text-sm font-semibold"><i class="ph-fill ph-gear-six text-xl"></i>Administrasi</a></li>
                    <li class="mb-2"><a href="kendaraan.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-truck text-xl"></i>Operasional Kendaraan</a></li>
                    <li class="mb-2"><a href="gudang.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-warehouse text-xl"></i>Operasional Gudang</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <main class="flex-1 p-8 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen BBM</h1>
                    <p class="text-gray-500">Kelola master data BBM.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2"><img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="User Avatar" class="w-10 h-10 rounded-full"><span class="font-semibold text-gray-700"><?php echo htmlspecialchars($nama_user); ?></span></div>
                    <a href="logout.php" class="text-sm text-red-500 hover:underline">Logout</a>
                </div>
            </header>
            
            <!-- Notifikasi -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_tambah'): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">Data BBM berhasil ditambahkan.</div>
            <?php endif; ?>
            <?php if ($status_message): ?>
                <div class="mb-4 px-4 py-3 rounded-lg 
                    <?= $status_message['type'] === 'sukses' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?>" role="alert">
                    <?= htmlspecialchars($status_message['message']) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold text-lg">Tabel Master BBM</h2>
                    <?php if ($is_admin): ?>
                        <a href="tambah_data_bbm.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">Tambah Data</a>
                    <?php endif; ?>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">No</th>
                                <th class="py-3 px-4 text-left">Nama BBM</th>
                                <th class="py-3 px-4 text-right">Harga</th>
                                <?php if ($is_admin): ?><th class="py-3 px-4 text-center">Aksi</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data_bbm)): ?>
                                <tr><td colspan="<?= $is_admin ? 4 : 3 ?>" class="py-4 px-4 text-center text-gray-400">Belum ada data.</td></tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($data_bbm as $data): ?>
                                <tr class="border-b">
                                    <td class="py-3 px-4"><?= $no++; ?></td>
                                    <td class="py-3 px-4"><?= htmlspecialchars($data['nama_bbm']); ?></td>
                                    <td class="py-3 px-4 text-right">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
                                    <?php if ($is_admin): ?>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <a href="edit_bbm.php?id=<?= $data['id'] ?>" class="font-medium text-blue-600 hover:underline">Edit</a>
                                        <a href="hapus_bbm.php?id=<?= $data['id'] ?>" class="font-medium text-red-600 hover:underline" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                                        <a href="view_transaksi_bbm.php?jenis=<?= urlencode($data['nama_bbm']) ?>" class="font-medium text-purple-600 hover:underline">View</a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    <a href="admin.php" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Administrasi</a>
                </div>
            </div>
        </main>
    </div>

</body>
</html>

