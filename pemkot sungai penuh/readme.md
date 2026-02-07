# 📢 SIPEM - Sistem Pengaduan Masyarakat

**SIPEM** adalah aplikasi berbasis web yang dibangun menggunakan **PHP Native** untuk memfasilitasi masyarakat dalam menyampaikan pengaduan atau aspirasi kepada pemerintah/instansi terkait. Aplikasi ini dirancang dengan antarmuka modern yang **Mobile Responsive** (seperti aplikasi native) dan dilengkapi fitur **Real-time Chat**.

## 🌟 Fitur Unggulan

### 📱 Sisi Warga (Mobile Optimized)

- **Tampilan Mobile App-like:** Navigasi bar di bawah (Bottom Navigation) saat dibuka di HP.
- **Laporan Berbasis Lokasi:** Integrasi **LeafletJS** untuk menandai titik lokasi kejadian secara akurat di peta.
- **Real-time Live Chat:** Konsultasi langsung dengan Admin layaknya WhatsApp (Bubble chat, status mengetik, antrian).
- **Riwayat Laporan:** Pantau status laporan (Menunggu, Proses, Selesai, Ditolak).

### 💻 Sisi Admin (Web Dashboard)

- **Dashboard Statistik:** Ringkasan jumlah laporan masuk, diproses, dan selesai.
- **Manajemen Antrian Chat:** Notifikasi badge real-time saat ada warga yang meminta konsultasi.
- **Verifikasi & Tindak Lanjut:** Ubah status laporan dan upload bukti penanganan.
- **Manajemen Data Warga:** Kelola akun pengguna.

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP Native (No Framework)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3 (Custom Dashboard), JavaScript (jQuery)
- **Maps API:** LeafletJS (OpenStreetMap)
- **Icons:** RemixIcon

## 📂 Struktur Folder

```text
/
├── actions/        # Logika pemrosesan data (CRUD & AJAX)
├── admin/          # Halaman dashboard admin
├── assets/         # CSS, Images, dan Library
├── config/         # Koneksi database
├── uploads/        # Penyimpanan file bukti laporan
├── warga/          # Halaman dashboard warga
├── index.php       # Landing page
└── login.php       # Halaman login

- Database Hubungi : fajriiksan5@gmail.com
```
