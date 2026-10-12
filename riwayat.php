<?php
// riwayat.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$filter_siswa = $_GET['siswa_id'] ?? '';
$where = "";
if (!empty($filter_siswa)) {
    $s_id = (int)$filter_siswa;
    $where = "WHERE ps.siswa_id = '$s_id'";
}

// Query disesuaikan dengan struktur tabel t_pelanggaran_siswa (menggunakan ps.nama_guru langsung)
$query = "SELECT ps.*, s.nis 
          FROM t_pelanggaran_siswa ps
          LEFT JOIN t_siswa s ON ps.siswa_id = s.id
          $where
          ORDER BY ps.tanggal DESC, ps.id DESC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pelanggaran Siswa</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-white text-slate-800 font-sans">
    <div class="flex min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-8 bg-white overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-black">Riwayat Pelanggaran</h1>
                <?php if (!empty($filter_siswa)): ?>
                    <a href="riwayat.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm">Tampilkan Semua</a>
                <?php endif; ?>
            </div>

            <div class="bg-slate-100 p-6 rounded-[1.5rem]">
                <h3 class="text-sm font-bold text-black mb-4">Daftar Riwayat</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-slate-300 bg-slate-100">
                        <thead>
                            <tr>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">TANGGAL</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">SISWA & KELAS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">PELANGGARAN</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">POIN</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">GURU</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">TINDAKAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center font-bold"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm">
                                            <div class="font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa']); ?></div>
                                            <div class="text-xs text-slate-500">Kelas: <?php echo htmlspecialchars($row['nama_kelas']); ?></div>
                                        </td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm font-bold"><?php echo htmlspecialchars($row['nama_pelanggaran']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center font-bold text-red-600">+<?php echo $row['poin']; ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm"><?php echo htmlspecialchars($row['nama_guru']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm">
                                            <div class="font-bold"><?php echo htmlspecialchars($row['tindakan']); ?></div>
                                            <div class="text-xs text-slate-500"><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>