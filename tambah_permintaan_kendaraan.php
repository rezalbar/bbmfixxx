<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'kendaraan';

// --- PROSES TAMBAH DATA ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_permintaan'])) {
    $no_transaksi = trim($_POST['no_transaksi']);
    $tanggal = trim($_POST['tanggal']);
    $kendaraan_id = trim($_POST['kendaraan_id']);
    $quota_permintaan = trim($_POST['quota_permintaan']);
    $jenis_bbm = trim($_POST['jenis_bbm']);

    // Validasi sederhana (bisa ditambahkan)
    if (!empty($no_transaksi) && !empty($tanggal) && !empty($kendaraan_id)) {
        $query = "INSERT INTO tb_permintaan_bbm (no_transaksi, tanggal, kendaraan_id, quota_permintaan, jenis_bbm) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssiis", $no_transaksi, $tanggal, $kendaraan_id, $quota_permintaan, $jenis_bbm);
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect kembali ke halaman utama dengan status sukses
            header("Location: kendaraan.php?status=sukses_tambah");
        } else {
            // Redirect dengan status gagal
            header("Location: tambah_permintaan_kendaraan.php?status=gagal_tambah");
        }
        mysqli_stmt_close($stmt);
        exit();
    }
}

// --- AMBIL DATA KENDARAAN UNTUK DROPDOWN ---
$kendaraan_list = [];
$query_kendaraan = "SELECT id, no_plat, jenis_kendaraan FROM tb_master_kendaraan ORDER BY jenis_kendaraan, no_plat";
$result_kendaraan = mysqli_query($conn, $query_kendaraan);
if ($result_kendaraan) {
    while ($row = mysqli_fetch_assoc($result_kendaraan)) {
        $kendaraan_list[] = $row;
    }
}

$status = $_GET['status'] ?? '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Permintaan BBM - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        <!-- ========== Sidebar ========== -->
        <aside class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-6 flex items-center gap-4 border-b border-blue-700">
                <img src="img/sier.jpeg" alt="Logo Perusahaan" class="h-10">
                <span class="text-xl font-bold">SI-BBM</span>
            </div>
            <nav class="flex-1 p-4">
                <ul>
                    <li class="mb-2"><a href="admin.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-gear-six text-xl"></i>Administrasi</a></li>
                    <li class="mb-2"><a href="kendaraan.php" class="flex items-center gap-3 bg-blue-900 rounded-md p-3 text-sm font-semibold"><i class="ph-fill ph-truck text-xl"></i>Operasional Kendaraan</a></li>
                    <li class="mb-2"><a href="gudang.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-warehouse text-xl"></i>Operasional Gudang</a></li>
                </ul>
            </nav>
        </aside>

        <!-- ========== Konten Utama ========== -->
        <main class="flex-1 p-8 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Permintaan BBM</h1>
                    <p class="text-gray-500">Isi formulir untuk membuat permintaan BBM baru.</p>
                </div>
                 <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="User Avatar" class="w-10 h-10 rounded-full">
                        <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($nama_user); ?></span>
                    </div>
                    <a href="logout.php" class="text-sm text-red-500 hover:underline">Logout</a>
                </div>
            </header>
            
            <?php if ($status === 'gagal_tambah'): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <strong class="font-bold">Gagal!</strong> Terjadi kesalahan saat menyimpan data.
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-md max-w-2xl mx-auto">
                <form action="tambah_permintaan_kendaraan.php" method="POST" class="space-y-4">
                    <div>
                        <label for="no_transaksi" class="block text-sm font-medium text-gray-700 mb-1">No Transaksi</label>
                        <input type="text" id="no_transaksi" name="no_transaksi" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                     <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label for="kendaraan_id" class="block text-sm font-medium text-gray-700 mb-1">Kendaraan</label>
                        <select id="kendaraan_id" name="kendaraan_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">-- Pilih Kendaraan --</option>
                            <?php foreach ($kendaraan_list as $kendaraan): ?>
                                <option value="<?= $kendaraan['id'] ?>"><?= htmlspecialchars($kendaraan['jenis_kendaraan'] . ' - ' . $kendaraan['no_plat']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="quota_permintaan" class="block text-sm font-medium text-gray-700 mb-1">Quota Permintaan (Liter)</label>
                        <input type="number" id="quota_permintaan" name="quota_permintaan" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="jenis_bbm" class="block text-sm font-medium text-gray-700 mb-1">Jenis BBM</label>
                        <select id="jenis_bbm" name="jenis_bbm" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="Solar">Solar</option>
                            <option value="Pertalite">Pertalite</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-4 pt-4">
                        <a href="kendaraan.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Kembali</a>
                        <button type="submit" name="tambah_permintaan" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

</body>
</html>
<?php
if (isset($conn)) {
    mysqli_close($conn);
}
?>
