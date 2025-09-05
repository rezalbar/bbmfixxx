<?php
session_start();
// Pastikan hanya admin yang bisa mengakses dan menghapus data
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// 1. Validasi ID dan sumber dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['sumber'])) {
    // Jika parameter tidak lengkap, redirect ke halaman utama
    header("Location: admin.php");
    exit();
}
$rekapan_id = (int)$_GET['id'];
$sumber = $_GET['sumber']; // 'kendaraan' atau 'gudang'

// 2. Siapkan query DELETE menggunakan prepared statement
$query = "DELETE FROM tb_rekapan_bbm WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $rekapan_id);
    
    // 3. Eksekusi query dan siapkan notifikasi
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data rekapan berhasil dihapus.'];
    } else {
        $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Gagal menghapus data rekapan.'];
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Terjadi kesalahan pada database.'];
}

// 4. Redirect kembali ke halaman yang benar (sumber)
if ($sumber === 'gudang') {
    header("Location: gudang.php");
} else {
    // Default kembali ke halaman kendaraan jika sumber tidak dikenali
    header("Location: kendaraan.php");
}
exit();
?>
