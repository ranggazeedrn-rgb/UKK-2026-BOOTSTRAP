<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$kelas_id = isset($_GET['kelas_id']) ? mysqli_real_escape_string($koneksi, $_GET['kelas_id']) : '';

$where = [];
if (!empty($kelas_id)) {
    $where[] = "ks.kelas_id = '$kelas_id'";
}

$where_sql = count($where) > 0 ? "WHERE " . implode(' AND ', $where) : "";

$query = "SELECT s.id, s.nis, s.nama AS nama_siswa, k.nama AS nama_kelas,
                 COALESCE(SUM(p.poin), 0) AS total_poin
          FROM t_siswa s
          JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
          JOIN t_kelas k ON ks.kelas_id = k.id
          LEFT JOIN t_pelanggaran_siswa ps ON s.id = ps.siswa_id
          LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          $where_sql
          GROUP BY s.id, s.nis, s.nama, k.nama
          ORDER BY total_poin DESC, s.nama ASC";

$result = @mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Akumulasi Poin Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-8">

    <div class="max-w-5xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="cetak_export.php" class="text-sm font-bold text-slate-600 hover:text-black">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Cetak & Export
        </a>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-lg text-sm shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Rekap Poin
        </button>
    </div>

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-center border-b border-slate-300 pb-4 mb-6">
            <h1 class="text-2xl font-bold text-black uppercase tracking-wide">Rekapitulasi Akumulasi Poin Kedisiplinan</h1>
            <p class="text-sm text-slate-500 font-bold mt-1">Laporan Ringkasan Poin Siswa per Kelas</p>
        </div>

        <table class="w-full border-collapse border border-slate-300 text-sm">
            <thead>
                <tr class="bg-slate-100 text-slate-800">
                    <th class="border border-slate-300 px-4 py-3 text-center w-12">NO</th>
                    <th class="border border-slate-300 px-4 py-3 text-center w-32">NIS</th>
                    <th class="border border-slate-300 px-4 py-3 text-left">NAMA SISWA</th>
                    <th class="border border-slate-300 px-4 py-3 text-center w-32">KELAS</th>
                    <th class="border border-slate-300 px-4 py-3 text-center w-36">TOTAL POIN</th>
                    <th class="border border-slate-300 px-4 py-3 text-center w-40">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): 
                        $poin = (int)$row['total_poin'];
                        $status_label = 'Aman';
                        $status_class = 'text-green-600 bg-green-50';
                        if ($poin >= 50) {
                            $status_label = 'Panggilan Ortu';
                            $status_class = 'text-red-600 bg-red-50 font-bold';
                        } elseif ($poin >= 20) {
                            $status_label = 'Peringatan';
                            $status_class = 'text-amber-600 bg-amber-50 font-bold';
                        }
                    ?>
                        <tr class="hover:bg-slate-50">
                            <td class="border border-slate-300 px-4 py-2.5 text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                            <td class="border border-slate-300 px-4 py-2.5 text-center font-mono font-bold text-blue-600"><?php echo htmlspecialchars($row['nis']); ?></td>
                            <td class="border border-slate-300 px-4 py-2.5 font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                            <td class="border border-slate-300 px-4 py-2.5 text-center font-bold"><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                            <td class="border border-slate-300 px-4 py-2.5 text-center font-bold text-base <?php echo $poin > 0 ? 'text-red-600' : 'text-slate-600'; ?>"><?php echo $poin; ?> Poin</td>
                            <td class="border border-slate-300 px-4 py-2.5 text-center"><span class="px-2 py-1 rounded text-xs <?php echo $status_class; ?>"><?php echo $status_label; ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Tidak ada data siswa ditemukan untuk rekapitulasi poin.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-10 flex justify-between items-end text-sm">
            <div>
                <p class="text-xs text-slate-500 font-bold">Keterangan Akumulasi Poin:</p>
                <p class="text-xs text-slate-600 mt-1">• 0 - 19 Poin: Kategori Pembinaan Normal</p>
                <p class="text-xs text-slate-600">• 20 - 49 Poin: Peringatan Pertama / Wali Kelas</p>
                <p class="text-xs text-slate-600">• ≥ 50 Poin: Surat Panggilan Orang Tua / BK</p>
            </div>
            <div class="text-center w-52">
                <p class="text-slate-600">Tanggal Cetak:</p>
                <p class="font-bold text-black mb-12"><?php echo date('d F Y'); ?></p>
                <p class="font-bold text-black border-b border-black inline-block px-6">Wali Kelas / Guru BK</p>
            </div>
        </div>
    </div>

    <script>
        // Memunculkan dialog cetak secara otomatis
        window.onload = function() {
            window.print();
        };
    </script>

</body>
</html>