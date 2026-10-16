<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Kueri presisi mengambil riwayat pelanggaran, siswa, kelas, dan poin pelanggaran
$query = "SELECT ps.*, 
                 s.nama AS nama_siswa, s.nis,
                 p.nama AS nama_pelanggaran, p.poin,
                 (SELECT k.nama 
                  FROM t_kelas_siswa ks 
                  JOIN t_kelas k ON ks.kelas_id = k.id 
                  WHERE ks.siswa_id = ps.siswa_id 
                  ORDER BY ks.id DESC LIMIT 1) AS nama_kelas
          FROM t_pelanggaran_siswa ps
          LEFT JOIN t_siswa s ON ps.siswa_id = s.id
          LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          ORDER BY ps.tanggal DESC, ps.id DESC";

$result = @mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black">Riwayat Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Daftar rekam jejak pelanggaran tata tertib siswa.</p>
            </div>
            <a href="catatan_pelanggaran.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Input Pelanggaran
            </a>
        </div>

        <!-- Table Card -->
        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">TANGGAL</th>
                            <th class="py-3 px-4">SISWA & KELAS</th>
                            <th class="py-3 px-4">PELANGGARAN</th>
                            <th class="py-3 px-4 text-center">POIN</th>
                            <th class="py-3 px-4">PETUGAS</th>
                            <th class="py-3 px-4">TINDAKAN & DESKRIPSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): 
                                $nama_siswa = !empty($row['nama_siswa']) ? $row['nama_siswa'] : 'Siswa Tidak Ditemukan';
                                $nama_kelas = !empty($row['nama_kelas']) ? $row['nama_kelas'] : '-';
                                $nama_pelanggaran = !empty($row['nama_pelanggaran']) ? $row['nama_pelanggaran'] : 'Jenis Pelanggaran Tidak Ada';
                                $poin = $row['poin'] ?? 0;
                                $tanggal = !empty($row['tanggal']) ? date('d/m/Y', strtotime($row['tanggal'])) : '-';
                            ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-4 px-4 font-bold text-slate-700 whitespace-nowrap">
                                        <?php echo htmlspecialchars($tanggal); ?>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($nama_siswa); ?></div>
                                        <div class="text-xs text-slate-400 font-bold">Kelas: <?php echo htmlspecialchars($nama_kelas); ?></div>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-800">
                                        <?php echo htmlspecialchars($nama_pelanggaran); ?>
                                    </td>
                                    <td class="py-4 px-4 text-center font-bold text-red-600">
                                        +<?php echo $poin; ?>
                                    </td>
                                    <td class="py-4 px-4 text-slate-600 font-medium">
                                        Administrator
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($row['tindakan'] ?? '-'); ?></div>
                                        <div class="text-xs text-slate-400 font-medium italic mt-0.5"><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500 font-bold">Belum ada riwayat pelanggaran yang dicatat.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>