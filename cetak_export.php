<?php
session_start();
include 'config/koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Cetak & Export - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <h1 class="text-2xl font-bold text-black mb-2">Cetak & Export Laporan</h1>
        <p class="text-sm font-bold text-slate-500 mb-8">Generate laporan kedisiplinan ke format dokumen atau spreadsheet.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card Export Pelanggaran -->
            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <i class="fa-solid fa-file-pdf text-2xl text-red-500"></i>
                    <h3 class="text-lg font-bold text-black">Rekap Pelanggaran Siswa</h3>
                </div>
                <form action="#" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Periode Tanggal</label>
                        <div class="flex gap-2">
                            <input type="date" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm">
                            <span class="text-slate-400 mt-2">-</span>
                            <input type="date" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Filter Kelas (Opsional)</label>
                        <select class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm">
                            <option value="all">-- Semua Kelas --</option>
                        </select>
                    </div>
                    <div class="flex gap-2 pt-4">
                        <button type="button" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold"><i class="fa-solid fa-print mr-2"></i>Cetak PDF</button>
                        <button type="button" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold"><i class="fa-solid fa-file-excel mr-2"></i>Export Excel</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>