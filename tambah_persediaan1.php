<?php // FILE: tambah_persediaan1.php ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data Persediaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-8">
    <div class="w-full max-w-2xl bg-white p-8 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Data Persediaan BBM</h1>
        <form action="proses_tambah_persediaan1.php" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Transaksi Persediaan</label>
                    <input type="date" name="tanggal_transaksi_persediaan_bbm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis BBM</label>
                    <select name="jenis_bbm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        <option value="solar">Solar</option>
                        <option value="pertalite">Pertalite</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">No. Kartu Persediaan</label>
                    <input type="text" name="no_kartu_persediaan_asli" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Transaksi BBM</label>
                    <input type="text" name="tanggal_transaksi_bbm" placeholder="Contoh: 2025-08-26" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Penerimaan (Liter)</label>
                    <input type="text" name="kuantitas_penerimaan_bbm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pengeluaran (Liter)</label>
                    <input type="text" name="kuantitas_pengeluaran_bbm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700">Sisa BBM (Liter)</label>
                    <input type="text" name="sisa_bbm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">No. SPB</label>
                    <input type="text" name="no_spb" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700">No. Kupon</label>
                    <input type="text" name="nomor_kupon_bbm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <input type="text" name="keterangan_transaksi" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="flex items-center gap-4 mt-6">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
                <a href="admin.php" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg text-center">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
