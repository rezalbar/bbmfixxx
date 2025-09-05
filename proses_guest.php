<?php
session_start();

// Set session untuk pengguna tamu
$_SESSION['username'] = 'Tamu (Viewer)';
$_SESSION['role'] = 'viewer';
// Tidak ada user_id untuk tamu

// Redirect ke halaman utama
header("Location: admin.php");
exit();
