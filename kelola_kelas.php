<?php
// kelola_kelas.php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Hapus
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    try {
        mysqli_query($koneksi, "DELETE FROM t_kelas WHERE id = '$id'");
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Kelas berhasil dihapus!'];
    } catch (Exception $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus kelas: ' . $e->getMessage()];
    }
    header("Location: kelola_kelas.php");
    exit();
}

// Fitur Filter/Search
$search = $_GET['search'] ?? '';
$where = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE nama LIKE '%$s%' OR tingkat LIKE '%$s%' OR jurusan LIKE '%$s%'";
}

$query_data = "SELECT * FROM t_kelas $where ORDER BY tingkat ASC, jurusan ASC, nama ASC";
$result = false;
try {
    $result = mysqli_query($koneksi, $query_data);
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Kelas - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola Data Kelas</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Atur master data kelas yang tersedia di sekolah.</p>
            </div>
            <a href="tambah_kelas.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Kelas
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="GET" class="mb-6 flex gap-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Nama Kelas, Tingkat, atau Jurusan..." class="px-4 py-2 border border-slate-300 rounded-lg text-sm w-80 focus:outline-none focus:border-blue-600">
            <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold">Cari</button>
        </form>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Kelas</h3>
            <div class="overflow-x-auto">
                <table id="tabelData" class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA KELAS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">TINGKAT</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">JURUSAN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">STATUS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center font-bold text-slate-600 bg-slate-50"><?php echo htmlspecialchars($data['tingkat']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center text-blue-600 font-bold"><?php echo htmlspecialchars($data['jurusan']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php echo (isset($data['status_aktif']) && $data['status_aktif'] == 1) ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <a href="edit_kelas.php?id=<?php echo $data['id']; ?>" class="text-blue-600 font-bold mr-2 hover:underline">Edit</a>
                                        <a href="kelola_kelas.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus kelas ini secara permanen?')" class="text-red-600 font-bold hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada data kelas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
