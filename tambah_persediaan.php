<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Proses form jika ada data yang dikirim (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data dari form
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $jenis_bbm = mysqli_real_escape_string($conn, $_POST['jenis_bbm']);
    $kuantitas_penerimaan = (int)$_POST['kuantitas_penerimaan'];
    $kuantitas_pengeluaran = (int)$_POST['kuantitas_pengeluaran'];
    $sisa_bbm_gudang = (int)$_POST['sisa_bbm_gudang'];
    $nomor_spb = mysqli_real_escape_string($conn, $_POST['nomor_spb']);
    $nomor_kupon = mysqli_real_escape_string($conn, $_POST['nomor_kupon']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Query SQL untuk memasukkan data baru
    $query = "INSERT INTO tabel_persediaan_gudang (tanggal_transaksi, jenis_bbm, kuantitas_penerimaan, kuantitas_pengeluaran, sisa_bbm_gudang, nomor_spb, nomor_kupon, keterangan) 
              VALUES ('$tanggal_transaksi', '$jenis_bbm', $kuantitas_penerimaan, $kuantitas_pengeluaran, $sisa_bbm_gudang, '$nomor_spb', '$nomor_kupon', '$keterangan')";

    if (mysqli_query($conn, $query)) {
        // Jika berhasil, set pesan sukses dan redirect ke halaman utama
        $_SESSION['success_message'] = "Data persediaan berhasil ditambahkan!";
    } else {
        // Jika gagal, set pesan error
        $_SESSION['error_message'] = "Gagal menambahkan data: " . mysqli_error($conn);
    }
    header("Location: gudang.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Persediaan - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 max-w-lg">
        <div class="bg-white p-8 rounded-xl shadow-md">
            <h1 class="text-2xl font-bold mb-6">Tambah Data Persediaan Gudang</h1>
            <form action="tambah_persediaan.php" method="post">
                <div class="mb-4">
                    <label for="tanggal_transaksi" class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                    <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="jenis_bbm" class="block text-sm font-medium text-gray-700">Jenis BBM</label>
                    <select name="jenis_bbm" id="jenis_bbm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="Pertalite">Pertalite</option>
                        <option value="Solar">Solar</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="kuantitas_penerimaan" class="block text-sm font-medium text-gray-700">Kuantitas Penerimaan (Liter)</label>
                    <input type="number" name="kuantitas_penerimaan" id="kuantitas_penerimaan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="kuantitas_pengeluaran" class="block text-sm font-medium text-gray-700">Kuantitas Pengeluaran (Liter)</label>
                    <input type="number" name="kuantitas_pengeluaran" id="kuantitas_pengeluaran" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="sisa_bbm_gudang" class="block text-sm font-medium text-gray-700">Sisa Stok Gudang (Liter)</label>
                    <input type="number" name="sisa_bbm_gudang" id="sisa_bbm_gudang" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="nomor_spb" class="block text-sm font-medium text-gray-700">Nomor SPB</label>
                    <input type="text" name="nomor_spb" id="nomor_spb" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="nomor_kupon" class="block text-sm font-medium text-gray-700">Nomor Kupon</label>
                    <input type="text" name="nomor_kupon" id="nomor_kupon" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <div class="flex items-center justify-end gap-4 mt-6">
                    <a href="gudang.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Batal</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
