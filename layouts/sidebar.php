<!-- layouts/sidebar.php -->
<?php
// Menentukan link dashboard berdasarkan role
$dashboard_link = 'dashboard_admin.php'; // Default
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Guru') {
        $dashboard_link = 'dashboard_guru.php';
    } elseif ($_SESSION['role'] === 'Wali Kelas') {
        $dashboard_link = 'dashboard_walikelas.php';
    }
}
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>
<aside class="w-64 bg-slate-100 flex flex-col hidden md:flex shrink-0 border-r border-slate-200">
    <div class="flex items-center justify-center h-32 mt-4">
        <i class="fa-solid fa-school text-6xl text-blue-600"></i>
    </div>
    <nav class="flex-1 px-4 space-y-2 mt-4 overflow-y-auto">
        <!-- Menu Umum untuk semua role -->
        <a href="<?php echo $dashboard_link; ?>" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo in_array($current_page, ['dashboard_admin.php', 'dashboard_guru.php', 'dashboard_walikelas.php']) ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
            <i class="fa-solid fa-border-all text-lg w-5 text-center"></i> Dashboard
        </a>

        <!-- Menu Khusus ADMIN -->
        <?php if ($role === 'Admin'): ?>
            <div class="pt-4 pb-1">
                <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Data Master</p>
            </div>
            <a href="kelola_siswa.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'kelola_siswa.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-user-graduate text-lg w-5 text-center"></i> Kelola Siswa
            </a>
            <a href="kelola_guru.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'kelola_guru.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-chalkboard-user text-lg w-5 text-center"></i> Kelola Guru
            </a>
            <a href="kelola_kelas.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'kelola_kelas.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-school text-lg w-5 text-center"></i> Kelola Kelas
            </a>
            <a href="kelola_tahun_ajaran.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'kelola_tahun_ajaran.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-calendar text-lg w-5 text-center"></i> Tahun Ajaran
            </a>
            
            <div class="pt-4 pb-1">
                <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Plotting / Penempatan</p>
            </div>
            <a href="penempatan_siswa.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'penempatan_siswa.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-users-rectangle text-lg w-5 text-center"></i> Penempatan Siswa
            </a>
            <a href="walikelas.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'walikelas.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-user-tie text-lg w-5 text-center"></i> Kelola Wali Kelas
            </a>
            
            <div class="pt-4 pb-1">
                <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Data Pelanggaran</p>
            </div>
            <a href="kelola_jenis_pelanggaran.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'kelola_jenis_pelanggaran.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-scale-balanced text-lg w-5 text-center"></i> Jenis Pelanggaran
            </a>
            <a href="kelola_kategori_pelanggaran.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'kelola_kategori_pelanggaran.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-list text-lg w-5 text-center"></i> Kategori Pelanggaran
            </a>
            <a href="cetak_export.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'cetak_export.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-file-export text-lg w-5 text-center"></i> Cetak / Export
            </a>
        <?php endif; ?>

        <!-- Menu Aktivitas -->
        <div class="pt-4 pb-1">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Aktivitas</p>
        </div>
        <a href="catatan_pelanggaran.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'catatan_pelanggaran.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
            <i class="fa-solid fa-clipboard-user text-lg w-5 text-center"></i> Catat Pelanggaran
        </a>
        <a href="laporan.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'laporan.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
            <i class="fa-solid fa-file-contract text-lg w-5 text-center"></i> Laporan
        </a>
        <a href="riwayat.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'riwayat.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
            <i class="fa-solid fa-clock-rotate-left text-lg w-5 text-center"></i> Riwayat
        </a>

        <!-- Menu Khusus WALI KELAS -->
        <?php if ($role === 'Wali Kelas'): ?>
            <div class="pt-4 pb-1">
                <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Khusus Wali Kelas</p>
            </div>
            <a href="rekap_point.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo $current_page == 'rekap_point.php' ? 'text-blue-600 bg-blue-50 rounded-lg' : ''; ?>">
                <i class="fa-solid fa-calculator text-lg w-5 text-center"></i> Rekap Poin
            </a>
        <?php endif; ?>

    </nav>
    <div class="p-4 mb-4 border-t border-slate-200">
        <a href="logout.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 rounded-lg transition">
            <i class="fa-solid fa-arrow-right-from-bracket text-lg w-5 text-center"></i> Logout
        </a>
    </div>
</aside>