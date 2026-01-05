-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 05, 2026 at 09:45 AM
-- Server version: 5.7.39
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_magang_kit`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_datang` time DEFAULT NULL,
  `lat_datang` varchar(50) DEFAULT NULL,
  `long_datang` varchar(50) DEFAULT NULL,
  `foto_datang` varchar(255) DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `lat_pulang` varchar(50) DEFAULT NULL,
  `long_pulang` varchar(50) DEFAULT NULL,
  `foto_pulang` varchar(255) DEFAULT NULL,
  `status` enum('hadir','telat','izin','alpa') NOT NULL DEFAULT 'alpa',
  `keterangan` text,
  `jenis_izin` varchar(50) DEFAULT NULL,
  `bukti_izin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL,
  `pendaftar_id` int(11) NOT NULL,
  `jenis_dokumen` enum('cv','foto','surat_permohonan','surat_kampus') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name_original` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `master_divisi`
--

CREATE TABLE `master_divisi` (
  `id` int(11) NOT NULL,
  `nama_divisi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `master_fakultas`
--

CREATE TABLE `master_fakultas` (
  `id` int(11) NOT NULL,
  `nama_fakultas` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `master_institusi`
--

CREATE TABLE `master_institusi` (
  `id` int(11) NOT NULL,
  `nama_institusi` varchar(255) NOT NULL,
  `kategori` enum('sekolah','universitas') NOT NULL DEFAULT 'universitas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `master_jurusan`
--

CREATE TABLE `master_jurusan` (
  `id` int(11) NOT NULL,
  `nama_jurusan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `master_lokasi`
--

CREATE TABLE `master_lokasi` (
  `id` int(11) NOT NULL,
  `nama_lokasi` varchar(100) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `radius_meter` int(11) DEFAULT '50'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pendaftar`
--

CREATE TABLE `pendaftar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `jenis_peserta` enum('mahasiswa','siswa') NOT NULL,
  `nim_nis` varchar(50) NOT NULL,
  `no_surat` varchar(100) DEFAULT NULL,
  `tgl_surat` date DEFAULT NULL,
  `institusi` varchar(150) NOT NULL,
  `fakultas` varchar(150) DEFAULT NULL,
  `jurusan` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` text,
  `jenis_magang` enum('wajib','mandiri') NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `durasi_bulan` int(11) NOT NULL,
  `status` enum('pending','diterima','ditolak','selesai') NOT NULL DEFAULT 'pending',
  `divisi_id` int(11) DEFAULT NULL COMMENT 'Pilihan Minat Divisi saat Daftar',
  `file_surat_balasan` varchar(255) DEFAULT NULL,
  `file_draft_balasan` varchar(255) DEFAULT NULL,
  `file_sertifikat` varchar(255) DEFAULT NULL,
  `tgl_daftar` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `permintaan_magang`
--

CREATE TABLE `permintaan_magang` (
  `id` int(11) NOT NULL,
  `divisi_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(1) DEFAULT NULL COMMENT '1:Admin/HC, 2:Mentor/Divisi, 3:Peserta',
  `divisi_id` int(11) DEFAULT NULL,
  `lokasi_id` int(11) DEFAULT NULL,
  `role` enum('admin','peserta','mentor') NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role_id`, `divisi_id`, `lokasi_id`, `role`, `nama_lengkap`, `created_at`, `last_login`) VALUES
(1, 'admin@kit.go.id', '$2y$10$D1wf5FYzOCyZja3CeDJKZeFB1L4QmTxVTeK/K5wUwO9HXZXNfyZFK', 1, 6, admin, 'admin', 'Administrator KIT', '2025-12-15 15:49:01', '2025-12-19 22:13:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_absensi_user` (`user_id`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dokumen_pendaftar` (`pendaftar_id`);

--
-- Indexes for table `master_divisi`
--
ALTER TABLE `master_divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_fakultas`
--
ALTER TABLE `master_fakultas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_institusi`
--
ALTER TABLE `master_institusi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_jurusan`
--
ALTER TABLE `master_jurusan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_lokasi`
--
ALTER TABLE `master_lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pendaftar`
--
ALTER TABLE `pendaftar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `fk_pendaftar_user` (`user_id`),
  ADD KEY `fk_pendaftar_divisi` (`divisi_id`);

--
-- Indexes for table `permintaan_magang`
--
ALTER TABLE `permintaan_magang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_divisi` (`divisi_id`),
  ADD KEY `fk_user_lokasi` (`lokasi_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_divisi`
--
ALTER TABLE `master_divisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_fakultas`
--
ALTER TABLE `master_fakultas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_institusi`
--
ALTER TABLE `master_institusi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_jurusan`
--
ALTER TABLE `master_jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_lokasi`
--
ALTER TABLE `master_lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pendaftar`
--
ALTER TABLE `pendaftar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permintaan_magang`
--
ALTER TABLE `permintaan_magang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `fk_dokumen_pendaftar` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pendaftar`
--
ALTER TABLE `pendaftar`
  ADD CONSTRAINT `fk_pendaftar_divisi` FOREIGN KEY (`divisi_id`) REFERENCES `master_divisi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pendaftar_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_divisi` FOREIGN KEY (`divisi_id`) REFERENCES `master_divisi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_lokasi` FOREIGN KEY (`lokasi_id`) REFERENCES `master_lokasi` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
