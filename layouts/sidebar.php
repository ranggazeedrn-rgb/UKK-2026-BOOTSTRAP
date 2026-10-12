<!-- layouts/sidebar.php -->
<aside class="w-64 bg-slate-100 flex flex-col hidden md:flex shrink-0 border-r border-slate-200">
    <div class="flex items-center justify-center h-32 mt-4">
        <i class="fa-brands fa-ethereum text-6xl text-blue-600"></i>
    </div>
    <nav class="flex-1 px-4 space-y-2 mt-4">
        <a href="dashboard_admin.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_admin.php' ? 'text-blue-600' : ''; ?>">
            <i class="fa-solid fa-border-all text-lg w-5 text-center"></i> Dashboard
        </a>
        <a href="kelola_siswa.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'kelola_siswa.php' ? 'text-blue-600' : ''; ?>">
            <i class="fa-solid fa-user text-lg w-5 text-center"></i> Siswa
        </a>
        <a href="catatan_pelanggaran.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'catatan_pelanggaran.php' ? 'text-blue-600' : ''; ?>">
            <i class="fa-regular fa-clipboard text-lg w-5 text-center"></i> Catatan
        </a>
        <a href="kelola_jenis_pelanggaran.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'kelola_jenis_pelanggaran.php' ? 'text-blue-600' : ''; ?>">
            <i class="fa-solid fa-coins text-lg w-5 text-center"></i> Poin
        </a>
        <a href="laporan.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'text-blue-600' : ''; ?>">
            <i class="fa-solid fa-circle-info text-lg w-5 text-center"></i> Laporan
        </a>
         <a href="kelola_jenis_pelanggaran.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'kelola_jenis_pelanggaran.php' ? 'text-blue-600' : ''; ?>">
            <i class="fa-solid fa-circle-info text-lg w-5 text-center"></i> catatan
        </a>
        <a href="kelola_guru.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-slate-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'kelola_jenis_pelanggaran.php' ? 'text-blue-600' : ''; ?>">
            <i class="fa-solid fa-circle-info text-lg w-5 text-center"></i> Data Guru
    </nav>
    <div class="p-4 mb-8">
        <a href="logout.php" class="flex items-center gap-4 px-4 py-3 text-sm font-bold text-red-600 transition">
            <i class="fa-solid fa-arrow-right-from-bracket text-lg w-5 text-center"></i> Logout
        </a>
    </div>
</aside>