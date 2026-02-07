<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php"); exit;
}

$id_warga = $_GET['id_user'];
$id_admin = $_SESSION['id_user'];

// 1. Ambil Data Profil Warga
$q_user = mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_warga'");
$user   = mysqli_fetch_assoc($q_user);

// 2. Ambil Riwayat Chat Lengkap (Antara Warga Ini & Admin)
$q_chat = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap, u.role
    FROM pesan p 
    JOIN users u ON p.id_pengirim = u.id_user 
    WHERE (p.id_pengirim='$id_warga' OR p.id_penerima='$id_warga') 
    ORDER BY p.waktu_kirim ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transkrip - Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .layout-grid { display: grid; grid-template-columns: 350px 1fr; gap: 25px; align-items: start; }
        
        /* Kartu Profil */
        .profile-card { background: white; padding: 30px; border-radius: 12px; box-shadow: var(--shadow); text-align: center; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #eff6ff; margin-bottom: 15px; }
        .data-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .data-label { color: #64748b; font-weight: 500; }
        .data-val { font-weight: 600; color: #1e293b; }

        /* Area Chat */
        .chat-history { background: white; border-radius: 12px; box-shadow: var(--shadow); overflow: hidden; }
        .chat-header { background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155; }
        .chat-content { padding: 25px; background: #f1f5f9; max-height: 600px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
        
        .chat-bubble { max-width: 75%; padding: 12px 18px; border-radius: 12px; font-size: 0.95rem; line-height: 1.5; position: relative; }
        
        /* Chat Warga (Kiri - Putih) */
        .warga-msg { align-self: flex-start; background: white; border: 1px solid #e2e8f0; color: #1e293b; border-bottom-left-radius: 2px; }
        
        /* Chat Admin (Kanan - Biru) */
        .admin-msg { align-self: flex-end; background: #2563eb; color: white; border-bottom-right-radius: 2px; }
        
        .time-stamp { font-size: 0.7rem; display: block; margin-top: 5px; opacity: 0.7; text-align: right; }
        .role-badge { font-size: 0.7rem; font-weight: 700; display: block; margin-bottom: 3px; opacity: 0.8; }
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
            <a href="dashboard.php" class="nav-link"><i class="ri-dashboard-3-line"></i> Dashboard</a>
            <a href="laporan.php" class="nav-link"><i class="ri-file-list-3-line"></i> Laporan Masuk</a>
            <a href="antrian_konsultasi.php" class="nav-link"><i class="ri-customer-service-2-line"></i> Konsultasi Live</a>
            
            <a href="riwayat_konsultasi.php" class="nav-link active"><i class="ri-history-line"></i> Riwayat Chat</a>
            
            <a href="data_warga.php" class="nav-link"><i class="ri-group-line"></i> Data Warga</a>
        </nav>
        <div class="sidebar-footer"><a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a></div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="page-title">
                <a href="riwayat_konsultasi.php" style="color: #64748b; margin-right: 10px;"><i class="ri-arrow-left-line"></i></a>
                Detail Transkrip
            </div>
            <div class="user-profile">
                <span class="user-name"><?= $_SESSION['nama'] ?></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=2563eb&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            <div class="layout-grid">
                
                <div class="profile-card">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['nama_lengkap']) ?>&background=059669&color=fff&size=200" class="profile-img">
                    <h3 style="margin-bottom: 5px;"><?= $user['nama_lengkap'] ?></h3>
                    <span style="font-size: 0.9rem; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 50px;">Warga</span>
                    
                    <div style="margin-top: 25px; text-align: left;">
                        <h4 style="margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Data Pribadi</h4>
                        
                        <div class="data-row">
                            <span class="data-label">NIK</span>
                            <span class="data-val"><?= $user['nik'] ?></span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">No. WhatsApp</span>
                            <span class="data-val"><?= $user['no_telp'] ?></span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">Terdaftar Sejak</span>
                            <span class="data-val"><?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></span>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <a href="https://wa.me/62<?= substr($user['no_telp'], 1) ?>" target="_blank" style="display: block; width: 100%; padding: 10px; background: #25d366; color: white; text-align: center; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                <i class="ri-whatsapp-line"></i> Hubungi via WA
                            </a>
                        </div>
                    </div>
                </div>

                <div class="chat-history">
                    <div class="chat-header">
                        <i class="ri-chat-history-line"></i> Transkrip Percakapan
                    </div>
                    <div class="chat-content">
                        <?php 
                        if(mysqli_num_rows($q_chat) > 0) {
                            while($msg = mysqli_fetch_assoc($q_chat)) { 
                                $is_admin = ($msg['role'] == 'admin');
                                $bubble_class = $is_admin ? 'admin-msg' : 'warga-msg';
                                $sender_name = $is_admin ? 'Admin' : $msg['nama_lengkap'];
                        ?>
                            <div class="chat-bubble <?= $bubble_class ?>">
                                <span class="role-badge"><?= $sender_name ?></span>
                                <?= nl2br(htmlspecialchars($msg['isi_pesan'])) ?>
                                <span class="time-stamp">
                                    <?= date('d M Y, H:i', strtotime($msg['waktu_kirim'])) ?>
                                </span>
                            </div>
                        <?php 
                            }
                        } else {
                            echo "<p style='text-align:center; color:#94a3b8; margin-top:50px;'>Belum ada riwayat percakapan.</p>";
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>