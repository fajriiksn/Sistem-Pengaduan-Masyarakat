<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['is_login'])) {
    header("location: ../login.php");
    exit;
}

if (isset($_POST['kirim'])) {
    // 1. Ambil Data Form
    $id_user     = $_SESSION['id_user'];
    $judul       = htmlspecialchars($_POST['judul']);
    $id_kategori = $_POST['kategori'];
    $isi         = htmlspecialchars($_POST['isi']);
    $lokasi_nama = htmlspecialchars($_POST['lokasi_nama']);
    
    // Ambil Koordinat (Default jika kosong/tidak digeser)
    $lat         = !empty($_POST['latitude']) ? $_POST['latitude'] : '-2.0673';
    $long        = !empty($_POST['longitude']) ? $_POST['longitude'] : '101.3899';

    // 2. Proses Upload Foto
    $foto_nama  = $_FILES['foto']['name'];
    $foto_tmp   = $_FILES['foto']['tmp_name'];
    $foto_error = $_FILES['foto']['error'];

    // Cek apakah ada file yang diupload
    if ($foto_error === 4) {
        echo "<script>alert('Harap upload foto bukti!'); window.history.back();</script>";
        exit;
    }

    // Validasi Ekstensi Gambar
    $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensiFoto  = explode('.', $foto_nama);
    $ekstensiFoto  = strtolower(end($ekstensiFoto));

    if (!in_array($ekstensiFoto, $ekstensiValid)) {
        echo "<script>alert('Format file tidak valid! Harap upload JPG/PNG.'); window.history.back();</script>";
        exit;
    }

    // Generate Nama File Unik (Agar tidak bentrok)
    // Contoh hasil: 65a4bc1_bukti.jpg
    $namaFileBaru = uniqid() . '.' . $ekstensiFoto;
    
    // Folder Tujuan
    $target_dir = "../uploads/bukti_laporan/";
    
    // Buat folder jika belum ada (Fitur Keamanan)
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Pindahkan File
    if (move_uploaded_file($foto_tmp, $target_dir . $namaFileBaru)) {
        
        // 3. Simpan ke Database
        $query = "INSERT INTO laporan (id_user, id_kategori, judul_laporan, isi_laporan, lokasi_nama, latitude, longitude, foto_laporan, status) 
                  VALUES ('$id_user', '$id_kategori', '$judul', '$isi', '$lokasi_nama', '$lat', '$long', '$namaFileBaru', 'Menunggu')";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Laporan Berhasil Dikirim! Admin akan segera memverifikasi.');
                    // Arahkan ke riwayat (nanti kita buat file ini)
                    window.location.href='../warga/riwayat.php';
                  </script>";
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }

    } else {
        echo "<script>alert('Gagal mengupload foto. Coba lagi.'); window.history.back();</script>";
    }
}
?>