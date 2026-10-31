<?php
// kelola_siswa.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Hapus
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if (mysqli_query($koneksi, "DELETE FROM t_siswa WHERE id = '$id'")) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil dihapus!'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus: ' . mysqli_error($koneksi)];
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-white text-slate-800 font-sans">
    <div class="flex min-h-screen">
        
        <?php include 'layouts/sidebar.php'; ?>

        <main class="flex-1 p-8 bg-white overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-black">Kelola Data Siswa</h1>
                <a href="tambah_siswa.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Siswa
                </a>
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

            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
                <h3 class="text-sm font-bold text-black mb-4">Daftar Siswa</h3>
                <div class="overflow-x-auto">
                    <table id="tabelData" class="w-full border-collapse border border-slate-300">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">NIS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">L/P</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">STATUS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo htmlspecialchars($data['nis']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['nama']); ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo $data['jenis_kelamin']; ?></td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                            <?php echo $data['status_aktif'] == 1 ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                        </td>
                                        <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                            <a href="edit_siswa.php?id=<?php echo $data['id']; ?>" class="text-blue-600 font-bold mr-2 hover:underline">Edit</a>
                                            <a href="kelola_siswa.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus siswa ini?')" class="text-red-600 font-bold hover:underline">Hapus</a>
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelData').DataTable({
                "searching": false, 
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>
</body>
</html>
