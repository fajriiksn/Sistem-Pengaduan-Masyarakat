<?php
session_start();

// Hapus semua data sesi
session_unset();
session_destroy();

// Arahkan kembali ke halaman login dengan pesan
echo "<script>
        alert('Anda berhasil keluar.');
        window.location.href='login.php';
      </script>";
?>