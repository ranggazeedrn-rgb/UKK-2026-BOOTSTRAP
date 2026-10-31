<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: kelola_guru.php"); exit(); }

$query_data = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id = '$id'");
$data = mysqli_fetch_assoc($query_data);
if (!$data) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak ditemukan!'];
    header("Location: kelola_guru.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;
    $user_id = $data['user_id'];

    $query_guru = "UPDATE t_guru SET nip = '$nip', nama = '$nama', email = '$email', status_aktif = '$status_aktif', updated_at = NOW() WHERE id = '$id'";
    $query_user = "UPDATE t_users SET name = '$nama', email = '$email', updated_at = NOW() WHERE id = '$user_id'";
    
    if (mysqli_query($koneksi, $query_guru) && mysqli_query($koneksi, $query_user)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Guru berhasil diperbarui!'];
        header("Location: kelola_guru.php");
        exit();
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: ' . mysqli_error($koneksi)];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Guru - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Edit Data Guru</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Perbarui biodata guru dan sinkronisasi dengan akun sistem.</p>
            </div>
            <a href="kelola_guru.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="">
                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">NIP / ID PEGAWAI *</label>
                        <input type="text" name="nip" value="<?php echo htmlspecialchars($data['nip']); ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">EMAIL AKUN *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA LENGKAP *</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

                <div class="flex items-center gap-2 mb-6 mt-4">
                    <input type="checkbox" name="status_aktif" value="1" <?php echo $data['status_aktif'] == 1 ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Status Aktif</label>
                </div>
                
                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Perbarui Data Guru
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
