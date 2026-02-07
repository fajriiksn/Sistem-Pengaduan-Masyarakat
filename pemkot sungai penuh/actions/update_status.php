<?php
session_start();
include '../config/koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("location: ../login.php"); exit;
}

if (isset($_POST['update'])) {
    $id_laporan = $_POST['id_laporan'];
    $status     = $_POST['status'];
    $tanggapan  = htmlspecialchars($_POST['tanggapan']);
    
    // Variabel Query Update Dasar
    $query = "UPDATE laporan SET status='$status', tanggapan_admin='$tanggapan' ";

    // Jika Status Selesai, kita set tgl_selesai = NOW()
    if ($status == 'Selesai') {
        $query .= ", tgl_selesai=NOW() ";
        
        // Cek Upload Foto Bukti Selesai
        if (!empty($_FILES['foto_tindak_lanjut']['name'])) {
            $foto_nama = $_FILES['foto_tindak_lanjut']['name'];
            $foto_tmp  = $_FILES['foto_tindak_lanjut']['tmp_name'];
            
            $ekstensi = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));
            $valid    = ['jpg', 'jpeg', 'png', 'webp'];

            if(in_array($ekstensi, $valid)) {
                $namaBaru = uniqid() . '_selesai.' . $ekstensi;
                $target   = "../uploads/bukti_laporan/" . $namaBaru;
                
                if(move_uploaded_file($foto_tmp, $target)){
                    // Tambahkan update kolom foto
                    $query .= ", foto_tindak_lanjut='$namaBaru' ";
                }
            }
        }
    }

    // Eksekusi Query
    $query .= " WHERE id_laporan='$id_laporan'";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Status Laporan Berhasil Diperbarui!');
                window.location.href='../admin/detail_laporan.php?id=$id_laporan';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>