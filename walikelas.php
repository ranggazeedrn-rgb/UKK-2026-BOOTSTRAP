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
    <title>Kelola Wali Kelas - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-black">Kelola Wali Kelas</h1>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm">Tetapkan Wali Kelas</button>
        </div>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Wali Kelas Aktif</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-200">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-left">TAHUN AJARAN</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-center">KELAS</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-left">NAMA GURU (WALI KELAS)</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-center">STATUS</th>
                            <th class="border-b border-slate-200 px-4 py-3 text-xs font-bold text-slate-600 uppercase text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data Statis Placeholder untuk Desain -->
                        <tr class="hover:bg-slate-50 border-b border-slate-100">
                            <td class="px-4 py-3 text-sm">2026/2027</td>
                            <td class="px-4 py-3 text-sm font-bold text-black text-center">X RPL 1</td>
                            <td class="px-4 py-3 text-sm">Budi Santoso, S.Kom</td>
                            <td class="px-4 py-3 text-sm text-center"><span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Aktif</span></td>
                            <td class="px-4 py-3 text-sm text-center">
                                <button class="text-blue-600 font-bold mr-2">Edit</button>
                                <button class="text-red-600 font-bold">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>