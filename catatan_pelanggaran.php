<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan Data Pelanggaran Siswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_pelanggaran'])) {
    $siswa_id       = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);
    $pelanggaran_id = mysqli_real_escape_string($koneksi, $_POST['pelanggaran_id']);
    $tanggal        = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keterangan     = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $tindakan       = mysqli_real_escape_string($koneksi, $_POST['tindakan']);

    if (!empty($siswa_id) && !empty($pelanggaran_id) && !empty($tanggal)) {
        $query = "INSERT INTO t_pelanggaran_siswa (siswa_id, pelanggaran_id, tanggal, keterangan, tindakan, created_at, updated_at) 
                  VALUES ('$siswa_id', '$pelanggaran_id', '$tanggal', '$keterangan', '$tindakan', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data pelanggaran berhasil dicatat!'];
            header("Location: catatan_pelanggaran.php");
            exit();
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Harap isi semua kolom wajib (*)'];
    }
}

// Ambil filter kelas dari URL
$selected_kelas = $_GET['kelas_id'] ?? '';

// Fetch Data Dropdown Kelas & Jenis Pelanggaran
$kelas_list = @mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY nama ASC");
$pelanggaran_list = @mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif = 1 ORDER BY kode ASC");

// Kueri Pengambilan Data Siswa Presisi
if (!empty($selected_kelas)) {
    // Memakai kelas_id & siswa_id sesuai struktur schema t_kelas_siswa
    $query_siswa = "SELECT s.*, k.nama AS nama_kelas 
                    FROM t_siswa s
                    INNER JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
                    INNER JOIN t_kelas k ON ks.kelas_id = k.id
                    WHERE ks.kelas_id = '$selected_kelas'
                    ORDER BY s.nama ASC";
} else {
    // Menampilkan seluruh siswa jika filter kelas tidak dipilih
    $query_siswa = "SELECT s.*, 
                    (SELECT k2.nama FROM t_kelas_siswa ks2 JOIN t_kelas k2 ON ks2.kelas_id = k2.id WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1) AS nama_kelas
                    FROM t_siswa s 
                    ORDER BY s.nama ASC";
}

$siswa_list = @mysqli_query($koneksi, $query_siswa);

// Fallback jika tidak ada data dari kueri di atas
if (!$siswa_list || mysqli_num_rows($siswa_list) == 0) {
    $siswa_list = @mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY nama ASC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 max-w-2xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Input Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Catat pelanggaran tata tertib yang dilakukan siswa.</p>
            </div>
            <a href="riwayat.php" class="text-blue-600 font-bold text-sm hover:underline flex items-center gap-1 mt-2 md:mt-0">
                <i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat
            </a>
        </div>

        <!-- Flash Alert -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-2xl mx-auto p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="catatan_pelanggaran.php">
                <input type="hidden" name="simpan_pelanggaran" value="1">
                
                <!-- Tanggal Kejadian -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Kejadian *</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

                <!-- Filter Pilih Kelas -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Kelas (Filter Siswa)</label>
                    <select onchange="location.href='catatan_pelanggaran.php?kelas_id=' + this.value" class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Semua Kelas --</option>
                        <?php if ($kelas_list): while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['nama']); ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <!-- Pilih Siswa -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Siswa *</label>
                    <select name="siswa_id" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Siswa --</option>
                        <?php if ($siswa_list && mysqli_num_rows($siswa_list) > 0): ?>
                            <?php while ($s = mysqli_fetch_assoc($siswa_list)): 
                                $nis = $s['nis'] ?? $s['nisn'] ?? '-';
                                $nama = $s['nama'] ?? 'Siswa Tanpa Nama';
                                $kelas_info = !empty($s['nama_kelas']) ? " ({$s['nama_kelas']})" : "";
                            ?>
                                <option value="<?php echo $s['id']; ?>">
                                    <?php echo htmlspecialchars($nis); ?> - <?php echo htmlspecialchars($nama) . htmlspecialchars($kelas_info); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>Belum ada siswa yang ditempatkan di kelas ini</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Jenis Pelanggaran -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Pelanggaran *</label>
                    <select name="pelanggaran_id" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Jenis Pelanggaran --</option>
                        <?php if ($pelanggaran_list): while ($p = mysqli_fetch_assoc($pelanggaran_list)): ?>
                            <option value="<?php echo $p['id']; ?>">
                                [<?php echo htmlspecialchars($p['kode']); ?>] <?php echo htmlspecialchars($p['nama']); ?> (+<?php echo $p['poin']; ?> Poin)
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <!-- Deskripsi / Keterangan -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Deskripsi / Keterangan Kejadian</label>
                    <textarea name="keterangan" rows="3" placeholder="Rincian kronologi atau detail pelanggaran..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"></textarea>
                </div>

                <!-- Tindakan -->
                <div class="mb-8">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tindakan Pembinaan</label>
                    <input type="text" name="tindakan" value="Diberi teguran & pembinaan" placeholder="Tindakan yang diberikan..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition">
                    SIMPAN PELANGGARAN
                </button>
            </form>
        </div>

    </main>

</body>
</html>