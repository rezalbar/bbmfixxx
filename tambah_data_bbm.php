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
    $nama_bbm = mysqli_real_escape_string($conn, $_POST['nama_bbm']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    // Query untuk memasukkan data baru
    $query = "INSERT INTO tb_master_bbm (nama_bbm, harga) VALUES ('$nama_bbm', '$harga')";

    if (mysqli_query($conn, $query)) {
        // Jika berhasil, redirect kembali ke halaman bbm
        header("Location: bbm.php?status=sukses");
        exit();
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

$nama_user = $_SESSION['username'] ?? 'Admin Demo';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data BBM - SI-BBM</title>
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
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Data BBM</h1>
                    <p class="text-gray-500">Isi form di bawah untuk menambahkan data BBM baru.</p>
                </div>
            </header>

            <!-- Form Tambah Data -->
            <div class="bg-white p-6 rounded-xl shadow-md max-w-2xl mx-auto">
                <form action="tambah_data_bbm.php" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label for="nama_bbm" class="block mb-2 text-sm font-medium text-gray-900">Nama BBM</label>
                            <select id="nama_bbm" name="nama_bbm" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <option value="">-- Pilih Jenis BBM --</option>
                                <option value="Pertalite">Pertalite</option>
                                <option value="Solar">Solar</option>
                            </select>
                        </div>
                        <div>
                            <label for="harga" class="block mb-2 text-sm font-medium text-gray-900">Harga per Liter (Rp)</label>
                            <input type="number" id="harga" name="harga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Masukkan hanya angka, contoh: 10000" required>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-4">
                        <a href="bbm.php" class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100">Batal</a>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Data</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
