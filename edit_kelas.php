<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: kelola_kelas.php"); exit(); }

$query_data = mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE id = '$id'");
$data = mysqli_fetch_assoc($query_data);
if (!$data) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak ditemukan!'];
    header("Location: kelola_kelas.php"); exit();
}

$tahun_ajaran_list = false;
try { $tahun_ajaran_list = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC"); } catch (Exception $e) {}

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
            $query = "UPDATE t_kelas SET nama='$nama', tingkat='$tingkat', jurusan='$jurusan', tahun_ajaran_id='$tahun_ajaran_id', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        } else {
            $query = "UPDATE t_kelas SET nama='$nama', tingkat='$tingkat', jurusan='$jurusan', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        }
        mysqli_query($koneksi, $query);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Kelas berhasil diperbarui!'];
        header("Location: kelola_kelas.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Kelas - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Edit Data Kelas</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Perbarui data kelas.</p>
            </div>
            <a href="kelola_kelas.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA KELAS *</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>
                
                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">TINGKAT *</label>
                        <select name="tingkat" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                            <option value="X" <?php echo $data['tingkat']=='X' ? 'selected' : ''; ?>>X (Sepuluh)</option>
                            <option value="XI" <?php echo $data['tingkat']=='XI' ? 'selected' : ''; ?>>XI (Sebelas)</option>
                            <option value="XII" <?php echo $data['tingkat']=='XII' ? 'selected' : ''; ?>>XII (Dua Belas)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">JURUSAN *</label>
                        <input type="text" name="jurusan" value="<?php echo htmlspecialchars($data['jurusan']); ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm uppercase focus:border-blue-600 focus:outline-none bg-slate-50">
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
                                $selected = (isset($data['tahun_ajaran_id']) && $data['tahun_ajaran_id'] == $ta['id']) ? 'selected' : '';
                                echo "<option value='{$ta['id']}' $selected>{$t_name}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-4">
                    <input type="checkbox" name="status_aktif" value="1" <?php echo $data['status_aktif'] == 1 ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Kelas Aktif</label>
                </div>
                
                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Perbarui Data Kelas
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
