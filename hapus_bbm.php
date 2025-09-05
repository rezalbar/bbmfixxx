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
    $query = "DELETE FROM tb_master_bbm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, set notifikasi sukses
        $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data BBM berhasil dihapus.'];
    } else {
        // Jika gagal, set notifikasi gagal
        $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Gagal menghapus data BBM.'];
    }
    mysqli_stmt_close($stmt);
}

// Kembali ke halaman bbm
header("Location: bbm.php");
exit();
?>
