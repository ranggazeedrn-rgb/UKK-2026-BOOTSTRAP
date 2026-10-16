<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan / Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $pelanggaran_kategori_id = mysqli_real_escape_string($koneksi, $_POST['pelanggaran_kategori_id']);
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $poin = (int)$_POST['poin'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $query = "INSERT INTO t_pelanggaran (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif, created_at, updated_at) 
                  VALUES ('$pelanggaran_kategori_id', '$kode', '$nama', '$poin', '$deskripsi', '$status_aktif', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data jenis pelanggaran berhasil ditambahkan!'];
        }
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_pelanggaran SET 
                  pelanggaran_kategori_id = '$pelanggaran_kategori_id',
                  kode = '$kode',
                  nama = '$nama',
                  poin = '$poin',
                  deskripsi = '$deskripsi',
                  status_aktif = '$status_aktif',
                  updated_at = NOW()
                  WHERE id = '$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data jenis pelanggaran berhasil diperbarui!'];
        }
    }
    header("Location: kelola_jenis_pelanggaran.php");
    exit();
}

// Proses Hapus Data
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if (mysqli_query($koneksi, "DELETE FROM t_pelanggaran WHERE id = '$delete_id'")) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data pelanggaran berhasil dihapus!'];
    }
    header("Location: kelola_jenis_pelanggaran.php");
    exit();
}

// Filter Pencarian
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori_id'] ?? '';

$where_clauses = [];
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where_clauses[] = "(p.kode LIKE '%$s%' OR p.nama LIKE '%$s%' OR p.deskripsi LIKE '%$s%')";
}
if (!empty($kategori_filter)) {
    $where_clauses[] = "p.pelanggaran_kategori_id = '$kategori_filter'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";

$query_data = "SELECT p.*, k.nama AS nama_kategori 
               FROM t_pelanggaran p 
               LEFT JOIN t_pelanggaran_kategori k ON p.pelanggaran_kategori_id = k.id 
               $where_sql 
               ORDER BY p.id DESC";
$result = mysqli_query($koneksi, $query_data);

$kategori_res = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE status_aktif = 1 ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Jenis Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola Jenis Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Kelola data master kode, nama, dan bobot poin pelanggaran.</p>
            </div>
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Pelanggaran
            </button>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <!-- Filter Form -->
        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Cari Pelanggaran</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari kode, nama, deskripsi..." class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="w-60">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="kategori_id" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Semua Kategori --</option>
                        <?php if ($kategori_res): while ($kat = mysqli_fetch_assoc($kategori_res)): ?>
                            <option value="<?php echo $kat['id']; ?>" <?php echo $kategori_filter == $kat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($kat['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Jenis Pelanggaran</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-24">KODE</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA PELANGGARAN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">KATEGORI</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-24">POIN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-28">STATUS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-mono font-bold text-blue-600 text-center"><?php echo htmlspecialchars($data['kode']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-slate-600 font-bold"><?php echo htmlspecialchars($data['nama_kategori'] ?? '-'); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-red-600 text-center">+<?php echo $data['poin']; ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php echo $data['status_aktif'] == 1 ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="text-blue-600 font-bold mr-2 hover:underline">Edit</button>
                                        <a href="kelola_jenis_pelanggaran.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus jenis pelanggaran ini?')" class="text-red-600 font-bold hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada data pelanggaran.</td>
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
            <h3 id="modalTitle" class="text-xl font-bold text-black mb-6">Form Jenis Pelanggaran</h3>
            <form method="POST" action="kelola_jenis_pelanggaran.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">KODE *</label>
                        <input type="text" name="kode" id="inputKode" required placeholder="P01" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">BOBOT POIN *</label>
                        <input type="number" name="poin" id="inputPoin" min="1" required placeholder="10" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">KATEGORI *</label>
                    <select name="pelanggaran_kategori_id" id="inputKategori" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Pilih Kategori --</option>
                        <?php 
                        mysqli_data_seek($kategori_res, 0);
                        while ($kat = mysqli_fetch_assoc($kategori_res)): 
                        ?>
                            <option value="<?php echo $kat['id']; ?>"><?php echo htmlspecialchars($kat['nama']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">NAMA PELANGGARAN *</label>
                    <input type="text" name="nama" id="inputNama" required placeholder="Terlambat Masuk Sekolah" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">DESKRIPSI</label>
                    <textarea name="deskripsi" id="inputDeskripsi" rows="2" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Pelanggaran Aktif</label>
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
            document.getElementById('modalTitle').innerText = 'Tambah Jenis Pelanggaran';
            document.getElementById('formAction').value = 'create';
            document.getElementById('dataId').value = '';
            document.getElementById('inputKode').value = '';
            document.getElementById('inputPoin').value = '';
            document.getElementById('inputKategori').value = '';
            document.getElementById('inputNama').value = '';
            document.getElementById('inputDeskripsi').value = '';
            document.getElementById('inputStatus').checked = true;
        }

        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = 'Edit Jenis Pelanggaran';
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputKode').value = data.kode;
            document.getElementById('inputPoin').value = data.poin;
            document.getElementById('inputKategori').value = data.pelanggaran_kategori_id;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputDeskripsi').value = data.deskripsi;
            document.getElementById('inputStatus').checked = data.status_aktif == 1;
        }

        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>