<?php
session_start();
include '../config/koneksi.php';

// Matikan error reporting agar JSON tidak rusak
error_reporting(0);

if (!isset($_SESSION['is_login'])) { echo json_encode(['status' => 'error']); exit; }

$id_user = $_SESSION['id_user'];
$role    = $_SESSION['role'];
$action  = isset($_POST['action']) ? $_POST['action'] : '';

// ==========================================
// 1. LOGIKA UNTUK NOTIFIKASI SIDEBAR (BARU)
// ==========================================
if ($action == 'check_notif') {
    // Hitung berapa orang yang statusnya 'Menunggu'
    $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM antrian WHERE status='Menunggu'");
    $res = mysqli_fetch_assoc($q);
    
    echo json_encode(['total' => $res['total']]);
    exit;
}

// ==========================================
// 2. LOGIKA WARGA
// ==========================================

// Warga Daftar Antrian
elseif ($action == 'daftar_antrian') {
    $cek = mysqli_query($conn, "SELECT * FROM antrian WHERE id_user='$id_user' AND status != 'Selesai'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO antrian (id_user, status) VALUES ('$id_user', 'Menunggu')");
    }
    echo json_encode(['status' => 'success']);
}

// Warga Cek Status (Looping AJAX)
elseif ($action == 'cek_status') {
    $q_me = mysqli_query($conn, "SELECT * FROM antrian WHERE id_user='$id_user' AND status != 'Selesai' ORDER BY id_antrian DESC LIMIT 1");
    $me   = mysqli_fetch_assoc($q_me);

    if (!$me) {
        echo json_encode(['state' => 'idle']); 
    } elseif ($me['status'] == 'Menunggu') {
        $id_antrian_saya = $me['id_antrian'];
        $q_count = mysqli_query($conn, "SELECT COUNT(*) as sisa FROM antrian WHERE status='Menunggu' AND id_antrian < '$id_antrian_saya'");
        $urutan  = mysqli_fetch_assoc($q_count)['sisa'];
        echo json_encode(['state' => 'waiting', 'posisi' => $urutan + 1]);
    } elseif ($me['status'] == 'Aktif') {
        echo json_encode(['state' => 'active']);
    }
}

// Warga Load Chat
elseif ($action == 'load_chat') {
    $q_chat = mysqli_query($conn, "
        SELECT p.*, u.nama_lengkap 
        FROM pesan p 
        JOIN users u ON p.id_pengirim = u.id_user 
        WHERE (p.id_pengirim='$id_user' OR p.id_penerima='$id_user') 
        ORDER BY p.waktu_kirim ASC
    ");
    
    $chat_html = '';
    while ($chat = mysqli_fetch_assoc($q_chat)) {
        $is_me = ($chat['id_pengirim'] == $id_user) ? 'me' : 'other';
        $chat_html .= '<div class="chat-bubble ' . $is_me . '">';
        $chat_html .= htmlspecialchars($chat['isi_pesan']);
        $chat_html .= '<span class="time">' . date('H:i', strtotime($chat['waktu_kirim'])) . '</span>';
        $chat_html .= '</div>';
    }
    echo $chat_html;
}

// Kirim Pesan (Umum)
elseif ($action == 'kirim_pesan') {
    $pesan = htmlspecialchars($_POST['pesan']);
    
    if ($role == 'warga') {
        $q_admin = mysqli_query($conn, "SELECT id_user FROM users WHERE role='admin' LIMIT 1");
        $target  = mysqli_fetch_assoc($q_admin)['id_user'];
        $sender  = $id_user;
    } else {
        $target = $_POST['id_tujuan']; 
        $sender = $id_user;
    }

    if ($pesan != '' && $target) {
        mysqli_query($conn, "INSERT INTO pesan (id_pengirim, id_penerima, isi_pesan) VALUES ('$sender', '$target', '$pesan')");
    }
    echo "ok";
}

// ==========================================
// 3. LOGIKA ADMIN
// ==========================================

// Admin: Get List Antrian
elseif ($action == 'admin_get_queue') {
    $q_wait = mysqli_query($conn, "SELECT a.*, u.nama_lengkap, u.nik FROM antrian a JOIN users u ON a.id_user = u.id_user WHERE a.status='Menunggu' ORDER BY a.waktu_daftar ASC");
    
    $q_active = mysqli_query($conn, "SELECT a.*, u.nama_lengkap, u.nik, u.id_user as id_warga FROM antrian a JOIN users u ON a.id_user = u.id_user WHERE a.status='Aktif' LIMIT 1");
    $active_data = mysqli_fetch_assoc($q_active);

    $list_html = '';
    if(mysqli_num_rows($q_wait) > 0){
        while($row = mysqli_fetch_assoc($q_wait)){
            $list_html .= '<div class="queue-item">
                <div class="q-info">
                    <strong>'.$row['nama_lengkap'].'</strong>
                    <small>NIK: '.$row['nik'].'</small>
                </div>
                <button onclick="terimaWarga('.$row['id_antrian'].')" class="btn-accept">Terima</button>
            </div>';
        }
    } else {
        $list_html = '<div style="text-align:center; padding:20px; color:#9ca3af;">Tidak ada antrian.</div>';
    }

    echo json_encode([
        'list' => $list_html,
        'active_session' => $active_data 
    ]);
}

// Admin: Terima Warga
elseif ($action == 'admin_accept') {
    $id_antrian = $_POST['id_antrian'];
    mysqli_query($conn, "UPDATE antrian SET status='Aktif' WHERE id_antrian='$id_antrian'");
    echo "ok";
}

// Admin: Selesaikan Sesi
elseif ($action == 'admin_close') {
    $id_antrian = $_POST['id_antrian'];
    mysqli_query($conn, "UPDATE antrian SET status='Selesai' WHERE id_antrian='$id_antrian'");
    echo "ok";
}

// Admin: Load Chat Warga Aktif
elseif ($action == 'admin_load_chat') {
    $id_warga = $_POST['id_warga'];
    
    $q_chat = mysqli_query($conn, "
        SELECT p.* FROM pesan p 
        WHERE (p.id_pengirim='$id_warga' AND p.id_penerima='$id_user') 
           OR (p.id_pengirim='$id_user' AND p.id_penerima='$id_warga') 
        ORDER BY p.waktu_kirim ASC
    ");

    $chat_html = '';
    while ($chat = mysqli_fetch_assoc($q_chat)) {
        $is_me = ($chat['id_pengirim'] == $id_user) ? 'me' : 'other'; 
        $chat_html .= '<div class="chat-bubble ' . $is_me . '">';
        $chat_html .= htmlspecialchars($chat['isi_pesan']);
        $chat_html .= '<span class="time">' . date('H:i', strtotime($chat['waktu_kirim'])) . '</span>';
        $chat_html .= '</div>';
    }
    echo $chat_html;
}
?>