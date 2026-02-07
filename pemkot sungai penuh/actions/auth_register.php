<?php
include '../config/koneksi.php';

if (isset($_POST['daftar'])) {
    $nik      = htmlspecialchars($_POST['nik']);
    $nama     = htmlspecialchars($_POST['nama']);
    $telp     = htmlspecialchars($_POST['telp']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 1. Cek Apakah NIK Sudah Ada?
    $cek = mysqli_query($conn, "SELECT nik FROM users WHERE nik = '$nik'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIK sudah terdaftar! Silakan Login.'); window.location.href='../login.php';</script>";
        exit;
    }

    // 2. Enkripsi Password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // 3. Simpan ke Database (Default Role = warga)
    $query = "INSERT INTO users (nik, nama_lengkap, no_telp, password, role) 
              VALUES ('$nik', '$nama', '$telp', '$password_hashed', 'warga')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Pendaftaran Berhasil! Silakan Login.');
                window.location.href='../login.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal Mendaftar: " . mysqli_error($conn) . "');
                window.location.href='../register.php';
              </script>";
    }
}
?>