<?php
session_start();
include 'config/koneksi.php';

// Proteksi: Hanya Admin yang boleh mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Penempatan Siswa - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black">Penempatan Siswa (Plotting)</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Atur penempatan siswa ke dalam kelas berdasarkan Tahun Ajaran.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="GET" class="flex flex-wrap gap-4 mb-6 items-end border-b border-slate-100 pb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Tahun Ajaran</label>
                    <select name="tahun_ajaran" class="w-48 border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Tahun Ajaran --</option>
                        <!-- Nanti diisi dari tabel tahun_ajaran -->
                        <option value="1">2026/2027</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Kelas</label>
                    <select name="kelas" class="w-48 border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Kelas --</option>
                        <!-- Nanti diisi dari tabel kelas -->
                        <option value="1">X RPL 1</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm">Tampilkan Siswa</button>
            </form>

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-black">Daftar Siswa di Kelas Ini</h3>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-xs shadow-sm"><i class="fa-solid fa-plus mr-2"></i>Tambah Siswa ke Kelas</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-200">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-center">NO</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-center">NIS</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-left">NAMA SISWA</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 font-bold text-sm">Silakan pilih Tahun Ajaran dan Kelas terlebih dahulu.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>