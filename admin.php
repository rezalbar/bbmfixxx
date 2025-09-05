<?php
// Memulai session
session_start();

// Jika pengguna belum login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// --- KONEKSI DATABASE ---
require_once 'koneksi.php'; 

// --- INISIALISASI VARIABEL ---
$nama_user = $_SESSION['username'] ?? 'Admin Demo';
$currentPage = 'admin'; // Menandakan ini adalah halaman admin
$isAdminActive = true; // Halaman admin selalu aktif jika file ini yang diakses

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrasi - SI-BBM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #4a5568;
            border-radius: 20px;
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="flex h-screen">
        <!-- ========== Sidebar ========== -->
        <aside class="w-64 bg-blue-800 text-white flex flex-col sidebar-scroll overflow-y-auto">
            <div class="p-6 flex items-center gap-4 border-b border-blue-700">
                <img src="img/sier.jpeg" alt="Logo Perusahaan" class="h-10">
                <span class="text-xl font-bold">SI-BBM</span>
            </div>
            <nav class="flex-1 p-4">
                <ul>
                    <li class="mb-2">
                        <!-- Menu Administrasi disederhanakan, tanpa submenu -->
                        <a href="admin.php" class="flex items-center gap-3 <?php echo $isAdminActive ? 'bg-blue-900' : ''; ?> rounded-md p-3 text-sm font-semibold">
                            <i class="ph-fill ph-gear-six text-xl"></i>Administrasi
                        </a>
                    </li>
                    <li class="mb-2"><a href="kendaraan.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-truck text-xl"></i>Operasional Kendaraan</a></li>
                    <li class="mb-2"><a href="gudang.php" class="flex items-center gap-3 rounded-md p-3 text-sm font-semibold text-blue-200 hover:text-white"><i class="ph-fill ph-warehouse text-xl"></i>Operasional Gudang</a></li>
                </ul>
            </nav>
        </aside>

        <!-- ========== Konten Utama ========== -->
        <main class="flex-1 p-8 overflow-y-auto">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Administrasi BBM</h1>
                    <p class="text-gray-500">Dashboard Sistem Informasi BBM / Administrasi BBM</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="User Avatar" class="w-10 h-10 rounded-full">
                        <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($nama_user); ?></span>
                    </div>
                    <a href="logout.php" class="text-sm text-red-500 hover:underline">Logout</a>
                </div>
            </header>

            <!-- ========== Menu Pilihan Baru ========== -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Kartu Menu Anggaran -->
                <a href="anggaran.php" class="bg-white p-8 rounded-xl shadow-md hover:bg-blue-50 hover:shadow-lg transition-all flex flex-col items-center justify-center text-center">
                    <i class="ph-fill ph-wallet text-5xl text-blue-600 mb-4"></i>
                    <h2 class="font-bold text-lg text-gray-800">Manajemen Anggaran</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola data anggaran BBM Solar & Pertalite.</p>
                </a>

                <!-- Kartu Menu BBM -->
                <a href="bbm.php" class="bg-white p-8 rounded-xl shadow-md hover:bg-blue-50 hover:shadow-lg transition-all flex flex-col items-center justify-center text-center">
                    <i class="ph-fill ph-gas-pump text-5xl text-green-600 mb-4"></i>
                    <h2 class="font-bold text-lg text-gray-800">Manajemen BBM</h2>
                    <p class="text-sm text-gray-500 mt-1">Atur persediaan, permintaan, dan data BBM lainnya.</p>
                </a>

                <!-- Kartu Menu Kendaraan -->
                <a href="kendaraan1.php" class="bg-white p-8 rounded-xl shadow-md hover:bg-blue-50 hover:shadow-lg transition-all flex flex-col items-center justify-center text-center">
                    <i class="ph-fill ph-truck text-5xl text-yellow-600 mb-4"></i>
                    <h2 class="font-bold text-lg text-gray-800">Data Kendaraan</h2>
                    <p class="text-sm text-gray-500 mt-1">Lihat dan kelola data operasional kendaraan.</p>
                </a>
            </div>

            <footer class="text-center mt-12 py-4">
                <p class="text-xs text-gray-400">&copy; <?php echo date("Y"); ?> SI-BBM | Dibuat oleh @Magang UMSIDA</p>
            </footer>
        </main>
    </div>

</body>
</html>
<?php
// Menutup koneksi
if (isset($conn)) {
    mysqli_close($conn);
}
?>

