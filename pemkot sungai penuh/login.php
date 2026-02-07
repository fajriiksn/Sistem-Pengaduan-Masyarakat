<?php
session_start();
if (isset($_SESSION['is_login'])) {
    if ($_SESSION['role'] == 'admin') {
        header("location: admin/dashboard.php");
    } else {
        header("location: warga/dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIPEM</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <img src="assets/img/logo.png" alt="Logo" style="height: 50px; margin-bottom: 15px;">
                <h2>Selamat Datang</h2>
                <p>Masuk untuk melaporkan masalah kota</p>
            </div>

            <form action="actions/auth_login.php" method="POST">
                <div class="form-group">
                    <label>NIK (Nomor Induk Kependudukan)</label>
                    <input type="number" name="nik" class="form-control" placeholder="Masukkan NIK" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="btn-primary">
                    Masuk Sekarang <i class="ri-arrow-right-line" style="margin-left: 10px;"></i>
                </button>
            </form>

            <div class="auth-footer">
                <p>Belum punya akun? <a href="register.php">Daftar Warga</a></p>
                <p style="margin-top: 15px;">
                    <a href="index.php" style="color: #64748b; text-decoration: none; font-size: 0.85rem;">
                        &larr; Kembali ke Beranda
                    </a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>