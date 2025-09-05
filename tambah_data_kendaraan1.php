<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'admin';
$pesan_error = '';

// --- PROSES SIMPAN DATA ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $no_plat = mysqli_real_escape_string($conn, $_POST['no_plat']);
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $detail_kendaraan = mysqli_real_escape_string($conn, $_POST['detail_kendaraan']);
    $quota_bbm_input = (float)$_POST['quota_bbm']; // Ambil input sebagai angka

    // Kalikan quota dengan 12 sebelum disimpan
    $quota_bbm_final = $quota_bbm_input * 12;

    $query = "INSERT INTO tb_master_kendaraan (no_plat, jenis_kendaraan, detail_kendaraan, quota_bbm) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    // Gunakan quota yang sudah dikalikan
    mysqli_stmt_bind_param($stmt, "sssd", $no_plat, $jenis_kendaraan, $detail_kendaraan, $quota_bbm_final);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: kendaraan1.php?status=sukses_tambah");
        exit();
    } else {
        $pesan_error = "Gagal menyimpan data.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Kendaraan - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Data Kendaraan</h1>
                    <p class="text-gray-500">Input detail master data kendaraan baru.</p>
                </div>
            </header>

            <div class="bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto">
                <?php if ($pesan_error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert"><?= $pesan_error ?></div>
                <?php endif; ?>
                <form action="tambah_data_kendaraan.php" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label for="no_plat" class="block text-sm font-medium text-gray-700">No. Plat</label>
                            <input type="text" id="no_plat" name="no_plat" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700">Jenis Kendaraan</label>
                            <input type="text" id="jenis_kendaraan" name="jenis_kendaraan" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        </div>
                         <div>
                            <label for="detail_kendaraan" class="block text-sm font-medium text-gray-700">Detail Kendaraan</label>
                            <textarea id="detail_kendaraan" name="detail_kendaraan" rows="3" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Contoh: Forklift, Mobil, dll."></textarea>
                        </div>
                        <div>
                            <label for="quota_bbm" class="block text-sm font-medium text-gray-700">Quota BBM Bulanan (Liter)</label>
                            <input type="number" step="0.01" id="quota_bbm" name="quota_bbm" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Contoh: 100" required>
                             <p class="text-xs text-gray-500 mt-1">Nilai ini akan dikalikan 12 untuk kuota tahunan.</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-6">
                        <a href="kendaraan1.php" class="text-sm text-gray-600 hover:underline">&larr; Batal</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Data</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

