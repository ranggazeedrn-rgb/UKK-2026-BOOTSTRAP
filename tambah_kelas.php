<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Ambil daftar Tahun Ajaran
$tahun_ajaran_list = false;
try { $tahun_ajaran_list = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC"); } catch (Exception $e) {}

// Cek kolom tahun_ajaran_id
$has_tahun_ajaran = false;
try {
    $cek_kolom = mysqli_query($koneksi, "SHOW COLUMNS FROM t_kelas LIKE 'tahun_ajaran_id'");
    if ($cek_kolom && mysqli_num_rows($cek_kolom) > 0) $has_tahun_ajaran = true;
} catch (Exception $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $tahun_ajaran_id = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran_id'] ?? '');
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    try {
        if ($has_tahun_ajaran && !empty($tahun_ajaran_id)) {
            $query = "INSERT INTO t_kelas (nama, tingkat, jurusan, tahun_ajaran_id, status_aktif, created_at, updated_at) 
                      VALUES ('$nama', '$tingkat', '$jurusan', '$tahun_ajaran_id', '$status_aktif', NOW(), NOW())";
        } else {
            $query = "INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif, created_at, updated_at) 
                      VALUES ('$nama', '$tingkat', '$jurusan', '$status_aktif', NOW(), NOW())";
        }
        mysqli_query($koneksi, $query);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Kelas berhasil ditambahkan!'];
        header("Location: kelola_kelas.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Kelas - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Tambah Kelas Baru</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Isi formulir di bawah untuk menambah data kelas.</p>
            </div>
            <a href="kelola_kelas.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-4xl mx-auto p-4 mb-6 rounded-xl text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA KELAS * (Contoh: X RPL 1)</label>
                    <input type="text" name="nama" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>
                
                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">TINGKAT *</label>
                        <select name="tingkat" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                            <option value="X">X (Sepuluh)</option>
                            <option value="XI">XI (Sebelas)</option>
                            <option value="XII">XII (Dua Belas)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">JURUSAN *</label>
                        <input type="text" name="jurusan" required placeholder="RPL, TKJ, dll" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm uppercase focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">TAHUN AJARAN</label>
                    <select name="tahun_ajaran_id" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php 
                        if ($tahun_ajaran_list && mysqli_num_rows($tahun_ajaran_list) > 0) {
                            mysqli_data_seek($tahun_ajaran_list, 0);
                            while ($ta = mysqli_fetch_assoc($tahun_ajaran_list)) {
                                $t_name = $ta['nama'] ?? 'TA ' . $ta['id'];
                                echo "<option value='{$ta['id']}'>{$t_name}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-4">
                    <input type="checkbox" name="status_aktif" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Kelas Aktif</label>
                </div>
                
                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Data Kelas
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
