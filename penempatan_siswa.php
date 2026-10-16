<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Tambah Siswa ke Kelas (t_kelas_siswa)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $tahun_ajaran_id = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran_id']);
    $kelas_id = mysqli_real_escape_string($koneksi, $_POST['kelas_id']);
    $siswa_id = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);

    if ($action === 'create') {
        $query = "INSERT INTO t_kelas_siswa (tahun_ajaran_id, kelas_id, siswa_id, status_aktif, created_at, updated_at) 
                  VALUES ('$tahun_ajaran_id', '$kelas_id', '$siswa_id', 1, NOW(), NOW())";
        mysqli_query($koneksi, $query);
    }
    header("Location: penempatan_siswa.php?tahun_ajaran_id=$tahun_ajaran_id&kelas_id=$kelas_id");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $ta_id = $_GET['tahun_ajaran_id'] ?? '';
    $k_id = $_GET['kelas_id'] ?? '';
    mysqli_query($koneksi, "DELETE FROM t_kelas_siswa WHERE id = '$id'");
    header("Location: penempatan_siswa.php?tahun_ajaran_id=$ta_id&kelas_id=$k_id");
    exit();
}

$selected_ta = $_GET['tahun_ajaran_id'] ?? '';
$selected_kelas = $_GET['kelas_id'] ?? '';

// Fetch filters secara aman
$ta_list = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");
$kelas_list = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY nama ASC");
$siswa_unallocated = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY nama ASC");

$siswa_in_kelas = null;
if ($selected_ta && $selected_kelas) {
    $query_siswa = "SELECT ks.id as ks_id, s.nis, s.nama, s.jenis_kelamin 
                    FROM t_kelas_siswa ks 
                    JOIN t_siswa s ON ks.siswa_id = s.id 
                    WHERE ks.tahun_ajaran_id = '$selected_ta' AND ks.kelas_id = '$selected_kelas'
                    ORDER BY s.nama ASC";
    $siswa_in_kelas = mysqli_query($koneksi, $query_siswa);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Penempatan Siswa - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Penempatan Siswa (Plotting)</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Atur penempatan siswa ke dalam kelas berdasarkan Tahun Ajaran.</p>
            </div>
            <?php if ($selected_ta && $selected_kelas): ?>
                <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Siswa ke Kelas
                </button>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" required class="w-56 border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php 
                        if ($ta_list) {
                            mysqli_data_seek($ta_list, 0);
                            while ($ta = mysqli_fetch_assoc($ta_list)): 
                                $t_name = $ta['tahun_ajaran'] ?? $ta['nama'] ?? $ta['tahun'] ?? '-';
                                $t_sem = isset($ta['semester']) ? ucfirst($ta['semester']) : 'Ganjil';
                            ?>
                                <option value="<?php echo $ta['id']; ?>" <?php echo $selected_ta == $ta['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t_name); ?> (<?php echo htmlspecialchars($t_sem); ?>)
                                </option>
                            <?php 
                            endwhile; 
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Kelas</label>
                    <select name="kelas_id" required class="w-56 border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        <?php 
                        if ($kelas_list) {
                            mysqli_data_seek($kelas_list, 0);
                            while ($k = mysqli_fetch_assoc($kelas_list)): 
                            ?>
                                <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($k['nama']); ?>
                                </option>
                            <?php 
                            endwhile; 
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition">
                    <i class="fa-solid fa-filter mr-1"></i> Tampilkan
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Siswa di Kelas Ini</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-12">NO</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">NIS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA SISWA</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$selected_ta || !$selected_kelas): ?>
                            <tr><td colspan="4" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold text-sm">Silakan pilih Tahun Ajaran dan Kelas terlebih dahulu.</td></tr>
                        <?php elseif ($siswa_in_kelas && mysqli_num_rows($siswa_in_kelas) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($siswa_in_kelas)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center font-mono font-bold text-blue-600"><?php echo htmlspecialchars($row['nis']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <a href="penempatan_siswa.php?delete_id=<?php echo $row['ks_id']; ?>&tahun_ajaran_id=<?php echo $selected_ta; ?>&kelas_id=<?php echo $selected_kelas; ?>" onclick="return confirm('Keluarkan siswa ini dari kelas?')" class="text-red-600 font-bold hover:underline">Keluarkan</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold text-sm">Belum ada siswa yang ditempatkan di kelas ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Form -->
    <div id="modalForm" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl">
            <h3 class="text-xl font-bold text-black mb-6">Tambah Siswa ke Kelas</h3>
            <form method="POST" action="penempatan_siswa.php">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="tahun_ajaran_id" value="<?php echo htmlspecialchars($selected_ta); ?>">
                <input type="hidden" name="kelas_id" value="<?php echo htmlspecialchars($selected_kelas); ?>">
                
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 mb-2">PILIH SISWA *</label>
                    <select name="siswa_id" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Pilih Siswa --</option>
                        <?php if ($siswa_unallocated): while ($s = mysqli_fetch_assoc($siswa_unallocated)): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nis']); ?> - <?php echo htmlspecialchars($s['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeModal()" class="px-5 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg text-sm hover:bg-slate-300">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-bold rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('modalForm').classList.remove('hidden'); }
        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>