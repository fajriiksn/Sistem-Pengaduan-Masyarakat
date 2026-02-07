<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'warga') {
    header("location: ../login.php"); exit;
}
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Konsultasi Live - SIPEM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        
        /* Desktop Layout */
        .chat-wrapper {
            max-width: 900px; margin: 0 auto;
            height: calc(100vh - 140px);
            display: flex; flex-direction: column;
        }

        /* State Box (Tampilan Idle/Waiting) */
        .state-box {
            background: white; border-radius: 16px; padding: 40px;
            text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin: auto; max-width: 500px; width: 100%;
            display: none;
        }
        .state-icon { font-size: 4rem; color: #059669; margin-bottom: 20px; }
        .queue-number { font-size: 3.5rem; font-weight: 800; color: #059669; margin: 15px 0; }

        /* Chat Room Style */
        .chat-card {
            background: #e5ddd5; /* Warna background WA */
            border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1);
            overflow: hidden; display: flex; flex-direction: column;
            height: 100%; display: none;
        }
        
        .chat-header {
            background: #075e54; color: white; padding: 15px;
            display: flex; align-items: center; gap: 15px; flex-shrink: 0;
        }
        .admin-avatar {
            width: 40px; height: 40px; background: white; color: #075e54;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }

        .chat-body {
            flex: 1; padding: 20px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 10px;
        }

        /* Bubble Chat Logic */
        .message-row { display: flex; width: 100%; }
        
        /* Admin (Kiri - Putih) */
        .message-row.other { justify-content: flex-start; }
        .message-row.other .bubble {
            background: white; color: #1f2937;
            border-top-left-radius: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* Saya (Kanan - Hijau Muda) */
        .message-row.me { justify-content: flex-end; }
        .message-row.me .bubble {
            background: #dcf8c6; color: #111827;
            border-top-right-radius: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .bubble {
            max-width: 75%; padding: 8px 12px; border-radius: 8px;
            position: relative; font-size: 0.95rem; line-height: 1.4;
        }
        .time { font-size: 0.7rem; display: block; opacity: 0.6; text-align: right; margin-top: 4px; }

        .chat-footer {
            padding: 10px; background: #f0f0f0; display: flex; gap: 10px; align-items: center; flex-shrink: 0;
        }
        .chat-input {
            flex: 1; padding: 12px 20px; border-radius: 50px; border: none;
            background: white; outline: none;
        }
        .btn-send-chat {
            background: #075e54; color: white; border: none;
            width: 45px; height: 45px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }

        /* ================= MOBILE RESPONSIVE FIX ================= */
        .bottom-nav { display: none; }

        @media (max-width: 768px) {
            .sidebar, .top-header { display: none; }
            
            /* Reset Main Content untuk Mobile Chat */
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100%;
                height: 100vh; /* Fallback */
                height: 100dvh; /* Modern Browser */
                background: #e5ddd5;
                overflow: hidden; /* Matikan scroll body */
            }

            /* Container Chat dikunci (Fixed Position) */
            .chat-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 70px; /* Sesuai tinggi Nav Bawah */
                height: auto !important;
                max-width: none;
                z-index: 10;
            }

            .chat-card {
                border-radius: 0;
                height: 100%;
            }

            /* State Box (Tampilan Awal) di Tengah Layar */
            .state-box {
                position: absolute;
                top: 50%; left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
            }

            /* Bottom Nav Tetap Paling Atas */
            .bottom-nav {
                display: flex; position: fixed; bottom: 0; left: 0; right: 0;
                background: white; height: 70px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
                justify-content: space-around; align-items: center; z-index: 999;
                border-top: 1px solid #f3f4f6;
            }
            .nav-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #9ca3af; font-size: 0.75rem; }
            .nav-item i { font-size: 1.5rem; margin-bottom: 4px; }
            .nav-item.active { color: #059669; font-weight: 600; }
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
            <a href="riwayat.php" class="nav-link"><i class="ri-history-line"></i> Riwayat Laporan</a>
            <a href="konsultasi.php" class="nav-link active"><i class="ri-message-2-line"></i> Konsultasi Admin</a>
        </nav>
        <div class="sidebar-footer"><a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a></div>
    </aside>

    <main class="main-content">
        
        <header class="top-header">
            <div class="page-title">Layanan Konsultasi</div>
            <div class="user-profile">
                <span class="user-name"><?= $nama ?></span>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&background=059669&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            <div class="chat-wrapper">

                <div id="view-idle" class="state-box">
                    <div class="state-icon"><i class="ri-customer-service-2-fill"></i></div>
                    <h2>Butuh Bantuan?</h2>
                    <p style="color: #666; margin-bottom: 20px;">Hubungkan diri Anda dengan Admin Pelayanan kami.</p>
                    <button onclick="daftarAntrian()" style="background: #059669; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                        Ambil Antrian
                    </button>
                </div>

                <div id="view-waiting" class="state-box">
                    <div class="state-icon"><i class="ri-time-line"></i></div>
                    <h3>Mohon Tunggu</h3>
                    <p>Anda sedang dalam antrian.</p>
                    <div style="background: #ecfdf5; padding: 15px; border-radius: 10px; margin: 15px 0;">
                        <span style="color: #047857;">Posisi Antrian Anda</span>
                        <div id="nomor-antrian" class="queue-number">-</div>
                    </div>
                </div>

                <div id="view-active" class="chat-card">
                    <div class="chat-header">
                        <div class="admin-avatar"><i class="ri-user-star-fill"></i></div>
                        <div>
                            <h4 style="margin: 0; font-size: 1rem;">Admin Pelayanan</h4>
                            <small style="opacity: 0.8;">Online</small>
                        </div>
                    </div>

                    <div class="chat-body" id="chat-box">
                        </div>

                    <div class="chat-footer">
                        <input type="text" id="input-pesan" class="chat-input" placeholder="Ketik pesan..." autocomplete="off">
                        <button onclick="kirimPesan()" id="btn-kirim" class="btn-send-chat">
                            <i class="ri-send-plane-fill"></i>
                        </button>
                    </div>
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
        <a href="riwayat.php" class="nav-item">
            <i class="ri-history-line"></i>
            <span>Riwayat</span>
        </a>
        <a href="konsultasi.php" class="nav-item active">
            <i class="ri-message-2-fill"></i>
            <span>Chat</span>
        </a>
        <a href="../logout.php" class="nav-item">
            <i class="ri-logout-box-r-line"></i>
            <span>Keluar</span>
        </a>
    </nav>

    <script>
        $(document).ready(function() {
            cekStatusAntrian();
            setInterval(function() { cekStatusAntrian(); }, 2000);
        });

        var currentState = '';
        var isScrolledToBottom = true;

        $('#chat-box').on('scroll', function(){
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 10) {
                isScrolledToBottom = true;
            } else {
                isScrolledToBottom = false;
            }
        });

        function cekStatusAntrian() {
            $.ajax({
                url: '../actions/ajax_konsultasi.php', type: 'POST',
                data: { action: 'cek_status' }, dataType: 'json',
                success: function(response) {
                    if(response.state == 'idle') tampilkanView('idle');
                    else if(response.state == 'waiting') {
                        tampilkanView('waiting');
                        $('#nomor-antrian').text(response.posisi);
                    } 
                    else if(response.state == 'active') {
                        tampilkanView('active');
                        loadChat();
                    }
                }
            });
        }

        function tampilkanView(state) {
            if(currentState !== state) {
                $('.state-box, .chat-card').hide();
                if(state == 'idle') $('#view-idle').fadeIn();
                if(state == 'waiting') $('#view-waiting').fadeIn();
                if(state == 'active') $('#view-active').css('display', 'flex').hide().fadeIn();
                currentState = state;
            }
        }

        function daftarAntrian() {
            $.post('../actions/ajax_konsultasi.php', { action: 'daftar_antrian' }, function() { cekStatusAntrian(); });
        }

        function loadChat() {
            $.post('../actions/ajax_konsultasi.php', { action: 'load_chat' }, function(html) {
                var tempDiv = $('<div>').html(html);
                var newHtml = '';
                tempDiv.find('.chat-bubble').each(function() {
                    var content = $(this).html();
                    var isMe = $(this).hasClass('me') ? 'me' : 'other';
                    newHtml += `<div class="message-row ${isMe}"><div class="bubble">${content}</div></div>`;
                });

                var chatBox = $('#chat-box');
                if(chatBox.html().length !== newHtml.length) {
                     chatBox.html(newHtml);
                     if(isScrolledToBottom) chatBox.scrollTop(chatBox[0].scrollHeight);
                }
            });
        }

        function kirimPesan() {
            var input = $('#input-pesan');
            var pesan = input.val().trim();
            if(pesan == '') return;
            $.post('../actions/ajax_konsultasi.php', { action: 'kirim_pesan', pesan: pesan }, function() {
                input.val(''); loadChat(); isScrolledToBottom = true;
            });
        }

        $('#input-pesan').keypress(function(e) { if(e.which == 13) kirimPesan(); });
    </script>
</body>
</html>