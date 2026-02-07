<?php
session_start();
if (isset($_SESSION['is_login'])) {
    header("location: warga/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SIPEM</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Buat Akun Baru</h2>
                <p>Isi data diri Anda untuk mulai melapor</p>
            </div>

            <form action="actions/auth_register.php" method="POST">
                
                <div class="form-group">
                    <label>NIK</label>
                    <input type="number" name="nik" class="form-control" placeholder="Contoh: 1571..." required>
                </div>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama sesuai KTP" required>
                </div>

                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="number" name="telp" class="form-control" placeholder="08..." required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Buat password aman" required>
                </div>

                <button type="submit" name="daftar" class="btn-primary">
                    Daftar Sekarang
                </button>
            </form>

            <div class="auth-footer">
                <p>Sudah punya akun? <a href="login.php">Masuk disini</a></p>
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