<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksi.php';

// Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: kendaraan.php");
    exit();
}
$id_permintaan = (int)$_GET['id'];

// Mulai transaksi untuk memastikan integritas data
mysqli_begin_transaction($conn);

try {
    // 1. Hapus data rekapan yang terkait
    $query_delete_rekapan = "DELETE FROM tb_rekapan_bbm WHERE permintaan_id = ?";
    $stmt_rekapan = mysqli_prepare($conn, $query_delete_rekapan);
    mysqli_stmt_bind_param($stmt_rekapan, "i", $id_permintaan);
    mysqli_stmt_execute($stmt_rekapan);
    mysqli_stmt_close($stmt_rekapan);

    // 2. Hapus data permintaan utama
    $query_delete_permintaan = "DELETE FROM tb_permintaan_bbm WHERE id = ?";
    $stmt_permintaan = mysqli_prepare($conn, $query_delete_permintaan);
    mysqli_stmt_bind_param($stmt_permintaan, "i", $id_permintaan);
    $sukses = mysqli_stmt_execute($stmt_permintaan);
    mysqli_stmt_close($stmt_permintaan);

    if ($sukses) {
        // Jika semua berhasil, simpan perubahan
        mysqli_commit($conn);
        $_SESSION['status_message'] = ['type' => 'sukses', 'message' => 'Data permintaan dan rekapan terkait berhasil dihapus.'];
    } else {
        // Jika gagal, batalkan semua perubahan
        mysqli_rollback($conn);
        $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Gagal menghapus data permintaan.'];
    }

} catch (Exception $e) {
    // Jika terjadi error, batalkan semua perubahan
    mysqli_rollback($conn);
    $_SESSION['status_message'] = ['type' => 'gagal', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
}

header("Location: kendaraan.php");
exit();
?>
