<?php
include 'config/koneksi.php';
include 'cek_akses.php'; // Cek login & hak akses terpusat di sini

// Ambil statistik ringkas untuk Dashboard Guru
$q_siswa = @mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_siswa");
$total_siswa = ($q_siswa) ? mysqli_fetch_assoc($q_siswa)['total'] : 0;

$q_pelanggaran = @mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_pelanggaran_siswa");
$total_pelanggaran = ($q_pelanggaran) ? mysqli_fetch_assoc($q_pelanggaran)['total'] : 0;

$q_hari_ini = @mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_pelanggaran_siswa WHERE DATE(tanggal) = CURDATE()");
$pelanggaran_hari_ini = ($q_hari_ini) ? mysqli_fetch_assoc($q_hari_ini)['total'] : 0;

// Ambil 5 riwayat pelanggaran terbaru
$query_latest = "SELECT ps.*, 
                        s.nama AS nama_siswa, s.nis,
                        p.nama AS nama_pelanggaran, p.poin
                 FROM t_pelanggaran_siswa ps
                 LEFT JOIN t_siswa s ON ps.siswa_id = s.id
                 LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
                 ORDER BY ps.tanggal DESC, ps.id DESC LIMIT 5";
$latest_list = @mysqli_query($koneksi, $query_latest);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Guru - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-black">Dashboard Guru</h1>
            <p class="text-sm font-bold text-slate-500 mt-1">Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username'] ?? 'Guru'); ?>!</p>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">TOTAL SISWA</p>
                    <h3 class="text-3xl font-extrabold text-black mt-2"><?php echo $total_siswa; ?></h3>
                </div>
                <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">PELANGGARAN HARI INI</p>
                    <h3 class="text-3xl font-extrabold text-black mt-2"><?php echo $pelanggaran_hari_ini; ?></h3>
                </div>
                <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">TOTAL CATATAN PELANGGARAN</p>
                    <h3 class="text-3xl font-extrabold text-black mt-2"><?php echo $total_pelanggaran; ?></h3>
                </div>
                <div class="p-4 bg-red-50 text-red-600 rounded-2xl">
                    <i class="fa-solid fa-clipboard-list text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Actions & Recent List -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Action Card -->
            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-black mb-2">Pencatatan Pelanggaran</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6">
                        Temukan atau laporkan pelanggaran tata tertib siswa secara langsung ke dalam sistem kedisiplinan sekolah.
                    </p>
                </div>
                <a href="catatan_pelanggaran.php" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md transition text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Input Pelanggaran Baru
                </a>
            </div>

            <!-- Recent Violations Table -->
            <div class="lg:col-span-2 bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-black">Pelanggaran Terakhir</h3>
                    <a href="riwayat.php" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase">
                                <th class="pb-3">TANGGAL</th>
                                <th class="pb-3">SISWA</th>
                                <th class="pb-3">PELANGGARAN</th>
                                <th class="pb-3 text-center">POIN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if ($latest_list && mysqli_num_rows($latest_list) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($latest_list)): 
                                    $tgl = !empty($row['tanggal']) ? date('d/m/Y', strtotime($row['tanggal'])) : '-';
                                ?>
                                    <tr>
                                        <td class="py-3 font-bold text-slate-600"><?php echo htmlspecialchars($tgl); ?></td>
                                        <td class="py-3 font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa'] ?? 'Siswa'); ?></td>
                                        <td class="py-3 text-slate-700"><?php echo htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?></td>
                                        <td class="py-3 text-center font-bold text-red-600">+<?php echo $row['poin'] ?? 0; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400 font-bold">Belum ada catatan pelanggaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

</body>
</html>