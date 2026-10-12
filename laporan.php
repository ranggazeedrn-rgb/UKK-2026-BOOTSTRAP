<?php
// laporan.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');

$query = "SELECT ps.*, s.nis, s.nama as nama_siswa, p.nama as nama_pelanggaran 
          FROM t_pelanggaran_siswa ps
          JOIN t_siswa s ON ps.siswa_id = s.id
          JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          WHERE ps.tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'
          ORDER BY ps.tanggal ASC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; background: white !important; }
            main { padding: 0 !important; }
            .bg-slate-100 { background: white !important; padding: 0 !important; }
            table { border-collapse: collapse !important; width: 100% !important; }
            th, td { border: 1px solid black !important; padding: 8px !important; }
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans">
    <div class="flex min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-8 bg-white overflow-y-auto">
            <div class="no-print flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-black">Cetak Laporan</h1>
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm">Print Laporan</button>
            </div>

            <form method="GET" class="no-print mb-6 flex gap-2 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" value="<?php echo $tgl_mulai; ?>" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" value="<?php echo $tgl_selesai; ?>" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-blue-600">
                </div>
                <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold h-10">Filter</button>
            </form>

            <div class="bg-slate-100 p-8 rounded-[1.5rem]">
                <div class="text-center mb-6 pb-4 border-b border-slate-300">
                    <h2 class="text-xl font-bold uppercase tracking-wider text-black">Laporan Rekapitulasi Pelanggaran Siswa</h2>
                    <p class="text-sm text-slate-600 mt-1">Periode: <?php echo date('d/m/Y', strtotime($tgl_mulai)); ?> s/d <?php echo date('d/m/Y', strtotime($tgl_selesai)); ?></p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-slate-300 bg-white">
                        <thead>
                            <tr>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-12">NO</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">TANGGAL</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">NIS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA SISWA</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">PELANGGARAN</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">POIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            $total_poin = 0;
                            if (mysqli_num_rows($result) > 0): 
                                while ($row = mysqli_fetch_assoc($result)): 
                                    $total_poin += $row['poin'];
                            ?>
                                <tr>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo $no++; ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center font-bold"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo htmlspecialchars($row['nis']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm"><?php echo htmlspecialchars($row['nama_pelanggaran']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-red-600 text-center">+<?php echo $row['poin']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                                <tr>
                                    <td colspan="5" class="border border-slate-300 px-4 py-3 text-sm font-bold text-right text-black">TOTAL POIN:</td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-red-600 text-center"><?php echo $total_poin; ?></td>
                                </tr>
                            <?php else: ?>
                                <tr><td colspan="6" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Tidak ada data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>