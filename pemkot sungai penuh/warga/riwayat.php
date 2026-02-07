<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'warga') {
    header("location: ../login.php");
    exit;
}
$id_user = $_SESSION['id_user'];
$nama    = $_SESSION['nama'];

// Ambil Data Laporan User Ini
$query = mysqli_query($conn, "SELECT * FROM laporan WHERE id_user='$id_user' ORDER BY tgl_laporan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan - SIPEM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f9fafb; }
        
        .table-card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        
        /* Table Styles */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f3f4f6; }
        th { background: #f9fafb; font-size: 0.85rem; text-transform: uppercase; color: #6b7280; font-weight: 600; }
        
        /* Status Badges */
        .badge { padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .bg-wait { background: #fffbeb; color: #b45309; }
        .bg-proc { background: #eff6ff; color: #1d4ed8; }
        .bg-done { background: #f0fdf4; color: #15803d; }
        .bg-fail { background: #fef2f2; color: #dc2626; }

        /* Mobile Nav & Header */
        .bottom-nav { display: none; }
        @media (max-width: 768px) {
            .sidebar, .top-header { display: none; }
            .main-content { margin-left: 0; width: 100%; padding: 20px 15px 90px 15px; }
            
            .bottom-nav {
                display: flex; position: fixed; bottom: 0; left: 0; right: 0;
                background: white; height: 70px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
                justify-content: space-around; align-items: center; z-index: 999;
                border-top: 1px solid #f3f4f6;
            }
            .nav-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #9ca3af; font-size: 0.75rem; }
            .nav-item i { font-size: 1.5rem; margin-bottom: 4px; }
            .nav-item.active { color: #059669; font-weight: 600; }

            .mobile-header {
                display: flex; align-items: center; justify-content: space-between;
                margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;
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
            <a href="buat_laporan.php" class="nav-link"><i class="ri-file-add-line"></i> Buat Laporan Baru</a>
            <a href="riwayat.php" class="nav-link active"><i class="ri-history-line"></i> Riwayat Laporan</a>
            <a href="konsultasi.php" class="nav-link"><i class="ri-message-2-line"></i> Konsultasi Admin</a>
        </nav>
        <div class="sidebar-footer"><a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a></div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="page-title">Riwayat Laporan Saya</div>
            <div class="user-profile">
                <span class="user-name"><?= $nama ?></span>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=059669&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            
            <div id="mobile-header-container"></div>

            <div class="table-card">
                <h3 style="margin-bottom: 20px; color: #374151;">Daftar Laporan</h3>
                
                <div class="table-responsive">
                    <?php if(mysqli_num_rows($query) > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Judul Laporan</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; while($row = mysqli_fetch_assoc($query)) { 
                                $status = $row['status'];
                                $badge = 'bg-wait';
                                if($status == 'Proses') $badge = 'bg-proc';
                                if($status == 'Selesai') $badge = 'bg-done';
                                if($status == 'Ditolak') $badge = 'bg-fail';
                                
                                // FIX: Nama kolom sesuai database Anda
                                $foto = isset($row['foto_laporan']) ? $row['foto_laporan'] : '';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($row['tgl_laporan'])) ?></td>
                                <td><strong><?= $row['judul_laporan'] ?></strong></td>
                                <td><?= $row['lokasi_nama'] ?></td>
                                <td><span class="badge <?= $badge ?>"><?= $status ?></span></td>
                                <td>
                                    <?php if(!empty($foto)): ?>
                                        <a href="../uploads/bukti_laporan/<?= $foto ?>" target="_blank" style="color: #059669; text-decoration: none; font-weight: 500;">
                                            <i class="ri-image-line"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #9ca3af; font-size: 0.8rem;">- Tidak ada -</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <div style="text-align: center; padding: 40px; color: #9ca3af;">
                            <i class="ri-folder-open-line" style="font-size: 3rem; opacity: 0.5;"></i>
                            <p>Anda belum pernah membuat laporan.</p>
                            <a href="buat_laporan.php" style="color: #059669; font-weight: 600;">Buat Laporan Sekarang</a>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </main>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <i class="ri-dashboard-line"></i>
            <span>Home</span>
        </a>
        <a href="buat_laporan.php" class="nav-item">
            <i class="ri-add-circle-line" style="font-size: 1.8rem;"></i>
            <span>Lapor</span>
        </a>
        <a href="riwayat.php" class="nav-item active">
            <i class="ri-history-fill"></i>
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

    <script>
        if (window.innerWidth <= 768) {
            document.getElementById('mobile-header-container').innerHTML = `
                <div class="mobile-header">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="../assets/img/logo.png" width="28">
                        <h3 style="margin:0; font-size:1.1rem; color:#059669;">Riwayat Saya</h3>
                    </div>
                </div>
            `;
        }
    </script>
</body>
</html>