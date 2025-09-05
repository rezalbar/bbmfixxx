<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Siapkan query penghapusan menggunakan prepared statement
    $query = "DELETE FROM tb_master_kendaraan WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data kendaraan berhasil dihapus.'];
    } else {
        $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Gagal menghapus data kendaraan.'];
    }
    mysqli_stmt_close($stmt);
}

// Kembali ke halaman kendaraan1
header("Location: kendaraan1.php");
exit();
?>
