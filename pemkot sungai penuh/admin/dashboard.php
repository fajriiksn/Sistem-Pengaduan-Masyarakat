<?php
session_start();
include '../config/koneksi.php';

// Cek Login & Role Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php");
    exit;
}

$nama = $_SESSION['nama'];

// Hitung Statistik Dasar
$q1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan"));
$q2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Menunggu'"));
$q3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Proses'"));
$q4 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Selesai'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPEM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; }
        
        /* STYLE KHUSUS BADGE NOTIFIKASI */
        .sidebar-menu .nav-link {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        
        .badge-notif {
            background-color: #ef4444;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 50px;
            min-width: 20px;
            text-align: center;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 2s infinite;
            display: none; /* Sembunyi default */
        }
        
        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* Stats Grid Styles */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; }
        .stat-card { background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; border: 1px solid #e5e7eb; transition: 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .stat-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .stat-info h3 { font-size: 1.8rem; font-weight: 700; margin: 0; color: #111827; }
        .stat-info p { margin: 5px 0 0 0; color: #6b7280; font-size: 0.9rem; font-weight: 500; }
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
            
            <a href="dashboard.php" class="nav-link active">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-dashboard-3-line"></i> Dashboard
                </div>
            </a>
            
            <a href="laporan.php" class="nav-link">
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
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="page-title">Ringkasan Sistem</div>
            <div class="user-profile">
                <span class="user-name"><?= $nama ?></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=2563eb&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="ri-inbox-archive-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $q1['total'] ?></h3>
                        <p>Total Laporan</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">
                        <i class="ri-alarm-warning-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $q2['total'] ?></h3>
                        <p>Perlu Verifikasi</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #fffbeb; color: #d97706;">
                        <i class="ri-hammer-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $q3['total'] ?></h3>
                        <p>Sedang Dikerjakan</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $q4['total'] ?></h3>
                        <p>Laporan Selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            // Cek notifikasi pertama kali
            checkQueueNotification();

            // Cek terus setiap 3 detik
            setInterval(function() {
                checkQueueNotification();
            }, 3000);
        });

        function checkQueueNotification() {
            $.ajax({
                url: '../actions/ajax_konsultasi.php',
                type: 'POST',
                data: { action: 'check_notif' }, // Perhatikan action ini sesuai backend
                dataType: 'json',
                success: function(response) {
                    var total = parseInt(response.total);
                    var badge = $('#queue-badge');

                    if (total > 0) {
                        badge.text(total);
                        badge.fadeIn(); // Munculkan Badge
                    } else {
                        badge.fadeOut(); // Sembunyikan Badge
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Gagal cek notifikasi: " + error);
                }
            });
        }
    </script>

</body>
</html>