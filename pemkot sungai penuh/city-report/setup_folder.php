<?php
// File ini berfungsi untuk membuat folder upload secara otomatis
// Jalankan file ini satu kali di browser: localhost/city-report/setup_folder.php

echo "<h2>⚙️ Setup Folder Penyimpanan...</h2>";

// Daftar folder yang akan dibuat
$folders = [
    'uploads',
    'uploads/bukti_laporan'
];

foreach ($folders as $folder) {
    // Cek apakah folder sudah ada?
    if (!file_exists($folder)) {
        // Jika belum, buat folder dengan izin akses penuh (0777)
        if (mkdir($folder, 0777, true)) {
            echo "<p style='color: green;'>✅ Folder <b>$folder</b> berhasil dibuat.</p>";
        } else {
            echo "<p style='color: red;'>❌ Gagal membuat folder <b>$folder</b>. Cek permission server.</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Folder <b>$folder</b> sudah ada (Aman).</p>";
    }
}

// Buat file index.php kosong di dalam uploads agar aman (mencegah listing directory)
$secure_file = 'uploads/index.php';
if (!file_exists($secure_file)) {
    $content = "<?php // Silence is golden ?>";
    file_put_contents($secure_file, $content);
    echo "<p style='color: green;'>✅ Keamanan folder (index.php) berhasil dibuat.</p>";
}

echo "<hr>";
echo "<h3>🎉 Selesai! Folder 'bukti_laporan' siap digunakan.</h3>";
echo "<a href='index.php'>Kembali ke Website</a>";
?>