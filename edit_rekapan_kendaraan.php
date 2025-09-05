<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: kendaraan.php");
    exit();
}
$id_rekapan = (int)$_GET['id'];

// Proses UPDATE data saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal_realisasi = $_POST['tanggal_realisasi'];
    $realisasi = $_POST['realisasi'];

    if (!empty($tanggal_realisasi) && isset($realisasi)) {
        $query_update = "UPDATE tb_rekapan_bbm SET tanggal_realisasi = ?, realisasi = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt, "sdi", $tanggal_realisasi, $realisasi, $id_rekapan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data rekapan berhasil diperbarui.'];
        } else {
            $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Gagal memperbarui data rekapan.'];
        }
        mysqli_stmt_close($stmt);
        header("Location: kendaraan.php");
        exit();
    }
}

// Ambil data rekapan yang akan diedit
$rekapan = null;
$query_get = "SELECT r.*, p.no_transaksi, p.quota_permintaan, k.no_plat, k.jenis_kendaraan
              FROM tb_rekapan_bbm r
              JOIN tb_permintaan_bbm p ON r.permintaan_id = p.id
              JOIN tb_master_kendaraan k ON p.kendaraan_id = k.id
              WHERE r.id = ? AND r.sumber_input = 'kendaraan'
              LIMIT 1";
$stmt_get = mysqli_prepare($conn, $query_get);
mysqli_stmt_bind_param($stmt_get, "i", $id_rekapan);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
if ($result_get && mysqli_num_rows($result_get) > 0) {
    $rekapan = mysqli_fetch_assoc($result_get);
} else {
    die("Data rekapan tidak ditemukan.");
}
mysqli_stmt_close($stmt_get);

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Rekapan Kendaraan - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Rekapan Kendaraan</h1>
        
        <!-- Info Transaksi -->
        <div class="mb-6 bg-gray-50 p-4 rounded-md border border-gray-200">
            <div class="mb-2">
                <span class="text-sm font-medium text-gray-500">No. Transaksi:</span>
                <span class="font-semibold text-gray-800 font-mono"><?= htmlspecialchars($rekapan['no_transaksi']) ?></span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Kendaraan:</span>
                <span class="font-semibold text-gray-800"><?= htmlspecialchars($rekapan['jenis_kendaraan'] . ' (' . $rekapan['no_plat'] . ')') ?></span>
            </div>
        </div>

        <form action="edit_rekapan_kendaraan.php?id=<?= $id_rekapan ?>" method="POST">
            <div class="mb-4">
                <label for="tanggal_realisasi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Realisasi</label>
                <input type="date" id="tanggal_realisasi" name="tanggal_realisasi" value="<?= htmlspecialchars($rekapan['tanggal_realisasi']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="realisasi" class="block text-sm font-medium text-gray-700 mb-1">Realisasi (Liter)</label>
                <input type="number" step="0.01" id="realisasi" name="realisasi" value="<?= htmlspecialchars($rekapan['realisasi']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="flex justify-between items-center mt-6">
                <a href="kendaraan.php" class="text-sm text-gray-600 hover:underline">&larr; Batal</a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>
</html>

