<?php
session_start();
include '../config/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'warga') {
    header("location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama    = $_SESSION['nama'];

// Hitung Statistik Laporan User Ini
$q1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE id_user='$id_user'"));
$q2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE id_user='$id_user' AND status='Proses'"));
$q3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE id_user='$id_user' AND status='Selesai'"));

// Ambil 5 Laporan Terakhir
$q_recent = mysqli_query($conn, "SELECT * FROM laporan WHERE id_user='$id_user' ORDER BY tgl_laporan DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Warga - SIPEM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background: #f9fafb; }
        
        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 15px; }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        
        /* Table */
        .table-container { background: white; padding: 20px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f3f4f6; }
        .badge { padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        
        /* ================= MOBILE RESPONSIVE ================= */
        .bottom-nav { display: none; } /* Default Hidden di Desktop */

        @media (max-width: 768px) {
            .sidebar, .top-header { display: none; } /* Sembunyikan Sidebar & Header Desktop */
            
            .main-content { margin-left: 0; width: 100%; padding: 20px 15px 80px 15px; }
            
            .stats-grid { grid-template-columns: 1fr; gap: 15px; }
            
            /* Bottom Nav Styles */
            .bottom-nav {
                display: flex; position: fixed; bottom: 0; left: 0; right: 0;
                background: white; height: 70px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
                justify-content: space-around; align-items: center; z-index: 999;
                border-top: 1px solid #f3f4f6;
            }
            .nav-item {
                display: flex; flex-direction: column; align-items: center;
                text-decoration: none; color: #9ca3af; font-size: 0.75rem;
            }
            .nav-item i { font-size: 1.5rem; margin-bottom: 4px; }
            .nav-item.active { color: #059669; font-weight: 600; }

            /* Mobile Header Styles */
            .mobile-header {
                display: flex; justify-content: space-between; align-items: center;
                margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;
            }
            .welcome-text h2 { font-size: 1.2rem; margin: 0; color: #111827; }
            .welcome-text p { margin: 0; font-size: 0.85rem; color: #6b7280; }
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
            <a href="dashboard.php" class="nav-link active"><i class="ri-dashboard-line"></i> Dashboard</a>
            <a href="buat_laporan.php" class="nav-link"><i class="ri-file-add-line"></i> Buat Laporan Baru</a>
            <a href="riwayat.php" class="nav-link"><i class="ri-history-line"></i> Riwayat Laporan</a>
            <a href="konsultasi.php" class="nav-link"><i class="ri-message-2-line"></i> Konsultasi Admin</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        
        <header class="top-header">
            <div class="page-title">Dashboard Overview</div>
            <div class="user-profile">
                <span class="user-name"><?= $nama ?></span>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=059669&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            
            <div id="mobile-header-container"></div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div>
                        <h3 style="margin:0; font-size:1.5rem;"><?= $q1['total'] ?></h3>
                        <span style="color:#6b7280; font-size:0.9rem;">Total Laporan</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fffbeb; color: #d97706;">
                        <i class="ri-loader-2-line"></i>
                    </div>
                    <div>
                        <h3 style="margin:0; font-size:1.5rem;"><?= $q2['total'] ?></h3>
                        <span style="color:#6b7280; font-size:0.9rem;">Sedang Proses</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div>
                        <h3 style="margin:0; font-size:1.5rem;"><?= $q3['total'] ?></h3>
                        <span style="color:#6b7280; font-size:0.9rem;">Selesai</span>
                    </div>
                </div>
            </div>

            <h3 style="margin-bottom: 15px; color: #374151;">Laporan Terakhir</h3>
            <div class="table-container">
                <?php if(mysqli_num_rows($q_recent) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul Laporan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($q_recent)) { 
                            $st = $row['status'];
                            $col = 'orange';
                            if($st=='Proses') $col='blue';
                            if($st=='Selesai') $col='green';
                            if($st=='Ditolak') $col='red';
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tgl_laporan'])) ?></td>
                            <td><?= $row['judul_laporan'] ?></td>
                            <td><span class="badge" style="background: <?= $col ?>; color: white;"><?= $st ?></span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                    <p style="text-align:center; color:#9ca3af; padding:20px;">Belum ada laporan yang dibuat.</p>
                <?php } ?>
            </div>
            
        </div>
    </main>

    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item active">
            <i class="ri-dashboard-fill"></i>
            <span>Home</span>
        </a>
        <a href="buat_laporan.php" class="nav-item">
            <i class="ri-add-circle-line" style="font-size: 1.8rem;"></i>
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

    <script>
        // Inject Mobile Header jika di HP
        if (window.innerWidth <= 768) {
            document.getElementById('mobile-header-container').innerHTML = `
                <div class="mobile-header">
                    <div class="welcome-text">
                        <h2>Halo, <?= explode(' ', $nama)[0] ?> 👋</h2>
                        <p>Selamat datang di SIPEM</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=059669&color=fff" style="width:40px; height:40px; border-radius:50%;">
                </div>
            `;
        }
    </script>
</body>
</html>