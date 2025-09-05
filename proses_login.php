<?php
// Memulai session untuk menyimpan status login pengguna
session_start();

// Menyertakan file koneksi ke database
include 'koneksi.php';

// Memeriksa apakah form login telah dikirim menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Mengamankan input username dari form untuk mencegah SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    // Mengambil password dari form (tidak perlu diamankan karena tidak langsung dimasukkan ke query)
    $password = $_POST['password'];

    // Membuat query untuk mencari user berdasarkan username di tabel 'users'
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah query berhasil dan menemukan satu baris data
    if ($result && mysqli_num_rows($result) == 1) {
        // Mengambil data user sebagai array asosiatif
        $user = mysqli_fetch_assoc($result);
        
        // Memverifikasi password yang diinput dengan hash password di database
        if (password_verify($password, $user['password'])) {
            // Jika password cocok, simpan informasi user ke dalam session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Arahkan pengguna ke halaman dashboard utama
            header("Location: admin.php");
            exit();
        } else {
            // Jika password salah, buat pesan error dan arahkan kembali ke halaman login
            $_SESSION['login_error'] = "Username atau password salah.";
            header("Location: login.php");
            exit();
        }
    } else {
        // Jika username tidak ditemukan, buat pesan error dan arahkan kembali ke halaman login
        $_SESSION['login_error'] = "Username atau password salah.";
        header("Location: login.php");
        exit();
    }
} else {
    // Jika halaman diakses tanpa mengirim form, arahkan kembali ke halaman login
    header("Location: login.php");
    exit();
}
?>
