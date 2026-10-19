<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $tahun_ajaran = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran'] ?? $_POST['nama'] ?? '');
    $semester = mysqli_real_escape_string($koneksi, $_POST['semester'] ?? 'ganjil');
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if ($status_aktif == 1) {
        @mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET status_aktif = 0");
    }

    if ($action === 'create') {
        $check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM t_tahun_ajaran LIKE 'tahun_ajaran'");
        if ($check_col && mysqli_num_rows($check_col) > 0) {
            $query = "INSERT INTO t_tahun_ajaran (tahun_ajaran, semester, status_aktif, created_at, updated_at) 
                      VALUES ('$tahun_ajaran', '$semester', '$status_aktif', NOW(), NOW())";
        } else {
            $query = "INSERT INTO t_tahun_ajaran (nama, semester, status_aktif, created_at, updated_at) 
                      VALUES ('$tahun_ajaran', '$semester', '$status_aktif', NOW(), NOW())";
        }
        @mysqli_query($koneksi, $query);
    } elseif ($action === 'update' && $id) {
        $check_col = mysqli_query($koneksi, "SHOW COLUMNS FROM t_tahun_ajaran LIKE 'tahun_ajaran'");
        if ($check_col && mysqli_num_rows($check_col) > 0) {
            $query = "UPDATE t_tahun_ajaran SET tahun_ajaran='$tahun_ajaran', semester='$semester', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        } else {
            $query = "UPDATE t_tahun_ajaran SET nama='$tahun_ajaran', semester='$semester', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";
        }
        @mysqli_query($koneksi, $query);
    }
    header("Location: kelola_tahun_ajaran.php");
    exit();
}

$result = @mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Tahun Ajaran - Sistem Kedisiplinan</title>
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
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola Tahun Ajaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Atur periode tahun ajaran aktif pada sistem.</p>
            </div>
            <button onclick="openModal('create')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Tahun Ajaran
            </button>
        </div>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Tahun Ajaran</h3>
            <div class="overflow-x-auto">
                <table id="tabelData" class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">TAHUN AJARAN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">SEMESTER</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">STATUS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): 
                                $val_tahun = $data['tahun_ajaran'] ?? $data['nama'] ?? $data['tahun'] ?? '-';
                                if (isset($data['semester'])) {
                                    $val_semester = ucfirst($data['semester']);
                                } elseif (isset($data['tanggal_mulai']) && isset($data['tanggal_selesai'])) {
                                    $val_semester = date('d/m/Y', strtotime($data['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($data['tanggal_selesai']));
                                } else {
                                    $val_semester = 'Ganjil';
                                }
                            ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($val_tahun); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center capitalize font-bold text-slate-600"><?php echo htmlspecialchars($val_semester); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php echo (isset($data['status_aktif']) && $data['status_aktif'] == 1) ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <button onclick='openEditModal(<?php echo json_encode($data); ?>)' class="text-blue-600 font-bold hover:underline">Edit</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada data tahun ajaran.</td>
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
            <h3 id="modalTitle" class="text-xl font-bold text-black mb-6">Form Tahun Ajaran</h3>
            <form method="POST" action="kelola_tahun_ajaran.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="dataId">
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">TAHUN AJARAN * (Contoh: 2025/2026)</label>
                    <input type="text" name="tahun_ajaran" id="inputTahun" placeholder="2025/2026" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">SEMESTER *</label>
                    <select name="semester" id="inputSemester" required class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 mb-6">
                    <input type="checkbox" name="status_aktif" id="inputStatus" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Sebagai Tahun Ajaran Aktif</label>
                </div>
                
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeModal()" class="px-5 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg text-sm hover:bg-slate-300">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-bold rounded-lg text-sm hover:bg-blue-700">Simpan</button>
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
            document.getElementById('modalTitle').innerText = 'Tambah Tahun Ajaran';
            document.getElementById('formAction').value = 'create';
            document.getElementById('dataId').value = '';
            document.getElementById('inputTahun').value = '';
            document.getElementById('inputSemester').value = 'ganjil';
            document.getElementById('inputStatus').checked = true;
        }

        function openEditModal(data) {
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = 'Edit Tahun Ajaran';
            document.getElementById('formAction').value = 'update';
            document.getElementById('dataId').value = data.id;
            document.getElementById('inputTahun').value = data.tahun_ajaran || data.nama || '';
            document.getElementById('inputSemester').value = data.semester || 'ganjil';
            document.getElementById('inputStatus').checked = data.status_aktif == 1;
        }

        function closeModal() {
            document.getElementById('modalForm').classList.add('hidden');
        }
    </script>
</body>
</html>