<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Cek jika form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data dari form
    $nama_anggaran = mysqli_real_escape_string($conn, $_POST['nama_anggaran']);
    $mata_anggaran = mysqli_real_escape_string($conn, $_POST['mata_anggaran']);
    $nominal = mysqli_real_escape_string($conn, $_POST['nominal']);

    // Query untuk memasukkan data baru menggunakan prepared statement
    $query = "INSERT INTO tb_master_anggaran (nama_anggaran, mata_anggaran, nominal) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssd", $nama_anggaran, $mata_anggaran, $nominal);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, set pesan sukses dan redirect
        $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data anggaran baru berhasil ditambahkan.'];
        header("Location: anggaran.php");
        exit();
    } else {
        // Jika gagal, tampilkan error
        $pesan_error = "Error: " . mysqli_error($conn);
    }
}

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Anggaran - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
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
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Data Anggaran</h1>
                    <p class="text-gray-500">Isi form di bawah untuk menambahkan data anggaran baru.</p>
                </div>
            </header>

            <!-- Form Tambah Data -->
            <div class="bg-white p-6 rounded-xl shadow-md max-w-2xl mx-auto">
                <?php if (isset($pesan_error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert"><?= $pesan_error ?></div>
                <?php endif; ?>
                <form action="tambah_data_anggaran.php" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label for="nama_anggaran" class="block text-sm font-medium text-gray-700 mb-1">Nama Anggaran</label>
                            <select id="nama_anggaran" name="nama_anggaran" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="" disabled selected>Pilih Jenis BBM</option>
                                <option value="Solar">Solar</option>
                                <option value="Pertalite">Pertalite</option>
                            </select>
                        </div>
                        <div>
                            <label for="mata_anggaran" class="block mb-2 text-sm font-medium text-gray-900">Mata Anggaran</label>
                            <input type="text" id="mata_anggaran" name="mata_anggaran" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Akan terisi otomatis" required readonly>
                        </div>
                        <div>
                            <label for="nominal" class="block mb-2 text-sm font-medium text-gray-900">Nominal (Rp)</label>
                            <input type="number" id="nominal" name="nominal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Masukkan hanya angka, contoh: 50000000" required>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="anggaran.php" class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100">Batal</a>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Data</button>
                    </div>
                </form>
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

            namaAnggaranSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                if (mataAnggaranMap[selectedValue]) {
                    mataAnggaranInput.value = mataAnggaranMap[selectedValue];
                } else {
                    mataAnggaranInput.value = '';
                }
            });
        });
    </script>
</body>
</html>
