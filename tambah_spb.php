<?php
// Tidak perlu lagi mengambil data untuk dropdown
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Surat Permintaan BBM (SPB)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-4xl bg-white p-8 rounded-xl shadow-2xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Formulir Buat SPB Baru</h1>
        
        <form action="proses_tambah_spb.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div>
                    <div class="mb-4">
                        <label for="no_spb" class="block text-sm font-medium text-gray-700 mb-2">No. SPB</label>
                        <input type="text" id="no_spb" name="no_spb" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: SPB/OPS/001">
                    </div>
                    <div class="mb-4">
                        <label for="no_polisi" class="block text-sm font-medium text-gray-700 mb-2">No. Polisi</label>
                        <!-- DIUBAH: dari select menjadi input text -->
                        <input type="text" id="no_polisi" name="no_polisi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Ketik nomor polisi">
                    </div>
                    <div class="mb-4">
                        <label for="no_urut" class="block text-sm font-medium text-gray-700 mb-2">No. Urut</label>
                        <input type="number" id="no_urut" name="no_urut" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="1">
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_spb" class="block text-sm font-medium text-gray-700 mb-2">Tanggal SPB</label>
                        <input type="date" id="tanggal_spb" name="tanggal_spb" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                     <div class="mb-4">
                        <label for="jenis_bbm" class="block text-sm font-medium text-gray-700 mb-2">Jenis BBM</label>
                        <input type="text" id="jenis_bbm" name="jenis_bbm" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: Pertamina Dex">
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div>
                    <div class="mb-4">
                        <label for="departemen" class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                        <input type="text" id="departemen" name="departemen" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Operasional">
                    </div>
                    <div class="mb-4">
                        <label for="pengambilan_jenisbbm" class="block text-sm font-medium text-gray-700 mb-2">Pengambilan BBM (Liter)</label>
                        <input type="number" step="0.01" id="pengambilan_jenisbbm" name="pengambilan_jenisbbm" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="45.50">
                    </div>
                    <div class="mb-4">
                        <label for="pejabat_pengajuan" class="block text-sm font-medium text-gray-700 mb-2">Pejabat Pengajuan</label>
                        <input type="text" id="pejabat_pengajuan" name="pejabat_pengajuan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Nama Pejabat">
                    </div>
                    <div class="mb-4">
                        <label for="pejabat_persetujuan" class="block text-sm font-medium text-gray-700 mb-2">Pejabat Persetujuan</label>
                        <input type="text" id="pejabat_persetujuan" name="pejabat_persetujuan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Nama Pejabat">
                    </div>
                    <div class="mb-4">
                        <label for="pejabat_penerima" class="block text-sm font-medium text-gray-700 mb-2">Pejabat Penerima</label>
                        <input type="text" id="pejabat_penerima" name="pejabat_penerima" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Nama Pejabat">
                    </div>
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
