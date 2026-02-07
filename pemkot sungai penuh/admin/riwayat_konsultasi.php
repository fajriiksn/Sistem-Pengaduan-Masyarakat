<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php"); exit;
}

$nama = $_SESSION['nama'];

// Ambil antrian yang statusnya 'Selesai'
// Join dengan tabel users untuk dapat nama & nik
$query = mysqli_query($conn, "
    SELECT antrian.*, users.nama_lengkap, users.nik, users.no_telp 
    FROM antrian 
    JOIN users ON antrian.id_user = users.id_user 
    WHERE antrian.status = 'Selesai' 
    ORDER BY antrian.waktu_daftar DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Konsultasi - Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }

        .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: #f8fafc; color: #64748b; font-size: 0.85rem; text-transform: uppercase; }
        
        .btn-detail { background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; }
        .btn-detail:hover { background: #2563eb; }

        /* STYLE BADGE SIDEBAR */
        .sidebar-menu .nav-link {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        .badge-notif {
            background-color: #ef4444; color: white;
            font-size: 0.75rem; font-weight: 700;
            padding: 2px 8px; border-radius: 50px;
            min-width: 20px; text-align: center;
            animation: pulse-red 2s infinite;
            display: none;
        }
        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
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
            
            <a href="riwayat_konsultasi.php" class="nav-link active">
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
            <div class="page-title">Arsip Konsultasi</div>
            <div class="user-profile">
                <span class="user-name"><?= $nama ?></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=2563eb&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl Sesi</th>
                            <th>Nama Warga</th>
                            <th>NIK</th>
                            <th>No. WA</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?= date('d M Y, H:i', strtotime($row['waktu_daftar'])) ?></td>
                            <td><strong><?= $row['nama_lengkap'] ?></strong></td>
                            <td><?= $row['nik'] ?></td>
                            <td><?= $row['no_telp'] ?></td>
                            <td>
                                <a href="detail_riwayat.php?id_user=<?= $row['id_user'] ?>" class="btn-detail">
                                    <i class="ri-file-text-line"></i> Lihat Chat
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
                data: { action: 'check_notif' }, 
                dataType: 'json',
                success: function(response) {
                    var total = parseInt(response.total);
                    var badge = $('#queue-badge');

                    if (total > 0) {
                        badge.text(total);
                        badge.fadeIn();
                    } else {
                        badge.fadeOut();
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