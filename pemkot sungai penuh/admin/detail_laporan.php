<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php"); exit;
}
$nama_admin = $_SESSION['nama'];

// Cek ID Laporan
if(!isset($_GET['id'])) { header("location: laporan.php"); exit; }
$id_laporan = $_GET['id'];

// --- PROSES UPDATE STATUS & TANGGAPAN (JIKA FORM DISUBMIT) ---
if(isset($_POST['update_laporan'])) {
    $status    = $_POST['status'];
    $tanggapan = htmlspecialchars($_POST['tanggapan']);
    $tgl_selesai = ($status == 'Selesai') ? date('Y-m-d H:i:s') : NULL;
    
    // Handle Upload Foto Tindak Lanjut (Opsional)
    $foto_tindak_lanjut = $_POST['foto_lama']; // Default pakai foto lama jika ada
    
    if(!empty($_FILES['bukti_selesai']['name'])) {
        $nama_file = time() . '_' . $_FILES['bukti_selesai']['name'];
        $tmp_file  = $_FILES['bukti_selesai']['tmp_name'];
        $path      = "../uploads/tindak_lanjut/";
        
        // Buat folder jika belum ada
        if (!file_exists($path)) { mkdir($path, 0777, true); }
        
        if(move_uploaded_file($tmp_file, $path . $nama_file)) {
            $foto_tindak_lanjut = $nama_file;
        }
    }

    // Query Update
    $q_update = "UPDATE laporan SET 
                 status='$status', 
                 tanggapan_admin='$tanggapan', 
                 foto_tindak_lanjut='$foto_tindak_lanjut'";
    
    if($status == 'Selesai') {
        $q_update .= ", tgl_selesai='$tgl_selesai'";
    }

    $q_update .= " WHERE id_laporan='$id_laporan'";

    if(mysqli_query($conn, $q_update)) {
        echo "<script>alert('Laporan berhasil diperbarui!'); window.location.href='detail_laporan.php?id=$id_laporan';</script>";
    } else {
        echo "<script>alert('Gagal update database.');</script>";
    }
}

