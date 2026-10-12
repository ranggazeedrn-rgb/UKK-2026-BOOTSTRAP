<?php
// catatan_pelanggaran.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan Transaksi Pelanggaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siswa_id = (int)$_POST['siswa_id'];
    $pelanggaran_id = (int)$_POST['pelanggaran_id'];
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $tindakan = mysqli_real_escape_string($koneksi, $_POST['tindakan'] ?? 'Diberi peringatan');
    $status = 'Diproses';

    // 1. Ambil detail data Siswa & Kelasnya
    $q_siswa = mysqli_query($koneksi, "SELECT s.nama as nama_siswa, k.id as kelas_id, k.nama as nama_kelas 
                                       FROM t_siswa s 
                                       LEFT JOIN t_kelas_siswa ks ON s.id = ks.siswa_id 
                                       LEFT JOIN t_kelas k ON ks.kelas_id = k.id 
                                       WHERE s.id = '$siswa_id' LIMIT 1");
    $d_siswa = mysqli_fetch_assoc($q_siswa);
    $nama_siswa = mysqli_real_escape_string($koneksi, $d_siswa['nama_siswa'] ?? 'Siswa');
    $kelas_id = $d_siswa['kelas_id'] ? "'".$d_siswa['kelas_id']."'" : "NULL";
    $nama_kelas = mysqli_real_escape_string($koneksi, $d_siswa['nama_kelas'] ?? '-');

    // 2. Ambil detail Pelanggaran & Kategori
    $q_pelanggaran = mysqli_query($koneksi, "SELECT nama, poin, pelanggaran_kategori_id FROM t_pelanggaran WHERE id = '$pelanggaran_id'");
    $d_pelanggaran = mysqli_fetch_assoc($q_pelanggaran);
    $nama_pelanggaran = mysqli_real_escape_string($koneksi, $d_pelanggaran['nama'] ?? 'Pelanggaran');
    $poin = (int)($d_pelanggaran['poin'] ?? 0);
    $pelanggaran_kategori_id = $d_pelanggaran['pelanggaran_kategori_id'] ? "'".$d_pelanggaran['pelanggaran_kategori_id']."'" : "NULL";

    // 3. Ambil Tahun Ajaran Aktif
    $q_ta = mysqli_query($koneksi, "SELECT id FROM t_tahun_ajaran WHERE status_aktif = 1 LIMIT 1");
    $d_ta = mysqli_fetch_assoc($q_ta);
    $tahun_ajaran_id = $d_ta['id'] ? "'".$d_ta['id']."'" : "NULL";

    // 4. Ambil Guru Pelapor dari session user
    $user_id = $_SESSION['user_id'];
    $q_guru = mysqli_query($koneksi, "SELECT id, nama FROM t_guru WHERE user_id = '$user_id' LIMIT 1");
    if (mysqli_num_rows($q_guru) > 0) {
        $d_guru = mysqli_fetch_assoc($q_guru);
        $guru_id = "'".$d_guru['id']."'";
        $nama_guru = mysqli_real_escape_string($koneksi, $d_guru['nama']);
    } else {
        $guru_id = "NULL";
        $nama_guru = mysqli_real_escape_string($koneksi, $_SESSION['name'] ?? 'Administrator');
    }

    // Insert Sesuai Kolom Tabel t_pelanggaran_siswa
    $query = "INSERT INTO t_pelanggaran_siswa 
              (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, poin, tindakan, status, created_at, updated_at) 
              VALUES 
              ($tahun_ajaran_id, '$siswa_id', '$nama_siswa', $kelas_id, '$nama_kelas', '$pelanggaran_id', '$nama_pelanggaran', $pelanggaran_kategori_id, $guru_id, '$nama_guru', '$tanggal', '$keterangan', '$poin', '$tindakan', '$status', NOW(), NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Catatan pelanggaran siswa berhasil disimpan!'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)];
    }
    header("Location: catatan_pelanggaran.php");
    exit();
}

// Ambil Data Master Dropdown Form
$siswa_list = mysqli_query($koneksi, "SELECT id, nis, nama FROM t_siswa WHERE status_aktif = 1 ORDER BY nama ASC");
$pelanggaran_list = mysqli_query($koneksi, "SELECT p.id, p.kode, p.nama, p.poin, k.nama as kategori 
                                            FROM t_pelanggaran p 
                                            LEFT JOIN t_pelanggaran_kategori k ON p.pelanggaran_kategori_id = k.id 
                                            WHERE p.status_aktif = 1 ORDER BY k.nama ASC, p.kode ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Pelanggaran</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-white text-slate-800 font-sans">
    <div class="flex min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-8 bg-white overflow-y-auto">
            <div class="max-w-2xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-black">Input Pelanggaran</h1>
                    <a href="riwayat.php" class="text-sm font-bold text-blue-600 hover:underline">Lihat Riwayat</a>
                </div>

                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                        <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-slate-100 p-8 rounded-[1.5rem]">
                    <form method="POST" action="catatan_pelanggaran.php" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Tanggal Kejadian</label>
                            <input type="date" name="tanggal" required value="<?php echo date('Y-m-d'); ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-600 bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Pilih Siswa</label>
                            <select name="siswa_id" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-600 bg-white">
                                <option value="">-- Pilih Siswa --</option>
                                <?php while ($s = mysqli_fetch_assoc($siswa_list)): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Jenis Pelanggaran</label>
                            <select name="pelanggaran_id" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-600 bg-white">
                                <option value="">-- Pilih Jenis Pelanggaran --</option>
                                <?php 
                                $current_kategori = '';
                                while ($p = mysqli_fetch_assoc($pelanggaran_list)): 
                                    if ($current_kategori != $p['kategori']) {
                                        if ($current_kategori != '') echo '</optgroup>';
                                        $current_kategori = $p['kategori'];
                                        echo '<optgroup label="' . htmlspecialchars($current_kategori) . '">';
                                    }
                                ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nama']); ?> (+<?php echo $p['poin']; ?> Poin)</option>
                                <?php endwhile; echo '</optgroup>'; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Deskripsi / Keterangan</label>
                            <textarea name="catatan" rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-600 bg-white"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Tindakan</label>
                            <input type="text" name="tindakan" value="Diberi teguran & pembinaan" class="w-full border border-slate-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-600 bg-white">
                        </div>

                        <button type="submit" class="w-full py-3 mt-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm transition">
                            SIMPAN PELANGGARAN
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>