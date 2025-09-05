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
$id_permintaan = (int)$_GET['id'];

// Proses UPDATE data saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kendaraan_id = $_POST['kendaraan_id'];
    $tanggal = $_POST['tanggal'];
    $quota_permintaan = $_POST['quota_permintaan'];
    $jenis_bbm = $_POST['jenis_bbm'];

    if (!empty($kendaraan_id) && !empty($tanggal) && !empty($quota_permintaan) && !empty($jenis_bbm)) {
        $query_update = "UPDATE tb_permintaan_bbm SET kendaraan_id = ?, tanggal = ?, quota_permintaan = ?, jenis_bbm = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt, "isdsi", $kendaraan_id, $tanggal, $quota_permintaan, $jenis_bbm, $id_permintaan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data permintaan berhasil diperbarui.'];
        } else {
            $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Gagal memperbarui data permintaan.'];
        }
        mysqli_stmt_close($stmt);
        header("Location: kendaraan.php");
        exit();
    }
}

// Ambil data permintaan yang akan diedit
$permintaan = null;
$query_get = "SELECT * FROM tb_permintaan_bbm WHERE id = ? LIMIT 1";
$stmt_get = mysqli_prepare($conn, $query_get);
mysqli_stmt_bind_param($stmt_get, "i", $id_permintaan);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
if ($result_get && mysqli_num_rows($result_get) > 0) {
    $permintaan = mysqli_fetch_assoc($result_get);
} else {
    die("Data permintaan tidak ditemukan.");
}
mysqli_stmt_close($stmt_get);

// Ambil semua data kendaraan untuk dropdown
$data_kendaraan = [];
$result_kendaraan = mysqli_query($conn, "SELECT id, no_plat, jenis_kendaraan FROM tb_master_kendaraan ORDER BY jenis_kendaraan ASC");
if($result_kendaraan) while($row = mysqli_fetch_assoc($result_kendaraan)) $data_kendaraan[] = $row;

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Permintaan BBM - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Permintaan BBM</h1>
        <form action="edit_permintaan_kendaraan.php?id=<?= $id_permintaan ?>" method="POST">
            <div class="mb-4">
                <label for="no_transaksi" class="block text-sm font-medium text-gray-700 mb-1">No Transaksi</label>
                <input type="text" id="no_transaksi" value="<?= htmlspecialchars($permintaan['no_transaksi']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" readonly>
            </div>
            <div class="mb-4">
                <label for="kendaraan_id" class="block text-sm font-medium text-gray-700 mb-1">Kendaraan</label>
                <select id="kendaraan_id" name="kendaraan_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="">-- Pilih Kendaraan --</option>
                    <?php foreach ($data_kendaraan as $kendaraan): ?>
                        <option value="<?= $kendaraan['id'] ?>" <?= ($permintaan['kendaraan_id'] == $kendaraan['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kendaraan['jenis_kendaraan'] . ' (' . $kendaraan['no_plat'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Permintaan</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($permintaan['tanggal']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="quota_permintaan" class="block text-sm font-medium text-gray-700 mb-1">Quota Permintaan (Liter)</label>
                <input type="number" id="quota_permintaan" name="quota_permintaan" value="<?= htmlspecialchars($permintaan['quota_permintaan']) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="mb-4">
                <label for="jenis_bbm" class="block text-sm font-medium text-gray-700 mb-1">Jenis BBM</label>
                <select id="jenis_bbm" name="jenis_bbm" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="Pertalite" <?= ($permintaan['jenis_bbm'] == 'Pertalite') ? 'selected' : '' ?>>Pertalite</option>
                    <option value="Solar" <?= ($permintaan['jenis_bbm'] == 'Solar') ? 'selected' : '' ?>>Solar</option>
                </select>
            </div>
            <div class="flex justify-between items-center mt-6">
                <a href="kendaraan.php" class="text-sm text-gray-600 hover:underline">&larr; Batal</a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>
</html>

