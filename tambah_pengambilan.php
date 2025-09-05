<?php
// Tidak perlu lagi mengambil data untuk dropdown
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Pengambilan BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-2xl bg-white p-8 rounded-xl shadow-2xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Formulir Pengambilan BBM</h1>
        
        <form action="proses_tambah_pengambilan.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal_pengambilan_jenisbbm" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengambilan</label>
                    <input type="date" id="tanggal_pengambilan_jenisbbm" name="tanggal_pengambilan_jenisbbm" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="nama_pemakai" class="block text-sm font-medium text-gray-700 mb-2">Nama Pemakai</label>
                    <input type="text" id="nama_pemakai" name="nama_pemakai" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Nama Driver">
                </div>
                <div>
                    <label for="no_polisi" class="block text-sm font-medium text-gray-700 mb-2">No. Polisi</label>
                    <!-- DIUBAH: dari select menjadi input text -->
                    <input type="text" id="no_polisi" name="no_polisi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Ketik nomor polisi">
                </div>
                <div>
                    <label for="no_spb" class="block text-sm font-medium text-gray-700 mb-2">No. SPB</label>
                    <!-- DIUBAH: dari select menjadi input text -->
                    <input type="text" id="no_spb" name="no_spb" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Ketik nomor SPB">
                </div>
                <div class="md:col-span-2">
                    <label for="pengambilan_jenisbbm" class="block text-sm font-medium text-gray-700 mb-2">Pengambilan (Liter)</label>
                    <input type="number" step="0.01" id="pengambilan_jenisbbm" name="pengambilan_jenisbbm" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="50.25">
                </div>
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Isi keterangan jika ada..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between mt-8">
                <a href="kendaraan.php" class="text-sm text-gray-600 hover:underline">Batal</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>

</body>
</html>
