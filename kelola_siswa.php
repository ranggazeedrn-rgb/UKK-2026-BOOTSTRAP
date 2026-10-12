<?php
// kelola_siswa.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Simpan / Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $query = "INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif, created_at, updated_at) 
                  VALUES ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', '$status_aktif', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil ditambahkan!'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal simpan data: ' . mysqli_error($koneksi)];
        }
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_siswa SET nis='$nis', nisn='$nisn', nama='$nama', jenis_kelamin='$jenis_kelamin', 
                  tanggal_lahir='$tanggal_lahir', alamat='$alamat', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil diperbarui!'];
        }
    }
    header("Location: kelola_siswa.php");
    exit();
}

// Proses Hapus
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if (mysqli_query($koneksi, "DELETE FROM t_siswa WHERE id = '$id'")) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil dihapus!'];
    }
    header("Location: kelola_siswa.php");
    exit();
}

// Pencarian
$search = $_GET['search'] ?? '';
$where = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE nis LIKE '%$s%' OR nisn LIKE '%$s%' OR nama LIKE '%$s%'";
}

$query_data = "SELECT * FROM t_siswa $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Data Siswa</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-white text-slate-800 font-sans">
    <div class="flex min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-8 bg-white overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-black">Kelola Data Siswa</h1>
                <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm">Tambah Siswa</button>
            </div>

            <?php if (isset($_SESSION['flash'])): ?>
                <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                    <?php echo $_SESSION['flash']['message']; ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form method="GET" class="mb-6 flex gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari NIS atau Nama..." class="px-4 py-2 border border-slate-300 rounded-lg text-sm w-72 focus:outline-none focus:border-blue-600">
                <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold">Cari</button>
            </form>

            <div class="bg-slate-100 p-6 rounded-[1.5rem]">
                <h3 class="text-sm font-bold text-black mb-4">Daftar Siswa</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-slate-300 bg-slate-100">
                        <thead>
                            <tr>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">NIS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">L/P</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">STATUS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo htmlspecialchars($data['nis']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['nama']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo $data['jenis_kelamin']; ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                            <?php echo $data['status_aktif'] == 1 ? '<span class="text-green-600 font-bold">Aktif</span>' : '<span class="text-slate-500 font-bold">Non-Aktif</span>'; ?>
                                        </td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                            <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="text-blue-600 font-bold mr-2">Edit</button>
                                            <a href="kelola_siswa.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus siswa ini?')" class="text-red-600 font-bold">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Form -->
    <div id="modalForm" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white max-w-lg w-full p-8 rounded-2xl shadow-xl">
            <h3 id="modalTitle" class="text-xl font-bold text-black mb-6">Form Siswa</h3>
            <form method="POST" action="kelola_siswa.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">NIS</label>
                    <input type="text" name="nis" id="inputNis" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">NISN</label>
                    <input type="text" name="nisn" id="inputNisn" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" id="inputNama" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="inputJk" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="inputTgl" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat</label>
                    <textarea name="alamat" id="inputAlamat" rows="2" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none"></textarea>
                </div>
                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked>
                    <label class="text-sm font-bold text-slate-700">Status Aktif</label>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-5 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-bold rounded-lg text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(mode) {
            document.getElementById('modalForm').classList.remove('hidden');
            if(mode === 'create') {
                document.getElementById('formAction').value = 'create';
                document.getElementById('dataId').value = '';
                document.getElementById('inputNis').value = '';
                document.getElementById('inputNisn').value = '';
                document.getElementById('inputNama').value = '';
                document.getElementById('inputAlamat').value = '';
                document.getElementById('inputStatus').checked = true;
            }
        }
        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputNis').value = data.nis;
            document.getElementById('inputNisn').value = data.nisn;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputJk').value = data.jenis_kelamin;
            document.getElementById('inputTgl').value = data.tanggal_lahir;
            document.getElementById('inputAlamat').value = data.alamat;
            document.getElementById('inputStatus').checked = data.status_aktif == 1;
        }
        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>