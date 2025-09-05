<?php
// Ganti password ini sesuai keinginan Anda
$password_admin = 'admin123';
$password_viewer = 'viewer123';

// Membuat hash yang aman
$hash_admin = password_hash($password_admin, PASSWORD_DEFAULT);
$hash_viewer = password_hash($password_viewer, PASSWORD_DEFAULT);

echo "Hash untuk Admin (yogi): <br>";
echo $hash_admin;
echo "<br><br>";
echo "Hash untuk Viewer (budi): <br>";
echo $hash_viewer;
?>