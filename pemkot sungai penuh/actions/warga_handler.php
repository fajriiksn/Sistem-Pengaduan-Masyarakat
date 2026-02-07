<?php
session_start();
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php"); exit;
}

// === 1. PROSES EDIT DATA ===
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id   = $_POST['id_user'];
    $nik  = htmlspecialchars($_POST['nik']);
    $nama = htmlspecialchars($_POST['nama']);
    $telp = htmlspecialchars($_POST['telp']);

    // Cek NIK duplikat (selain punya user ini sendiri)
    $cek = mysqli_query($conn, "SELECT id_user FROM users WHERE nik='$nik' AND id_user != '$id'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Gagal! NIK sudah digunakan warga lain.'); window.location.href='../admin/data_warga.php';</script>";
        exit;
    }

    $query = "UPDATE users SET nik='$nik', nama_lengkap='$nama', no_telp='$telp' WHERE id_user='$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data Warga Berhasil Diubah!'); window.location.href='../admin/data_warga.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// === 2. PROSES RESET PASSWORD ===
elseif (isset($_GET['act']) && $_GET['act'] == 'reset') {
    $id = $_GET['id'];
    
    // Default Password: 123456
    $new_pass = password_hash('123456', PASSWORD_DEFAULT);
    
    $query = "UPDATE users SET password='$new_pass' WHERE id_user='$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Password berhasil direset menjadi: 123456'); window.location.href='../admin/data_warga.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// === 3. PROSES HAPUS WARGA ===
elseif (isset($_GET['act']) && $_GET['act'] == 'hapus') {
    $id = $_GET['id'];
    
    // Hapus User (Laporan & Antrian akan ikut terhapus jika Anda setting ON DELETE CASCADE di database)
    // Jika tidak, query ini akan menghapus usernya saja.
    $query = "DELETE FROM users WHERE id_user='$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Warga Berhasil Dihapus!'); window.location.href='../admin/data_warga.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

else {
    header("location: ../admin/data_warga.php");
}
?>