<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php"); exit;
}
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat Center - Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* --- STYLE MODERN ADMIN CHAT --- */
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        
        .chat-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            height: calc(100vh - 120px);
            align-items: start;
        }

        /* 1. PANEL ANTRIAN (KIRI) */
        .queue-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .queue-header {
            padding: 20px;
            background: #ffffff;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .queue-title { font-weight: 700; color: #111827; font-size: 1rem; }
        
        .queue-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: #f9fafb;
        }
        
        /* Item Antrian */
        .queue-item {
            background: white;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
            transition: 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .queue-item:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .q-info strong { display: block; color: #1f2937; font-size: 0.95rem; }
        .q-info small { color: #6b7280; font-size: 0.8rem; }
        
        .btn-accept {
            background: #dbeafe; color: #2563eb;
            border: none; padding: 8px 14px;
            border-radius: 8px; font-size: 0.8rem; font-weight: 600;
            cursor: pointer; transition: 0.2s;
        }
        .btn-accept:hover { background: #2563eb; color: white; }


        /* 2. PANEL CHAT (KANAN) */
        .chat-room {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            position: relative;
        }

        /* Empty State */
        .empty-state {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            height: 100%; color: #9ca3af; text-align: center;
            padding: 20px;
        }
        .empty-icon { font-size: 4rem; opacity: 0.5; margin-bottom: 15px; color: #cbd5e1; }

        /* Header Chat Aktif */
        .active-chat-header {
            padding: 15px 25px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }
        .user-meta { display: flex; align-items: center; gap: 12px; }
        .avatar-circle {
            width: 40px; height: 40px;
            background: #3b82f6; color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 1.1rem;
        }
        .user-details h4 { margin: 0; font-size: 1rem; color: #111827; }
        .user-details span { font-size: 0.75rem; color: #10b981; font-weight: 500; }

        .btn-end-session {
            background: #fee2e2; color: #dc2626;
            border: none; padding: 8px 16px;
            border-radius: 8px; font-weight: 600; font-size: 0.85rem;
            cursor: pointer; transition: 0.2s;
            display: flex; align-items: center; gap: 6px;
        }
        .btn-end-session:hover { background: #dc2626; color: white; }

        /* Body Chat */
        .chat-body {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Bubble Styles */
        .message-row { display: flex; width: 100%; }
        
        /* Pesan Warga (Kiri) */
        .message-row.other { justify-content: flex-start; }
        .message-row.other .bubble {
            background: #ffffff;
            color: #1f2937;
            border-top-left-radius: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }
        
        /* Pesan Admin/Saya (Kanan) */
        .message-row.me { justify-content: flex-end; }
        .message-row.me .bubble {
            background: #3b82f6; /* Warna Biru Admin */
            color: white;
            border-top-right-radius: 0;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        .bubble {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 12px;
            position: relative;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .time {
            font-size: 0.7rem; margin-top: 5px;
            display: block; opacity: 0.8; text-align: right;
        }

        /* Footer Input */
        .chat-footer {
            padding: 15px 25px;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex; gap: 12px; align-items: center;
        }
        .chat-input {
            flex: 1;
            padding: 12px 20px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.95rem;
            outline: none; transition: 0.3s;
        }
        .chat-input:focus { border-color: #3b82f6; background: white; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        
        .btn-send {
            width: 45px; height: 45px;
            border-radius: 50%; border: none;
            background: #3b82f6; color: white;
            font-size: 1.2rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
        }
        .btn-send:hover { transform: scale(1.05); background: #2563eb; }

        /* Scrollbar */
        .chat-body::-webkit-scrollbar { width: 6px; }
        .chat-body::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        
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

        /* Responsive */
        @media (max-width: 900px) {
            .chat-layout { grid-template-columns: 1fr; grid-template-rows: 250px 1fr; }
            .queue-card { height: 100%; }
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
            
            <a href="antrian_konsultasi.php" class="nav-link active">
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
            <div class="page-title">Live Chat Center</div>
            <div class="user-profile">
                <span class="user-name">Admin</span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=2563eb&color=fff" class="user-avatar">
            </div>
        </header>

        <div class="content-wrapper">
            <div class="chat-layout">
                
                <div class="queue-card">
                    <div class="queue-header">
                        <span class="queue-title"><i class="ri-list-check"></i> Antrian Masuk</span>
                        <div id="queue-loader" style="display:none; color: #3b82f6;"><i class="ri-loader-4-line ri-spin"></i></div>
                    </div>
                    <div class="queue-list" id="queue-container">
                        </div>
                </div>

                <div class="chat-room">
                    
                    <div id="chat-empty" class="empty-state">
                        <i class="ri-chat-smile-3-line empty-icon"></i>
                        <h3 style="color: #374151; margin-bottom: 5px;">Belum Ada Sesi Aktif</h3>
                        <p>Silakan pilih warga dari daftar antrian di sebelah kiri<br>untuk memulai percakapan.</p>
                    </div>

                    <div id="chat-active" style="display: none; flex-direction: column; height: 100%;">
                        
                        <div class="active-chat-header">
                            <div class="user-meta">
                                <div class="avatar-circle"><i class="ri-user-line"></i></div>
                                <div class="user-details">
                                    <h4 id="active-warga-name">Nama Warga</h4>
                                    <span><i class="ri-record-circle-line"></i> Sedang Konsultasi</span>
                                </div>
                            </div>
                            <button onclick="akhiriSesi()" class="btn-end-session">
                                <i class="ri-close-circle-line"></i> Akhiri Sesi
                            </button>
                        </div>

                        <div class="chat-body" id="chat-box">
                            </div>

                        <div class="chat-footer">
                            <input type="text" id="input-pesan" class="chat-input" placeholder="Ketik balasan..." autocomplete="off">
                            <button onclick="kirimPesan()" class="btn-send"><i class="ri-send-plane-fill"></i></button>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </main>

    <script>
        var activeAntrianId = null;
        var activeWargaId = null;
        var isScrolledToBottom = true;

        $(document).ready(function() {
            // Load data pertama kali
            refreshData();
            checkQueueNotification(); // Cek badge juga
            
            // Auto Refresh setiap 3 detik
            setInterval(function() {
                refreshData();
                checkQueueNotification();
                if(activeWargaId) loadChat(); 
            }, 3000);

            // Deteksi Scroll
            $('#chat-box').on('scroll', function(){
                if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 10) {
                    isScrolledToBottom = true;
                } else {
                    isScrolledToBottom = false;
                }
            });
        });

        // --- FUNGSI BADGE SIDEBAR ---
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
                }
            });
        }

        // --- FUNGSI CHAT & ANTRIAN ---
        function refreshData() {
            $('#queue-loader').show();
            $.ajax({
                url: '../actions/ajax_konsultasi.php',
                type: 'POST',
                data: { action: 'admin_get_queue' },
                dataType: 'json',
                success: function(res) {
                    $('#queue-loader').hide();
                    
                    // Update List Antrian
                    $('#queue-container').html(res.list);

                    // Update Status Sesi Aktif
                    if(res.active_session) {
                        // Jika ada sesi aktif, set variable global
                        activeAntrianId = res.active_session.id_antrian;
                        activeWargaId   = res.active_session.id_warga;
                        
                        $('#chat-empty').hide();
                        $('#chat-active').css('display', 'flex');
                        $('#active-warga-name').text(res.active_session.nama_lengkap);
                    } else {
                        // Jika tidak ada sesi aktif, reset variable global
                        activeAntrianId = null;
                        activeWargaId = null;
                        $('#chat-active').hide();
                        $('#chat-empty').css('display', 'flex');
                    }
                }
            });
        }

        function terimaWarga(idAntrian) {
            $.post('../actions/ajax_konsultasi.php', { 
                action: 'admin_accept', 
                id_antrian: idAntrian 
            }, function() {
                refreshData(); // Refresh UI langsung
            });
        }

        function akhiriSesi() {
            if(confirm('Yakin ingin mengakhiri sesi konsultasi ini?')) {
                $.post('../actions/ajax_konsultasi.php', { 
                    action: 'admin_close', 
                    id_antrian: activeAntrianId 
                }, function() {
                    refreshData(); // UI akan kembali ke empty state
                });
            }
        }

        function loadChat() {
            if(!activeWargaId) return;
            $.post('../actions/ajax_konsultasi.php', { 
                action: 'admin_load_chat', 
                id_warga: activeWargaId 
            }, function(html) {
                // Parsing HTML agar sesuai Bubble Style Baru
                var tempDiv = $('<div>').html(html);
                var newHtml = '';

                tempDiv.find('.chat-bubble').each(function() {
                    var content = $(this).html();
                    var isMe = $(this).hasClass('me') ? 'me' : 'other';
                    
                    newHtml += `<div class="message-row ${isMe}">
                                    <div class="bubble">
                                        ${content}
                                    </div>
                                </div>`;
                });

                var chatBox = $('#chat-box');
                // Update hanya jika ada pesan baru
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

            $.post('../actions/ajax_konsultasi.php', { 
                action: 'kirim_pesan', 
                pesan: pesan,
                id_tujuan: activeWargaId 
            }, function() {
                input.val('');
                loadChat();
                isScrolledToBottom = true;
            });
        }

        // Enter untuk kirim
        $('#input-pesan').keypress(function(e) {
            if(e.which == 13) kirimPesan();
        });
    </script>

</body>
</html>