<?php
// MULAI SESSION UNTUK CEK LOGIN
session_start();
include 'config/koneksi.php';

// 1. Logika Smart Button
$cta_link = 'login.php';
$cta_text = 'Buat Laporan Sekarang';

if (isset($_SESSION['is_login']) && $_SESSION['is_login'] == true) {
    // Jika sudah login, arahkan sesuai role
    if ($_SESSION['role'] == 'admin') {
        $cta_link = 'admin/dashboard.php';
        $cta_text = 'Ke Dashboard Admin';
    } else {
        $cta_link = 'warga/dashboard.php';
        $cta_text = 'Dashboard Saya';
    }
}

// 2. Query Statistik Laporan
$q1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Menunggu'");
$q2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Proses'");
$q3 = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Selesai'");

// Validasi Query
if (!$q1 || !$q2 || !$q3) {
    $menunggu = 0; $proses = 0; $selesai = 0;
} else {
    $menunggu = mysqli_fetch_assoc($q1)['total'];
    $proses   = mysqli_fetch_assoc($q2)['total'];
    $selesai  = mysqli_fetch_assoc($q3)['total'];
}

// 3. Query Galeri Dokumentasi
$q_docs = mysqli_query($conn, "SELECT * FROM laporan WHERE status='Selesai' AND foto_tindak_lanjut != '' ORDER BY id_laporan DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPEM - Kota Sungai Penuh</title>
    
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="container nav-content">
            <a href="index.php" class="brand">
                <img src="assets/img/logo.png" alt="Logo Sungai Penuh">
                <div class="brand-text">
                    <small>Pemerintah Kota</small>
                    <span>Sungai Penuh</span>
                </div>
            </a>

            <div style="display: flex; align-items: center;">
                <div class="nav-links">
                    <a href="#beranda" class="nav-link">Beranda</a>
                    <a href="#statistik" class="nav-link">Statistik</a>
                    <a href="#alur" class="nav-link">Alur Pengaduan</a>
                    <a href="#lokasi" class="nav-link">Peta Wilayah</a>
                </div>
                
                <div class="nav-actions">
                    <?php if (!isset($_SESSION['is_login'])): ?>
                        <a href="login.php" class="btn-cta">
                            Login <i class="ri-login-circle-line"></i>
                        </a>
                    <?php else: ?>
                        <a href="logout.php" class="btn-cta" style="background: #ef4444; color: white;">
                            Keluar <i class="ri-logout-box-r-line"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero" id="beranda">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>Layanan Aspirasi & Pengaduan Masyarakat.</h1>
                <p>
                    Selamat datang di Portal Resmi Pemerintah Kota Sungai Penuh. 
                    Sampaikan laporan Anda terkait infrastruktur, tata kota, dan fasilitas umum dengan mudah dan transparan.
                </p>
                
                <div class="hero-btn-group">
                    <a href="<?= $cta_link ?>" class="btn-primary"><?= $cta_text ?></a>
                    
                    <a href="#alur" style="display: flex; align-items: center; gap: 8px; color: var(--secondary); text-decoration: none; font-weight: 600; font-family: var(--font-primary); transition: 0.3s;">
                        <i class="ri-information-line" style="font-size: 20px; color: var(--primary);"></i> 
                        Pelajari Alur
                    </a>
                </div>
            </div>
            
            <div class="hero-image">
                <img src="assets/img/cs.png" alt="Layanan Masyarakat Sungai Penuh">
            </div>
        </div>
    </section>

    <section class="container stats-container" id="statistik">
        <div class="stats-card-wrapper">
            <div class="stat-item">
                <span class="stat-num"><?= $menunggu ?></span>
                <span class="stat-desc">Aspirasi Diterima</span>
            </div>
            <div class="stat-item">
                <span class="stat-num"><?= $proses ?></span>
                <span class="stat-desc">Sedang Ditindaklanjuti</span>
            </div>
            <div class="stat-item">
                <span class="stat-num"><?= $selesai ?></span>
                <span class="stat-desc">Laporan Diselesaikan</span>
            </div>
        </div>
    </section>

    <section class="steps-section" id="alur">
        <div class="container">
            <h2 class="section-title">Mekanisme Pelaporan</h2>
            <p class="section-subtitle">Prosedur mudah penyampaian aspirasi Anda</p>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon"><i class="ri-map-pin-add-line"></i></div>
                    <h3 class="step-title">1. Lapor & Foto</h3>
                    <p class="step-text">Isi formulir pengaduan, unggah foto bukti permasalahan, dan tentukan lokasi kejadian.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="ri-mail-check-line"></i></div>
                    <h3 class="step-title">2. Verifikasi & Disposisi</h3>
                    <p class="step-text">Laporan diverifikasi oleh Admin dan diteruskan ke instansi teknis terkait untuk ditangani.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="ri-checkbox-circle-line"></i></div>
                    <h3 class="step-title">3. Tindak Lanjut Selesai</h3>
                    <p class="step-text">Anda menerima notifikasi dan dapat melihat bukti foto penyelesaian masalah secara transparan.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="gallery-section">
        <div class="container">
            <div style="margin-bottom: 30px;">
                <h2 class="section-title" style="text-align: left;">Bukti Tindak Lanjut Nyata</h2>
                <p class="section-subtitle" style="text-align: left; margin-bottom: 0;">Kinerja terbaru penanganan masalah di lapangan</p>
            </div>

            <div class="gallery-grid">
                <?php 
                if($q_docs && mysqli_num_rows($q_docs) > 0){
                    while($row = mysqli_fetch_assoc($q_docs)) { ?>
                    <div class="gallery-item">
                        <img src="uploads/bukti_laporan/<?= $row['foto_tindak_lanjut'] ?>" alt="Bukti Selesai">
                        <div class="overlay-info">
                            <h4><?= $row['judul_laporan'] ?></h4>
                            <p><i class="ri-map-pin-line"></i> <?= $row['lokasi_nama'] ?></p>
                        </div>
                    </div>
                <?php } 
                } else { ?>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1605152276897-4f618f831968?auto=format&fit=crop&w=600&q=80" alt="Jalan">
                        <div class="overlay-info">
                            <h4>Perbaikan Jalan Berlubang</h4>
                            <p>Kecamatan Sungai Penuh</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1581094288338-2314dddb7ece?auto=format&fit=crop&w=600&q=80" alt="Lampu">
                        <div class="overlay-info">
                            <h4>Perbaikan PJU Padam</h4>
                            <p>Kecamatan Pondok Tinggi</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1590959651373-a3db0f38a961?auto=format&fit=crop&w=600&q=80" alt="Drainase">
                        <div class="overlay-info">
                            <h4>Normalisasi Saluran Air</h4>
                            <p>Kecamatan Hamparan Rawang</p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="maps-section" id="lokasi">
        <div class="container">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 class="section-title">Wilayah Jangkauan</h2>
                <p class="section-subtitle">Layanan mencakup seluruh wilayah administratif Kota Sungai Penuh</p>
            </div>
            
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63806.90483641753!2d101.35414574044123!3d-2.0622769919077243!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e2da1072973719b%3A0xc3921319c585098e!2sKota%20Sungai%20Penuh%2C%20Jambi!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div style="margin-bottom: 25px;">
                <img src="assets/img/logo.png" alt="Logo Footer" style="height: 65px;">
            </div>
            <h3>Pemerintah Kota Sungai Penuh</h3>
            <p style="max-width: 500px; margin: 0 auto 40px auto; line-height: 1.8;">
                Dinas Komunikasi, Informatika dan Statistik<br>
                Jl. Jenderal Sudirman No. 1, Sungai Penuh, Jambi
            </p>
            <hr style="border-color: rgba(255,255,255,0.1); margin-bottom: 30px;">
            <p style="font-size: 0.9rem; opacity: 0.7;">© 2024 Pemkot Sungai Penuh. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>