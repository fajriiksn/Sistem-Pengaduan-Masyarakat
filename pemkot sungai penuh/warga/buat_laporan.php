<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'warga') {
    header("location: ../login.php");
    exit;
}
$nama = $_SESSION['nama'];

// Data Kategori
$q_kat = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - SIPEM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        body { font-family: 'Inter', sans-serif; background: #f9fafb; }
        
        .form-section { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .grid-form { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }
        
        #map { height: 400px; width: 100%; border-radius: 12px; border: 2px solid #e5e7eb; z-index: 1; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { font-weight: 600; display: block; margin-bottom: 8px; color: #374151; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; background: #f9fafb; }
        .form-control:focus { outline: none; border-color: #059669; background: white; }
        
        .btn-submit { background: #059669; color: white; width: 100%; padding: 14px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: 0.2s; }
        .btn-submit:hover { background: #047857; }

        /* ================= MOBILE RESPONSIVE ================= */
        .bottom-nav { display: none; }

        @media (max-width: 768px) {
            .sidebar, .top-header { display: none; }
            .main-content { margin-left: 0; width: 100%; padding: 20px 15px 90px 15px; }
            
            /* Stack Grid jadi 1 Kolom */
            .grid-form { grid-template-columns: 1fr; gap: 20px; }
            #map { height: 250px; } /* Peta lebih pendek di HP */
            
            /* Bottom Nav */
            .bottom-nav {
                display: flex; position: fixed; bottom: 0; left: 0; right: 0;
                background: white; height: 70px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
                justify-content: space-around; align-items: center; z-index: 999;
                border-top: 1px solid #f3f4f6;
            }
            .nav-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #9ca3af; font-size: 0.75rem; }
            .nav-item i { font-size: 1.5rem; margin-bottom: 4px; }
            .nav-item.active { color: #059669; font-weight: 600; }

            /* Mobile Header */
            .mobile-header {
                display: flex; align-items: center; gap: 10px; margin-bottom: 20px; 
                padding-bottom: 15px; border-bottom: 1px solid #e5e7eb; color: #059669;
            }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="brand">
                <img src="../assets/img/logo.png" alt="Logo">
                <span>SIPEM Warga</span>
            </a>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="dashboard.php" class="nav-link"><i class="ri-dashboard-line"></i> Dashboard</a>
            <a href="buat_laporan.php" class="nav-link active"><i class="ri-file-add-line"></i> Buat Laporan Baru</a>
            <a href="riwayat.php" class="nav-link"><i class="ri-history-line"></i> Riwayat Laporan</a>
            <a href="konsultasi.php" class="nav-link"><i class="ri-message-2-line"></i> Konsultasi Admin</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="page-title">Form Pengaduan</div>
            <div class="user-profile">
                <span class="user-name"><?= $nama ?></span>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=059669&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            
            <div id="mobile-header-container"></div>

            <form action="../actions/simpan_laporan.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <div class="grid-form">
                        
                        <div>
                            <h3 style="margin-bottom: 10px; font-size: 1.1rem;">1. Lokasi Kejadian</h3>
                            <p style="color:#6b7280; font-size:0.85rem; margin-bottom:15px;">Geser pin merah ke titik lokasi yang tepat.</p>
                            
                            <div id="map"></div>
                            
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="long">

                            <div class="form-group" style="margin-top: 20px;">
                                <label class="form-label">Patokan Lokasi</label>
                                <input type="text" name="lokasi_nama" class="form-control" placeholder="Contoh: Depan Indomaret, Simpang Tiga..." required>
                            </div>
                        </div>

                        <div>
                            <h3 style="margin-bottom: 20px; font-size: 1.1rem;">2. Detail Laporan</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Judul Laporan</label>
                                <input type="text" name="judul" class="form-control" placeholder="Apa masalahnya?" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-control" required>
                                    <option value="">- Pilih Kategori -</option>
                                    <?php while($row = mysqli_fetch_assoc($q_kat)) { ?>
                                        <option value="<?= $row['id_kategori'] ?>"><?= $row['nama_kategori'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Bukti Foto</label>
                                <input type="file" name="foto" class="form-control" accept="image/*" required>
                                <small style="color: #ef4444;">*Wajib upload foto kondisi terkini</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Keterangan Lengkap</label>
                                <textarea name="isi" class="form-control" rows="4" placeholder="Ceritakan kronologi lengkapnya..." required></textarea>
                            </div>

                            <button type="submit" name="kirim" class="btn-submit">
                                <i class="ri-send-plane-fill"></i> Kirim Laporan
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <i class="ri-dashboard-line"></i>
            <span>Home</span>
        </a>
        <a href="buat_laporan.php" class="nav-item active">
            <i class="ri-add-circle-fill" style="font-size: 1.8rem;"></i>
            <span>Lapor</span>
        </a>
        <a href="riwayat.php" class="nav-item">
            <i class="ri-history-line"></i>
            <span>Riwayat</span>
        </a>
        <a href="konsultasi.php" class="nav-item">
            <i class="ri-message-2-line"></i>
            <span>Chat</span>
        </a>
        <a href="../logout.php" class="nav-item">
            <i class="ri-logout-box-r-line"></i>
            <span>Keluar</span>
        </a>
    </nav>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var defaultLat = -2.0673; 
        var defaultLng = 101.3899;

        var map = L.map('map').setView([defaultLat, defaultLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);

        function updateInput(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('long').value = lng;
        }
        updateInput(defaultLat, defaultLng);

        marker.on('dragend', function(e) {
            var position = marker.getLatLng();
            updateInput(position.lat, position.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInput(e.latlng.lat, e.latlng.lng);
        });

        // Mobile Header Inject
        if (window.innerWidth <= 768) {
            document.getElementById('mobile-header-container').innerHTML = `
                <div class="mobile-header">
                    <img src="../assets/img/logo.png" width="28">
                    <h3 style="margin:0;">Form Pengaduan</h3>
                </div>
            `;
        }
    </script>

</body>
</html>