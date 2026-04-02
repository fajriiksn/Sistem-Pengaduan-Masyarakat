-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2026 at 10:29 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_city_report`
--

-- --------------------------------------------------------

--
-- Table structure for table `antrian`
--

CREATE TABLE `antrian` (
  `id_antrian` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `waktu_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Menunggu','Aktif','Selesai') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `antrian`
--

INSERT INTO `antrian` (`id_antrian`, `id_user`, `waktu_daftar`, `status`) VALUES
(1, 2, '2026-02-06 20:37:16', 'Selesai'),
(2, 2, '2026-02-06 21:01:30', 'Selesai'),
(3, 2, '2026-02-07 04:05:59', 'Selesai'),
(4, 2, '2026-02-07 04:19:50', 'Selesai'),
(5, 2, '2026-02-07 07:04:58', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Infrastruktur (Jalan/Jembatan)'),
(2, 'Tata Kota (Taman/Lampu Jalan)'),
(3, 'Kebersihan & Sampah'),
(4, 'Saluran Air / Banjir');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `judul_laporan` varchar(100) NOT NULL,
  `isi_laporan` text NOT NULL,
  `tgl_laporan` timestamp NOT NULL DEFAULT current_timestamp(),
  `lokasi_nama` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `foto_laporan` varchar(255) NOT NULL,
  `status` enum('Menunggu','Proses','Selesai','Ditolak') DEFAULT 'Menunggu',
  `tgl_selesai` timestamp NULL DEFAULT NULL,
  `tanggapan_admin` text DEFAULT NULL,
  `foto_tindak_lanjut` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `id_user`, `id_kategori`, `judul_laporan`, `isi_laporan`, `tgl_laporan`, `lokasi_nama`, `latitude`, `longitude`, `foto_laporan`, `status`, `tgl_selesai`, `tanggapan_admin`, `foto_tindak_lanjut`) VALUES
(1, 2, 2, 'Jalan Masjid Agung Penuh Mobil', 'gitulah', '2026-02-07 03:27:23', 'simpang peace, masjid agung', -2.06629056332006, 101.3936909531245, '6986b11b068da.png', 'Selesai', '2026-02-07 00:44:23', 'sudah kami proses', '1770446663_Qu561U1oxMHTvZsCn4SYyz8o243wu3CzS0RCzF3v.jpg'),
(2, 2, 4, 'A Brief History of Time', '12', '2026-02-07 04:35:52', '12', -2.0673, 101.3899, '6986c12802120.png', 'Selesai', '2026-02-07 00:46:05', 'Sidak Langsung', '1770446765_BNNq2NzVMuJCWw0lCWA62QocSWSOKrtDkpojuDJ9.jpg'),
(3, 2, 1, 'Lapangan merdeka jalan rusak', 'Jalan Parah', '2026-02-07 06:48:27', 'Depan Gedung Nasional', -2.064194438316215, 101.3943732863187, '6986e03b406ea.jpg', 'Selesai', '2026-02-07 00:49:40', 'Sudah Ditindak Lanjuti', '1770446980_QZPfqLEIvQ3nlPBCdAJ0nwmLvj54BMhLtbQ5HzG1.jpg'),
(4, 2, 2, 'Parkir Berantakan', 'Parkir di bahu jalan', '2026-02-07 07:04:01', 'Simpang adipura', -2.0634439069521644, 101.39472247470765, '6986e3e1db34d.jpg', 'Proses', NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `pesan`
--

CREATE TABLE `pesan` (
  `id_pesan` int(11) NOT NULL,
  `id_pengirim` int(11) NOT NULL,
  `id_penerima` int(11) NOT NULL,
  `isi_pesan` text NOT NULL,
  `waktu_kirim` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesan`
--

INSERT INTO `pesan` (`id_pesan`, `id_pengirim`, `id_penerima`, `isi_pesan`, `waktu_kirim`, `is_read`) VALUES
(1, 2, 3, 'halloo admin', '2026-02-06 21:00:48', 0),
(2, 3, 2, 'iyaa', '2026-02-06 21:00:54', 0),
(3, 2, 3, '1', '2026-02-07 03:53:57', 0),
(4, 2, 3, '1', '2026-02-07 04:08:29', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `role` enum('admin','warga') NOT NULL DEFAULT 'warga',
  `foto_profil` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nik`, `nama_lengkap`, `email`, `password`, `no_telp`, `role`, `foto_profil`, `created_at`) VALUES
(2, '123', 'warga', NULL, '$2y$10$6jVJkd4NuY46L0O6rJUNve52hjriY0Rwyn2OVegXwA.1PY7zBSG.i', '0821989898', 'warga', 'default.jpg', '2026-02-06 20:26:16'),
(3, '999999', 'Admin Reset', NULL, '$2y$10$EX.OHzzV/85T8S/gmS8t.u31r1o64ygzZ1Y/s8oSbju1qY7amgBg2', NULL, 'admin', 'default.jpg', '2026-02-06 20:54:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `antrian`
--
ALTER TABLE `antrian`
  ADD PRIMARY KEY (`id_antrian`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id_pesan`),
  ADD KEY `id_pengirim` (`id_pengirim`),
  ADD KEY `id_penerima` (`id_penerima`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antrian`
--
ALTER TABLE `antrian`
  MODIFY `id_antrian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `antrian`
--
ALTER TABLE `antrian`
  ADD CONSTRAINT `antrian_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `pesan`
--
ALTER TABLE `pesan`
  ADD CONSTRAINT `pesan_ibfk_1` FOREIGN KEY (`id_pengirim`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesan_ibfk_2` FOREIGN KEY (`id_penerima`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
