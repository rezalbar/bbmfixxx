<?php

$hostname = "localhost";
$username = "root";
$password = "";          // Untuk XAMPP default, password-nya kosong
$database = "bbmfix";      // Pastikan nama database ini sama persis

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

?>