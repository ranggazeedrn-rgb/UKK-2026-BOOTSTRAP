<?php
// kelola_guru.php
session_start();
include 'config/koneksi.php'; 
include 'cek_akses.php';

// Proses Simpan / Edit Data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($action === 'create') {
        $password = 'guru123'; 
        
        $query_user = "INSERT INTO t_users (name, email, password, email_verified_at, remember_token, role, created_at, updated_at) 
                       VALUES ('$nama', '$email', '$password', NOW(), '', 'guru', NOW(), NOW())";
        
        if (mysqli_query($koneksi, $query_user)) {
            $new_user_id = mysqli_insert_id($koneksi); 
            
            $query_guru = "INSERT INTO t_guru (nip, nama, email, status_aktif, user_id, created_at, updated_at) 
                           VALUES ('$nip', '$nama', '$email', '$status_aktif', '$new_user_id', NOW(), NOW())";
            
            if (mysqli_query($koneksi, $query_guru)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Guru berhasil ditambahkan! Password login default: guru123'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal simpan biodata: ' . mysqli_error($koneksi)];
            }
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal membuat akun user: ' . mysqli_error($koneksi)];
        }
    } elseif ($action === 'update' && $id && $user_id) {
        $query_guru = "UPDATE t_guru SET nip = '$nip', nama = '$nama', email = '$email', status_aktif = '$status_aktif', updated_at = NOW() WHERE id = '$id'";
        $query_user = "UPDATE t_users SET name = '$nama', email = '$email', updated_at = NOW() WHERE id = '$user_id'";
        
        if (mysqli_query($koneksi, $query_guru) && mysqli_query($koneksi, $query_user)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Guru berhasil diperbarui!'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . mysqli_error($koneksi)];
        }
    }
    header("Location: kelola_guru.php");
    exit();
}

// Proses Hapus Data
if (isset($_GET['delete_user_id'])) {
    $del_user_id = (int)$_GET['delete_user_id'];
    $query = "DELETE FROM t_users WHERE id = '$del_user_id' AND role = 'guru'";
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Guru beserta akun login berhasil dihapus!'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus: ' . mysqli_error($koneksi)];
    }
    header("Location: kelola_guru.php");
    exit();
}

// Pencarian Filter
$search = $_GET['search'] ?? '';
$where = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE nip LIKE '%$s%' OR nama LIKE '%$s%' OR email LIKE '%$s%'";
}

// Ambil Data Guru
$query_data = "SELECT * FROM t_guru $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Data Guru</title>
    <?php include 'layouts/head.php'; ?>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            padding: 0.25rem;
            outline: none;
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans">
    <div class="flex min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-8 bg-white overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-black">Kelola Data Guru</h1>
                <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm">Tambah Guru</button>
            </div>

            <?php if (isset($_SESSION['flash'])): ?>
                <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                    <?php echo $_SESSION['flash']['message']; ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <form method="GET" class="mb-6 flex gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari NIP, Nama, atau Email..." class="px-4 py-2 border border-slate-300 rounded-lg text-sm w-72 focus:outline-none focus:border-blue-600">
                <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold">Cari</button>
            </form>

            <div class="bg-slate-100 p-6 rounded-[1.5rem]">
                <h3 class="text-sm font-bold text-black mb-4">Daftar Guru</h3>
                <div class="overflow-x-auto">
                    <table id="tabelData" class="w-full border-collapse border border-slate-300 bg-slate-100">
                        <thead>
                            <tr>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">NIP</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA LENGKAP</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">EMAIL</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">STATUS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo htmlspecialchars($data['nip']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['nama']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm"><?php echo htmlspecialchars($data['email']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                            <?php echo $data['status_aktif'] == 1 ? '<span class="text-green-600 font-bold">Aktif</span>' : '<span class="text-slate-500 font-bold">Non-Aktif</span>'; ?>
                                        </td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                            <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="text-blue-600 font-bold mr-2">Edit</button>
                                            <a href="kelola_guru.php?delete_user_id=<?php echo $data['user_id']; ?>" onclick="return confirm('Menghapus guru ini juga akan menghapus akun loginnya! Lanjutkan?')" class="text-red-600 font-bold">Hapus</a>
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
            <h3 id="modalTitle" class="text-xl font-bold text-black mb-6">Form Guru</h3>
            <form method="POST" action="kelola_guru.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">
                <input type="hidden" name="user_id" id="dataUserId">
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">NIP / ID PEGAWAI *</label>
                    <input type="text" name="nip" id="inputNip" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">NAMA LENGKAP *</label>
                    <input type="text" name="nama" id="inputNama" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">EMAIL AKUN *</label>
                    <input type="email" name="email" id="inputEmail" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
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

    <!-- JS jQuery dan DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelData').DataTable({
                "searching": false, // Menyembunyikan form pencarian bawaan DataTables
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(disaring dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>

    <script>
        function openModal(mode) {
            document.getElementById('modalForm').classList.remove('hidden');
            if(mode === 'create') {
                document.getElementById('formAction').value = 'create';
                document.getElementById('dataId').value = '';
                document.getElementById('dataUserId').value = '';
                document.getElementById('inputNip').value = '';
                document.getElementById('inputNama').value = '';
                document.getElementById('inputEmail').value = '';
                document.getElementById('inputStatus').checked = true;
            }
        }
        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('dataUserId').value = data.user_id;
            document.getElementById('inputNip').value = data.nip;
            document.getElementById('inputNama').value = data.nama;
            document.getElementById('inputEmail').value = data.email;
            document.getElementById('inputStatus').checked = parseInt(data.status_aktif) === 1;
        }
        function closeModal() { document.getElementById('modalForm').classList.add('hidden'); }
    </script>
</body>
</html>