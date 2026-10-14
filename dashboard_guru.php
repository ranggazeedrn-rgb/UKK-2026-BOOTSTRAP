<?php
session_start();
include 'config/koneksi.php';

// Cek sesi dan role
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Guru') {
    header("Location: login.php");
    exit;
}

// Ambil total pelanggaran
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa");
$total_pelanggaran = $query_total ? mysqli_fetch_assoc($query_total)['total'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Guru - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <!-- Sidebar -->
    <?php include 'layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Dashboard Guru</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">
                    Selamat datang, <span class="text-blue-600"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                </p>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm flex items-center gap-3">
                <i class="fa-solid fa-calendar-day text-blue-600"></i>
                <span class="text-sm font-bold text-slate-700"><?php echo date('d M Y'); ?></span>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="bg-red-100 p-4 rounded-xl flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-600 w-6 text-center"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Pelanggaran</p>
                    <h3 class="text-3xl font-bold text-black"><?php echo $total_pelanggaran; ?></h3>
                </div>
            </div>
        </div>

        <!-- Menu Akses Cepat (Fitur Guru) -->
        <div class="bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-black mb-6 border-b border-slate-100 pb-4">Menu Guru</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <a href="catatan_pelanggaran.php" class="flex flex-col items-center justify-center p-6 bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-xl transition group">
                    <i class="fa-solid fa-clipboard-user text-3xl text-slate-400 group-hover:text-blue-600 mb-3 transition"></i>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-blue-700">Catat Pelanggaran</span>
                </a>
                
                <a href="tindakan.php" class="flex flex-col items-center justify-center p-6 bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-200 rounded-xl transition group">
                    <i class="fa-solid fa-gavel text-3xl text-slate-400 group-hover:text-amber-600 mb-3 transition"></i>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-amber-700">Tindakan</span>
                </a>
                
                <a href="laporan.php" class="flex flex-col items-center justify-center p-6 bg-slate-50 hover:bg-green-50 border border-slate-200 hover:border-green-200 rounded-xl transition group">
                    <i class="fa-solid fa-file-contract text-3xl text-slate-400 group-hover:text-green-600 mb-3 transition"></i>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-green-700">Laporan</span>
                </a>
                
                <a href="riwayat.php" class="flex flex-col items-center justify-center p-6 bg-slate-50 hover:bg-purple-50 border border-slate-200 hover:border-purple-200 rounded-xl transition group">
                    <i class="fa-solid fa-clock-rotate-left text-3xl text-slate-400 group-hover:text-purple-600 mb-3 transition"></i>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-purple-700">Riwayat</span>
                </a>

            </div>
        </div>
    </main>
</div>
</body>
</html>