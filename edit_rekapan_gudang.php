<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: gudang.php?status=gagal");
    exit();
}
$rekapan_id = (int)$_GET['id'];

// Proses saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_rekapan'])) {
    $tanggal_realisasi = $_POST['tanggal_realisasi'];
    $realisasi = (float)$_POST['realisasi'];
    $quota_permintaan = (float)$_POST['quota_permintaan'];

    // Hitung ulang sisa
    $sisa = $quota_permintaan - $realisasi;

    $query_update = "UPDATE tb_rekapan_bbm SET tanggal_realisasi = ?, realisasi = ?, sisa = ? WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "sddi", $tanggal_realisasi, $realisasi, $sisa, $rekapan_id);

    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data rekapan berhasil diperbarui.'];
    } else {
        $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Gagal memperbarui data rekapan.'];
    }
    mysqli_stmt_close($stmt_update);
    header("Location: gudang.php");
    exit();
}

// Ambil data rekapan yang akan diedit
$data_rekapan = null;
$query_select = "SELECT r.*, p.no_transaksi, p.quota_permintaan, k.jenis_kendaraan, k.no_plat
                 FROM tb_rekapan_bbm r
                 JOIN tb_permintaan_bbm p ON r.permintaan_id = p.id
                 JOIN tb_master_kendaraan k ON p.kendaraan_id = k.id
                 WHERE r.id = ? AND r.sumber_input = 'gudang'";
$stmt_select = mysqli_prepare($conn, $query_select);
mysqli_stmt_bind_param($stmt_select, "i", $rekapan_id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
if ($result && mysqli_num_rows($result) > 0) {
    $data_rekapan = mysqli_fetch_assoc($result);
} else {
    die("Data rekapan tidak ditemukan.");
}
mysqli_stmt_close($stmt_select);

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rekapan Gudang - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <aside class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-6 flex items-center gap-4 border-b border-blue-700">
                <img src="img/sier.jpeg" alt="Logo Perusahaan" class="h-10"><span class="text-xl font-bold">SI-BBM</span>
            </div>
            <nav class="flex-1 p-4">
                <ul>
                    <li class="mb-2"><a href="admin.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-gear-six text-xl"></i>Administrasi</a></li>
                    <li class="mb-2"><a href="kendaraan.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-truck text-xl"></i>Operasional Kendaraan</a></li>
                    <li class="mb-2"><a href="gudang.php" class="flex items-center gap-3 bg-blue-900 rounded-md p-3 text-sm font-semibold"><i class="ph-fill ph-warehouse text-xl"></i>Operasional Gudang</a></li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Rekapan Gudang</h1>
                    <p class="text-gray-500">Ubah detail realisasi BBM dari gudang.</p>
                </div>
            </header>
            
            <div class="bg-white p-6 rounded-xl shadow-md max-w-4xl mx-auto">
                <form action="edit_rekapan_gudang.php?id=<?= $rekapan_id ?>" method="POST">
                    <input type="hidden" name="quota_permintaan" value="<?= $data_rekapan['quota_permintaan'] ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label class="block text-sm font-medium text-gray-500">No Transaksi</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800 font-mono"><?= htmlspecialchars($data_rekapan['no_transaksi']) ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label class="block text-sm font-medium text-gray-500">Kendaraan</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800"><?= htmlspecialchars($data_rekapan['jenis_kendaraan'] . ' (' . $data_rekapan['no_plat'] . ')') ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <label class="block text-sm font-medium text-gray-500">Quota Permintaan</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800"><?= htmlspecialchars(number_format($data_rekapan['quota_permintaan'])) . ' Liter' ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-md">
                             <label class="block text-sm font-medium text-gray-500">Jenis BBM</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800"><?= htmlspecialchars($data_rekapan['jenis_bbm']) ?></p>
                        </div>
                        <div>
                            <label for="tanggal_realisasi" class="block text-sm font-medium text-gray-700">Tanggal Realisasi</label>
                            <input type="date" id="tanggal_realisasi" name="tanggal_realisasi" value="<?= htmlspecialchars($data_rekapan['tanggal_realisasi']) ?>" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label for="realisasi" class="block text-sm font-medium text-gray-700">Realisasi (Liter)</label>
                            <input type="number" step="0.01" id="realisasi" name="realisasi" value="<?= htmlspecialchars($data_rekapan['realisasi']) ?>" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Masukkan jumlah liter" required>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-6">
                        <a href="gudang.php" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Operasional Gudang</a>
                        <button type="submit" name="update_rekapan" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