// --- AMBIL DATA LAPORAN ---
$query = mysqli_query($conn, "
    SELECT laporan.*, users.nama_lengkap, users.no_telp, kategori.nama_kategori 
    FROM laporan 
    JOIN users ON laporan.id_user = users.id_user
    JOIN kategori ON laporan.id_kategori = kategori.id_kategori
    WHERE laporan.id_laporan = '$id_laporan'
");
$data = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if(!$data) { echo "Laporan tidak ditemukan."; exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; }

        /* Layout Grid Detail */
        .detail-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px; margin-top: 20px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; margin-bottom: 20px; }
        
        .section-title { font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 15px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px; }

        /* Map & Image */
        #map { height: 300px; width: 100%; border-radius: 12px; margin-bottom: 15px; z-index: 1; }
        .bukti-img { width: 100%; height: auto; border-radius: 12px; border: 1px solid #e5e7eb; }
        
        /* Info Rows */
        .info-row { margin-bottom: 12px; }
        .info-label { display: block; font-size: 0.85rem; color: #6b7280; margin-bottom: 3px; }
        .info-value { font-weight: 600; color: #1f2937; font-size: 1rem; }
        
        /* Form Styles */
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; }
        .btn-update { background: #2563eb; color: white; width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .btn-update:hover { background: #1d4ed8; }
        .btn-back { background: #e5e7eb; color: #374151; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }

        /* Status Badge */
        .badge-lg { padding: 8px 16px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; display: inline-block; }
        .status-Menunggu { background: #fff7ed; color: #c2410c; }
        .status-Proses { background: #eff6ff; color: #1d4ed8; }
        .status-Selesai { background: #f0fdf4; color: #15803d; }
        .status-Ditolak { background: #fef2f2; color: #dc2626; }

        /* Sidebar Badge */
        .sidebar-menu .nav-link { display: flex !important; justify-content: space-between !important; align-items: center !important; }
        .badge-notif { background-color: #ef4444; color: white; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 50px; min-width: 20px; text-align: center; animation: pulse-red 2s infinite; display: none; }
        @keyframes pulse-red { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }

        @media(max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="brand">
                <img src="../assets/img/logo.png" alt="Logo">
                <span>Admin Panel</span>
            </a>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Pengelolaan</div>
            
            <a href="dashboard.php" class="nav-link">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-dashboard-3-line"></i> Dashboard
                </div>
            </a>
            
            <a href="laporan.php" class="nav-link active">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-file-list-3-line"></i> Laporan Masuk
                </div>
            </a>
            
            <a href="antrian_konsultasi.php" class="nav-link">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-customer-service-2-line"></i> Konsultasi Live
                </div>
                <span id="queue-badge" class="badge-notif">0</span>
            </a>
            
            <a href="riwayat_konsultasi.php" class="nav-link">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-history-line"></i> Riwayat Chat
                </div>
            </a>
            
            <a href="data_warga.php" class="nav-link">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-group-line"></i> Data Warga
                </div>
            </a>
        </nav>
        <div class="sidebar-footer"><a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a></div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="page-title">
                <a href="laporan.php" style="color: #6b7280; margin-right: 10px; text-decoration: none;"><i class="ri-arrow-left-line"></i></a>
                Detail Laporan #<?= $data['id_laporan'] ?>
            </div>
            <div class="user-profile">
                <span class="user-name"><?= $nama_admin ?></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=2563eb&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            
            <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="display:block; color:#6b7280; font-size:0.9rem;">Status Laporan Saat Ini:</span>
                    <span class="badge-lg status-<?= $data['status'] ?>"><?= $data['status'] ?></span>
                </div>
                <a href="laporan.php" class="btn-back"><i class="ri-arrow-left-line"></i> Kembali</a>
            </div>

            <div class="detail-grid">
                
                <div class="left-col">
                    <div class="card">
                        <div class="section-title"><i class="ri-file-list-2-line"></i> Informasi Pelapor</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="info-row">
                                <span class="info-label">Nama Pelapor</span>
                                <span class="info-value"><?= $data['nama_lengkap'] ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Nomor WhatsApp</span>
                                <span class="info-value">
                                    <a href="https://wa.me/62<?= substr($data['no_telp'], 1) ?>" target="_blank" style="color:#059669; text-decoration:none;">
                                        <?= $data['no_telp'] ?> <i class="ri-whatsapp-line"></i>
                                    </a>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tanggal Lapor</span>
                                <span class="info-value"><?= date('d M Y, H:i', strtotime($data['tgl_laporan'])) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kategori</span>
                                <span class="info-value"><?= $data['nama_kategori'] ?></span>
                            </div>
                        </div>
                        
                        <div class="info-row" style="margin-top: 15px;">
                            <span class="info-label">Judul Laporan</span>
                            <span class="info-value"><?= $data['judul_laporan'] ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Isi Laporan</span>
                            <p style="background: #f9fafb; padding: 10px; border-radius: 8px; line-height: 1.5; color: #374151;">
                                <?= nl2br(htmlspecialchars($data['isi_laporan'])) ?>
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="section-title"><i class="ri-map-pin-line"></i> Lokasi & Bukti</div>
                        <div class="info-row">
                            <span class="info-label">Lokasi Patokan</span>
                            <span class="info-value"><?= $data['lokasi_nama'] ?></span>
                        </div>
                        
                        <div id="map"></div>
                        
                        <div class="info-row">
                            <span class="info-label">Foto Bukti Awal</span>
                            <?php if(!empty($data['foto_laporan'])): ?>
                                <img src="../uploads/bukti_laporan/<?= $data['foto_laporan'] ?>" class="bukti-img" alt="Bukti Laporan">
                            <?php else: ?>
                                <p style="color: #9ca3af;">Tidak ada foto.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="right-col">
                    <div class="card" style="position: sticky; top: 20px;">
                        <div class="section-title"><i class="ri-hammer-line"></i> Proses Tindak Lanjut</div>
                        
                        <form action="" method="POST" enctype="multipart/form-data">
                            
                            <div class="form-group">
                                <label class="info-label">Update Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Menunggu" <?= $data['status']=='Menunggu'?'selected':'' ?>>Menunggu</option>
                                    <option value="Proses" <?= $data['status']=='Proses'?'selected':'' ?>>Sedang Diproses</option>
                                    <option value="Selesai" <?= $data['status']=='Selesai'?'selected':'' ?>>Selesai</option>
                                    <option value="Ditolak" <?= $data['status']=='Ditolak'?'selected':'' ?>>Ditolak</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="info-label">Tanggapan Admin (Pesan untuk Warga)</label>
                                <textarea name="tanggapan" rows="5" class="form-control" placeholder="Tulis tanggapan atau alasan penolakan..."><?= $data['tanggapan_admin'] ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="info-label">Foto Bukti Penanganan (Jika Selesai)</label>
                                <input type="file" name="bukti_selesai" class="form-control" accept="image/*">
                                <input type="hidden" name="foto_lama" value="<?= $data['foto_tindak_lanjut'] ?>">
                                
                                <?php if(!empty($data['foto_tindak_lanjut'])): ?>
                                    <div style="margin-top: 10px;">
                                        <small>Bukti Tindak Lanjut Terkini:</small>
                                        <img src="../uploads/tindak_lanjut/<?= $data['foto_tindak_lanjut'] ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; display: block; margin-top: 5px;">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" name="update_laporan" class="btn-update">
                                <i class="ri-save-3-line"></i> Simpan Perubahan
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {
            // Realtime Notif
            checkQueueNotification();
            setInterval(checkQueueNotification, 3000);

            // Initialize Map
            var lat = <?= $data['latitude'] ?>;
            var lng = <?= $data['longitude'] ?>;
            
            var map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>Lokasi Laporan</b><br><?= $data['lokasi_nama'] ?>")
                .openPopup();
        });

        function checkQueueNotification() {
            $.ajax({
                url: '../actions/ajax_konsultasi.php', type: 'POST',
                data: { action: 'check_notif' }, dataType: 'json',
                success: function(response) {
                    var total = parseInt(response.total);
                    var badge = $('#queue-badge');
                    if (total > 0) { badge.text(total); badge.fadeIn(); } 
                    else { badge.fadeOut(); }
                }
            });
        }
    </script>

</body>
</html>