<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['login'])) {
    $nik = htmlspecialchars($_POST['nik']);
    $password = $_POST['password'];

    // 1. Cek Data User berdasarkan NIK
    $query = mysqli_query($conn, "SELECT * FROM users WHERE nik = '$nik'");
    
    // Jika data ditemukan
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        // 2. Verifikasi Password (Hash)
        if (password_verify($password, $data['password'])) {
            
            // 3. Set Session
            $_SESSION['is_login'] = true;
            $_SESSION['id_user']  = $data['id_user'];
            $_SESSION['nama']     = $data['nama_lengkap'];
            $_SESSION['role']     = $data['role'];

            // 4. Redirect Sesuai Role
            if ($data['role'] == 'admin') {
                echo "<script>alert('Selamat Datang Admin!'); window.location.href='../admin/dashboard.php';</script>";
            } else {
                echo "<script>alert('Login Berhasil!'); window.location.href='../warga/dashboard.php';</script>";
            }

        } else {
            // Password Salah
            echo "<script>alert('Password Salah!'); window.location.href='../login.php';</script>";
        }

    } else {
        // NIK Tidak Ditemukan
        echo "<script>alert('NIK tidak terdaftar. Silakan daftar dulu.'); window.location.href='../register.php';</script>";
    }
}
?>