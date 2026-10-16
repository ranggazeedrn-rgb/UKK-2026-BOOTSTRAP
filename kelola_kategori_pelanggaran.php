<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $query = "INSERT INTO t_pelanggaran_kategori (nama, status_aktif, created_at, updated_at) VALUES ('$nama', '$status_aktif', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori Pelanggaran berhasil ditambahkan!'];
        }
    } elseif ($action === 'update' && $id) {
        $query = "UPDATE t_pelanggaran_kategori SET nama='$nama', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori Pelanggaran berhasil diperbarui!'];
        }
    }
    header("Location: kelola_kategori_pelanggaran.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if (mysqli_query($koneksi, "DELETE FROM t_pelanggaran_kategori WHERE id = '$id'")) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori Pelanggaran berhasil dihapus!'];
    }
    header("Location: kelola_kategori_pelanggaran.php");
    exit();
}

$query_data = "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC";
$result = mysqli_query($koneksi, $query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Kategori Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola Kategori Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Atur pengelompokan jenis pelanggaran siswa.</p>
            </div>
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kategori
            </button>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Kategori Pelanggaran</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA KATEGORI</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">STATUS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php echo $data['status_aktif'] == 1 ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="text-blue-600 font-bold mr-2 hover:underline">Edit</button>
                                        <a href="kelola_kategori_pelanggaran.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus kategori ini?')" class="text-red-600 font-bold hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada kategori pelanggaran.</td>
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
            <h3 id="modalTitle" class="text-xl font-bold text-black mb-6">Form Kategori</h3>
            <form method="POST" action="kelola_kategori_pelanggaran.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">NAMA KATEGORI *</label>
                    <input type="text" name="nama" id="inputNama" required placeholder="Contoh: Kerajinan, Sikap" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>

                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Kategori Aktif</label>
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
            document.getElementById('modalTitle').innerText = 'Tambah Kategori Pelanggaran';
            document.getElementById('formAction').value = 'create';
            document.getElementById('dataId').value = '';
            document.getElementById('inputNama').value = '';
            document.getElementById('inputStatus').checked = true;
        }

        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = 'Edit Kategori Pelanggaran';
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputStatus').checked = data.status_aktif == 1;
        }

        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>