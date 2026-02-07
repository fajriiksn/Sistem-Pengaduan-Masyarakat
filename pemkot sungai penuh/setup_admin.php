<?php
include 'config/koneksi.php';

// Data Admin Baru
$nik      = '999999'; // NIK Khusus Admin
$nama     = 'Admin Pusat';
$password = 'admin123'; // Password Login
$role     = 'admin';

// Enkripsi Password
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah admin sudah ada?
$cek = mysqli_query($conn, "SELECT * FROM users WHERE role='admin'");
if(mysqli_num_rows($cek) > 0){
    echo "<h3>⚠️ Akun admin sudah ada di database.</h3>";
} else {
    $query = "INSERT INTO users (nik, nama_lengkap, password, role) 
              VALUES ('$nik', '$nama', '$pass_hash', '$role')";
    
    if(mysqli_query($conn, $query)){
        echo "<h3>✅ Sukses! Akun Admin berhasil dibuat.</h3>";
        echo "<p>NIK: <b>$nik</b><br>Password: <b>$password</b></p>";
        echo "<a href='login.php'>Login Admin Disini</a>";
    } else {
        echo "Gagal: " . mysqli_error($conn);
    }
}
?>