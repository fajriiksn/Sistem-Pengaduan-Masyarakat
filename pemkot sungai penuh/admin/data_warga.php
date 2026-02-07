<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php"); exit;
}

$nama = $_SESSION['nama'];

// Query Ambil Semua Data Warga
$query = mysqli_query($conn, "SELECT * FROM users WHERE role='warga' ORDER BY id_user DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Warga - Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }

        .table-container { background: white; padding: 20px; border-radius: 12px; box-shadow: var(--shadow); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: #f8fafc; font-size: 0.85rem; text-transform: uppercase; color: #64748b; }
        
        .btn-action { padding: 6px 10px; border-radius: 6px; font-size: 0.8rem; border: none; cursor: pointer; color: white; margin-right: 5px; text-decoration: none; display: inline-block;}
        .btn-edit { background: #f59e0b; }
        .btn-reset { background: #3b82f6; }
        .btn-del { background: #ef4444; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 25px; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .btn-save { width: 100%; padding: 12px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 10px; }

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
            
            <a href="riwayat_konsultasi.php" class="nav-link">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-history-line"></i> Riwayat Chat
                </div>
            </a>
            
            <a href="data_warga.php" class="nav-link active">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="ri-group-line"></i> Data Warga
                </div>
            </a>
        </nav>
        <div class="sidebar-footer"><a href="../logout.php" class="btn-logout"><i class="ri-logout-box-line"></i> Keluar</a></div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="page-title">Manajemen Warga</div>
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
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>No. WhatsApp</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nik'] ?></td>
                            <td><strong><?= $row['nama_lengkap'] ?></strong></td>
                            <td><?= $row['no_telp'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['created_at'] ?? 'now')) ?></td>
                            <td>
                                <button class="btn-action btn-edit" 
                                    onclick="openEditModal('<?= $row['id_user'] ?>', '<?= $row['nik'] ?>', '<?= $row['nama_lengkap'] ?>', '<?= $row['no_telp'] ?>')">
                                    <i class="ri-pencil-line"></i> Edit
                                </button>
                                
                                <a href="../actions/warga_handler.php?act=reset&id=<?= $row['id_user'] ?>" 
                                   class="btn-action btn-reset"
                                   onclick="return confirm('Yakin reset password warga ini menjadi 123456?')">
                                   <i class="ri-key-2-line"></i> Reset
                                </a>

                                <a href="../actions/warga_handler.php?act=hapus&id=<?= $row['id_user'] ?>" 
                                   class="btn-action btn-del"
                                   onclick="return confirm('Hapus data warga ini? Data laporan mereka juga akan terhapus!')">
                                   <i class="ri-delete-bin-line"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3 style="margin-bottom: 20px;">Edit Data Warga</h3>
            
            <form action="../actions/warga_handler.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_user" id="edit_id">
                
                <div class="form-group">
                    <label>NIK</label>
                    <input type="number" name="nik" id="edit_nik" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>No. WhatsApp</label>
                    <input type="number" name="telp" id="edit_telp" class="form-control" required>
                </div>

                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </form>
        </div>
    </div>

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

        // Fungsi Buka Modal & Isi Data
        function openEditModal(id, nik, nama, telp) {
            document.getElementById('editModal').style.display = "block";
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nik').value = nik;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_telp').value = telp;
        }

        // Fungsi Tutup Modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = "none";
        }

        // Tutup jika klik di luar modal
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>