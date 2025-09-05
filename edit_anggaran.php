<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'admin'; // Agar sidebar Administrasi tetap aktif
$id_anggaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data_anggaran = null;
$pesan_error = '';

// Opsi untuk dropdown Nama Anggaran
$opsi_nama_anggaran = [
    'Solar',
    'Pertalite'
];

// --- PROSES UPDATE DATA ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_anggaran'])) {
    $id_update = $_POST['id_anggaran'];
    $nama_anggaran = mysqli_real_escape_string($conn, $_POST['nama_anggaran']);
    $mata_anggaran = mysqli_real_escape_string($conn, $_POST['mata_anggaran']);
    $nominal = $_POST['nominal'];

    $query_update = "UPDATE tb_master_anggaran SET nama_anggaran = ?, mata_anggaran = ?, nominal = ? WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "ssdi", $nama_anggaran, $mata_anggaran, $nominal, $id_update);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data anggaran berhasil diperbarui.'];
        header("Location: anggaran.php");
        exit();
    } else {
        $pesan_error = "Gagal memperbarui data.";
    }
}

// --- AMBIL DATA YANG AKAN DIEDIT ---
if ($id_anggaran > 0) {
    $query_get = "SELECT * FROM tb_master_anggaran WHERE id = ? LIMIT 1";
    $stmt_get = mysqli_prepare($conn, $query_get);
    mysqli_stmt_bind_param($stmt_get, "i", $id_anggaran);
    mysqli_stmt_execute($stmt_get);
    $result_get = mysqli_stmt_get_result($stmt_get);
    if ($result_get && mysqli_num_rows($result_get) > 0) {
        $data_anggaran = mysqli_fetch_assoc($result_get);
    } else {
        $pesan_error = "Data anggaran tidak ditemukan.";
    }
} else {
    $pesan_error = "ID anggaran tidak valid.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Anggaran - SI-BBM</title>
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
                    <h1 class="text-2xl font-bold text-gray-800">Edit Data Anggaran</h1>
                    <p class="text-gray-500">Ubah detail master data anggaran.</p>
                </div>
            </header>

            <div class="bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto">
                <?php if ($pesan_error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert"><?= $pesan_error ?></div>
                    <a href="anggaran.php" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
                <?php elseif ($data_anggaran): ?>
                <form action="edit_anggaran.php?id=<?= $id_anggaran ?>" method="POST">
                    <input type="hidden" name="id_anggaran" value="<?= $data_anggaran['id'] ?>">
                    <div class="space-y-4">
                        <div>
                            <label for="nama_anggaran" class="block text-sm font-medium text-gray-700">Nama Anggaran</label>
                            <select id="nama_anggaran" name="nama_anggaran" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Pilih Nama Anggaran</option>
                                <?php foreach ($opsi_nama_anggaran as $opsi): ?>
                                    <option value="<?= htmlspecialchars($opsi) ?>" <?= ($data_anggaran['nama_anggaran'] == $opsi) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($opsi) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="mata_anggaran" class="block text-sm font-medium text-gray-700">Mata Anggaran</label>
                            <input type="text" id="mata_anggaran" name="mata_anggaran" value="<?= htmlspecialchars($data_anggaran['mata_anggaran']) ?>" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" required readonly>
                        </div>
                        <div>
                            <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal</label>
                            <input type="number" id="nominal" name="nominal" value="<?= htmlspecialchars($data_anggaran['nominal']) ?>" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-6">
                        <a href="anggaran.php" class="text-sm text-gray-600 hover:underline">&larr; Batal</a>
                        <button type="submit" name="update_anggaran" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">Update Data</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const namaAnggaranSelect = document.getElementById('nama_anggaran');
            const mataAnggaranInput = document.getElementById('mata_anggaran');

            const mataAnggaranMap = {
                'Solar': '11.11.11.01',
                'Pertalite': '11.11.11.02'
            };

            function updateMataAnggaran() {
                const selectedNamaAnggaran = namaAnggaranSelect.value;
                if (mataAnggaranMap[selectedNamaAnggaran]) {
                    mataAnggaranInput.value = mataAnggaranMap[selectedNamaAnggaran];
                } else {
                    mataAnggaranInput.value = '';
                }
            }

            // Update on change
            namaAnggaranSelect.addEventListener('change', updateMataAnggaran);

            // Initial update on page load to set the value based on current selection
            updateMataAnggaran();
        });
    </script>
</body>
</html>

