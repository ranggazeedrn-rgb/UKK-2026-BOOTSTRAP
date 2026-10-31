<?php
// tambah_tahun_ajaran.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $tanggal_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($koneksi, $_POST['tanggal_selesai']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($status_aktif == 1) {
        @mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET status_aktif = 0");
    }

    $query = "INSERT INTO t_tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif, created_at, updated_at) 
              VALUES ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif', NOW(), NOW())";
              
    if(mysqli_query($koneksi, $query)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Tahun Ajaran berhasil ditambahkan!'];
        header("Location: kelola_tahun_ajaran.php");
        exit();
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Tahun Ajaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-3xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Tambah Tahun Ajaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Tambahkan periode tahun ajaran baru ke dalam sistem.</p>
            </div>
            <a href="kelola_tahun_ajaran.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-3xl mx-auto p-4 mb-6 rounded-xl text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="max-w-3xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-2">TAHUN AJARAN * (Contoh: 2025/2026 Ganjil)</label>
                    <input type="text" name="nama" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL MULAI *</label>
                        <input type="date" name="tanggal_mulai" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL SELESAI *</label>
                        <input type="date" name="tanggal_selesai" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-4">
                    <input type="checkbox" name="status_aktif" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Sebagai Tahun Ajaran Aktif</label>
                </div>
                
                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Tahun Ajaran
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
