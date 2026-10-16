<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan / Edit / Hapus Wali Kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $tahun_ajaran_id = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran_id']);
    $kelas_id = mysqli_real_escape_string($koneksi, $_POST['kelas_id']);
    $guru_id = mysqli_real_escape_string($koneksi, $_POST['guru_id']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $query = "INSERT INTO t_wali_kelas (tahun_ajaran_id, kelas_id, guru_id, status_aktif, created_at, updated_at) 
                  VALUES ('$tahun_ajaran_id', '$kelas_id', '$guru_id', '$status_aktif', NOW(), NOW())";
        mysqli_query($koneksi, $query);
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_wali_kelas SET tahun_ajaran_id='$tahun_ajaran_id', kelas_id='$kelas_id', guru_id='$guru_id', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        mysqli_query($koneksi, $query);
    }
    header("Location: walikelas.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    mysqli_query($koneksi, "DELETE FROM t_wali_kelas WHERE id = '$id'");
    header("Location: walikelas.php");
    exit();
}

// Query menggunakan SELECT ta.* untuk keamanan nama kolom
$query_data = "SELECT wk.*, g.nama as nama_guru, k.nama as nama_kelas, ta.* 
               FROM t_wali_kelas wk 
               LEFT JOIN t_guru g ON wk.guru_id = g.id 
               LEFT JOIN t_kelas k ON wk.kelas_id = k.id 
               LEFT JOIN t_tahun_ajaran ta ON wk.tahun_ajaran_id = ta.id 
               ORDER BY wk.id DESC";
$result = mysqli_query($koneksi, $query_data);

// Source Dropdown
$guru_list = mysqli_query($koneksi, "SELECT * FROM t_guru ORDER BY nama ASC");
$kelas_list = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY nama ASC");
$ta_list = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Wali Kelas - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola Wali Kelas</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Penetapan guru sebagai wali kelas per tahun ajaran.</p>
            </div>
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tetapkan Wali Kelas
            </button>
        </div>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Wali Kelas</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">TAHUN AJARAN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">KELAS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA GURU (WALI KELAS)</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">STATUS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): 
                                $thn = $data['tahun_ajaran'] ?? $data['nama'] ?? $data['tahun'] ?? '-';
                                $sem = isset($data['semester']) ? ucfirst($data['semester']) : 'Ganjil';
                            ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-slate-700">
                                        <?php echo htmlspecialchars($thn); ?> (<?php echo htmlspecialchars($sem); ?>)
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black text-center"><?php echo htmlspecialchars($data['nama_kelas'] ?? '-'); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-slate-800"><?php echo htmlspecialchars($data['nama_guru'] ?? '-'); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php echo (isset($data['status_aktif']) && $data['status_aktif'] == 1) ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="text-blue-600 font-bold mr-2 hover:underline">Edit</button>
                                        <a href="walikelas.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus penetapan wali kelas ini?')" class="text-red-600 font-bold hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada data wali kelas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Form -->
    <div id="modalForm" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white max-w-md w-full p-8 rounded-2xl shadow-xl">
            <h3 id="modalTitle" class="text-xl font-bold text-black mb-6">Form Wali Kelas</h3>
            <form method="POST" action="walikelas.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">TAHUN AJARAN *</label>
                    <select name="tahun_ajaran_id" id="inputTA" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php if ($ta_list): while ($ta = mysqli_fetch_assoc($ta_list)): 
                            $t_name = $ta['tahun_ajaran'] ?? $ta['nama'] ?? $ta['tahun'] ?? '-';
                            $t_sem = isset($ta['semester']) ? ucfirst($ta['semester']) : 'Ganjil';
                        ?>
                            <option value="<?php echo $ta['id']; ?>"><?php echo htmlspecialchars($t_name); ?> (<?php echo htmlspecialchars($t_sem); ?>)</option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">KELAS *</label>
                    <select name="kelas_id" id="inputKelas" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        <?php if ($kelas_list): while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                            <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">GURU (WALI KELAS) *</label>
                    <select name="guru_id" id="inputGuru" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Pilih Guru --</option>
                        <?php if ($guru_list): while ($g = mysqli_fetch_assoc($guru_list)): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Sebagai Wali Kelas Aktif</label>
                </div>
                
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeModal()" class="px-5 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg text-sm hover:bg-slate-300">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-bold rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(mode) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = 'Tetapkan Wali Kelas';
            document.getElementById('formAction').value = 'create';
            document.getElementById('dataId').value = '';
            document.getElementById('inputStatus').checked = true;
        }

        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = 'Edit Wali Kelas';
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputTA').value = data.tahun_ajaran_id;
            document.getElementById('inputKelas').value = data.kelas_id;
            document.getElementById('inputGuru').value = data.guru_id;
            document.getElementById('inputStatus').checked = data.status_aktif == 1;
        }

        function closeModal() {
            document.getElementById('modalForm').classList.add('hidden');
        }
    </script>
</body>
</html>