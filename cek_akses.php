<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_file = basename($_SERVER['PHP_SELF']);

// 1. Pengecekan Login (Memeriksa semua variabel session yang mungkin diset saat login)
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['login']) || isset($_SESSION['loggedin']);

if ($current_file === 'login.php') {
    if ($is_logged_in) {
        $r = strtolower($_SESSION['role'] ?? $_SESSION['level'] ?? 'admin');
        if ($r === 'guru') header("Location: dashboard_guru.php");
        elseif ($r === 'wali kelas' || $r === 'walikelas') header("Location: dashboard_walikelas.php");
        else header("Location: dashboard_admin.php");
        exit;
    }
    return;
}

if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

// 2. Normalisasi Role
$raw_role = $_SESSION['role'] ?? $_SESSION['level'] ?? 'Admin';
$role_lower = strtolower($raw_role);

if ($role_lower === 'admin') {
    $role = 'Admin';
} elseif ($role_lower === 'guru') {
    $role = 'Guru';
} elseif ($role_lower === 'wali kelas' || $role_lower === 'walikelas') {
    $role = 'Wali Kelas';
} else {
    $role = 'Admin';
}

// 3. Pengalihan dari index.php
if ($current_file === 'index.php') {
    if ($role === 'Guru') header("Location: dashboard_guru.php");
    elseif ($role === 'Wali Kelas') header("Location: dashboard_walikelas.php");
    else header("Location: dashboard_admin.php");
    exit;
}

// 4. Hak Akses
$akses_halaman = [
    'catatan_pelanggaran.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'tindakan.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'laporan.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'riwayat.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'logout.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'dashboard_admin.php' => ['Admin'],
    'kelola_siswa.php' => ['Admin'],
    'kelola_guru.php' => ['Admin'],
    'kelola_kelas.php' => ['Admin'],
    'kelola_tahun_ajaran.php' => ['Admin'],
    'penempatan_siswa.php' => ['Admin'],
    'walikelas.php' => ['Admin'],
    'kelola_jenis_pelanggaran.php' => ['Admin'],
    'kelola_kategori_pelanggaran.php' => ['Admin'],
    'cetak_export.php' => ['Admin'],
    'dashboard_guru.php' => ['Guru'],
    'dashboard_walikelas.php' => ['Wali Kelas'],
    'rekap_point.php' => ['Wali Kelas']
];

// 5. Validasi Akses Halaman
if (array_key_exists($current_file, $akses_halaman)) {
    if (!in_array($role, $akses_halaman[$current_file])) {
        if ($role === 'Admin' && $current_file !== 'dashboard_admin.php') {
            header("Location: dashboard_admin.php");
            exit;
        } elseif ($role === 'Guru' && $current_file !== 'dashboard_guru.php') {
            header("Location: dashboard_guru.php");
            exit;
        } elseif ($role === 'Wali Kelas' && $current_file !== 'dashboard_walikelas.php') {
            header("Location: dashboard_walikelas.php");
            exit;
        }
    }
}
?>