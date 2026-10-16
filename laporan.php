<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$tahun_ajaran_id = isset($_GET['tahun_ajaran_id']) ? mysqli_real_escape_string($koneksi, $_GET['tahun_ajaran_id']) : '';
$kelas_id = isset($_GET['kelas_id']) ? mysqli_real_escape_string($koneksi, $_GET['kelas_id']) : '';
$export = $_GET['export'] ?? 'pdf';

$where = [];
if (!empty($tahun_ajaran_id)) {
    $where[] = "ks.tahun_ajaran_id = '$tahun_ajaran_id'";
}
if (!empty($kelas_id)) {
    $where[] = "ks.kelas_id = '$kelas_id'";
}

$where_sql = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

$query = "SELECT ps.*, s.nis, s.nama AS nama_siswa, k.nama AS nama_kelas, 
                 p.nama AS nama_pelanggaran, p.poin
          FROM t_pelanggaran_siswa ps
          LEFT JOIN t_siswa s ON ps.siswa_id = s.id
          LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
          LEFT JOIN t_kelas k ON ks.kelas_id = k.id
          LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          $where_sql
          ORDER BY ps.id DESC";

$result = @mysqli_query($koneksi, $query);

if ($export === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Pelanggaran_Siswa_" . date('Y-m-d') . ".xls");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pelanggaran Siswa</title>
    <?php if ($export !== 'excel'): ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <?php endif; ?>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-8">

    <?php if ($export !== 'excel'): ?>
        <div class="max-w-5xl mx-auto mb-6 flex justify-between items-center no-print">
            <a href="cetak_export.php" class="text-sm font-bold text-slate-600 hover:text-black">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Cetak & Export
            </a>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-lg text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>
    <?php endif; ?>

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-center border-b border-slate-300 pb-4 mb-6">
            <h1 class="text-2xl font-bold text-black uppercase tracking-wide">Laporan Kedisiplinan & Pelanggaran Siswa</h1>
            <p class="text-sm text-slate-500 font-bold mt-1">Sistem Informasi Kedisiplinan Sekolah UKK 2026</p>
        </div>

        <table class="w-full border-collapse border border-slate-300 text-sm">
            <thead>
                <tr class="bg-slate-100 text-slate-800">
                    <th class="border border-slate-300 px-3 py-2 text-center w-12">NO</th>
                    <th class="border border-slate-300 px-3 py-2 text-center w-28">NIS</th>
                    <th class="border border-slate-300 px-3 py-2 text-left">NAMA SISWA</th>
                    <th class="border border-slate-300 px-3 py-2 text-center w-28">KELAS</th>
                    <th class="border border-slate-300 px-3 py-2 text-left">PELANGGARAN</th>
                    <th class="border border-slate-300 px-3 py-2 text-center w-20">POIN</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-mono font-bold text-blue-600"><?php echo htmlspecialchars($row['nis'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold"><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-slate-700"><?php echo htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-red-600">+<?php echo $row['poin'] ?? 0; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Tidak ada data pelanggaran ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-10 flex justify-end">
            <div class="text-center w-52 text-sm">
                <p class="text-slate-600">Tanggal Cetak:</p>
                <p class="font-bold text-black mb-12"><?php echo date('d F Y'); ?></p>
                <p class="font-bold text-black border-b border-black inline-block px-4">Petugas Kesiswaan</p>
            </div>
        </div>
    </div>

    <?php if ($export === 'pdf'): ?>
    <script>
        // Memunculkan dialog cetak secara otomatis saat halaman selesai dimuat
        window.onload = function() {
            window.print();
        };
    </script>
    <?php endif; ?>

</body>
</html>