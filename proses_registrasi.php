<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Validasi input
    if (empty($username) || empty($password) || empty($role)) {
        $_SESSION['register_error'] = "Semua kolom harus diisi.";
        header("Location: registrasi.php");
        exit();
    }

    // Cek apakah username sudah ada
    $check_query = "SELECT id FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['register_error'] = "Username sudah digunakan. Silakan pilih yang lain.";
        header("Location: registrasi.php");
        exit();
    }

    // Buat hash password yang aman
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Masukkan user baru ke database
    $insert_query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
    
    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['register_success'] = "Pengguna " . htmlspecialchars($username) . " berhasil didaftarkan!";
    } else {
        $_SESSION['register_error'] = "Gagal mendaftarkan pengguna: " . mysqli_error($conn);
    }

    header("Location: registrasi.php");
    exit();
}
