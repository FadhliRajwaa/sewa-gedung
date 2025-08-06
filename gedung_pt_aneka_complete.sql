-- ===============================================
-- DATABASE SEWA GEDUNG - COMPLETE SQL EXPORT
-- Created on: August 5, 2025
-- ===============================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `sewa_gedung` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sewa_gedung`;

-- ===============================================
-- TABLE STRUCTURES
-- ===============================================

-- Table structure for table `admin`
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `acara`
DROP TABLE IF EXISTS `acara`;
CREATE TABLE `acara` (
  `id_acara` int(11) NOT NULL AUTO_INCREMENT,
  `nama_acara` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `status` enum('tersedia','tidak tersedia') NOT NULL DEFAULT 'tersedia',
  `fasilitas` text DEFAULT NULL,
  PRIMARY KEY (`id_acara`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `penyewa`
DROP TABLE IF EXISTS `penyewa`;
CREATE TABLE `penyewa` (
  `id_penyewa` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_penyewa` enum('individu','instansi') NOT NULL,
  `nama_instansi` varchar(100) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_terverifikasi` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_penyewa`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `pemesanan`
DROP TABLE IF EXISTS `pemesanan`;
CREATE TABLE `pemesanan` (
  `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT,
  `id_penyewa` int(11) NOT NULL,
  `id_acara` int(11) NOT NULL,
  `tanggal_sewa` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi` int(11) NOT NULL,
  `kebutuhan_tambahan` text DEFAULT NULL,
  `total` decimal(15,2) NOT NULL,
  `metode_pembayaran` enum('QRIS','Transfer_BCA','Transfer_BNI','Transfer_BRI','Transfer_Mandiri') NOT NULL,
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipe_pesanan` enum('online','offline') NOT NULL DEFAULT 'online',
  PRIMARY KEY (`id_pemesanan`),
  KEY `fk_pemesanan_penyewa` (`id_penyewa`),
  KEY `fk_pemesanan_acara` (`id_acara`),
  CONSTRAINT `fk_pemesanan_acara` FOREIGN KEY (`id_acara`) REFERENCES `acara` (`id_acara`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pemesanan_penyewa` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `pembayaran`
DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT,
  `id_pemesanan` int(11) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL,
  `status_pembayaran` enum('Lunas','Belum Lunas') NOT NULL DEFAULT 'Belum Lunas',
  PRIMARY KEY (`id_pembayaran`),
  KEY `fk_pembayaran_pemesanan` (`id_pemesanan`),
  CONSTRAINT `fk_pembayaran_pemesanan` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `verifikasi_email`
DROP TABLE IF EXISTS `verifikasi_email`;
CREATE TABLE `verifikasi_email` (
  `id_verifikasi` int(11) NOT NULL AUTO_INCREMENT,
  `id_penyewa` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL,
  PRIMARY KEY (`id_verifikasi`),
  KEY `fk_verifikasi_penyewa` (`id_penyewa`),
  CONSTRAINT `fk_verifikasi_penyewa` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================
-- SAMPLE DATA INSERTION
-- ===============================================

-- Insert admin data
INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$ZsmdmFvUHjJUH2RyjySPp.C.Nl4j.3YyeQ8ImI/m.rY209GWvaHPK', 'Administrator', 'admin@gedungptaneka.com', '2025-01-01 10:00:00');

-- Insert acara data (events/venues)
INSERT INTO `acara` (`id_acara`, `nama_acara`, `kapasitas`, `harga`, `lokasi`, `status`, `fasilitas`) VALUES
(1, 'Pernikahan', 200, 5000000.00, 'Gedung Utama Lantai 1', 'tersedia', 'AC, Sound System, Panggung, Dekorasi, Catering Area'),
(2, 'Rapat/Meeting', 50, 3500000.00, 'Ruang Meeting Lantai 2', 'tersedia', 'AC, Proyektor, WiFi, Whiteboard, Coffee Break'),
(3, 'Seminar', 100, 4000000.00, 'Auditorium Lantai 2', 'tersedia', 'AC, Proyektor, Sound System, WiFi, Podium'),
(4, 'Workshop', 30, 2500000.00, 'Ruang Workshop Lantai 1', 'tersedia', 'AC, Proyektor, WiFi, Meja Kerja'),
(5, 'Pameran', 150, 4500000.00, 'Hall Pameran Lantai 1', 'tersedia', 'AC, Lampu Pameran, Stand Display, Security');

-- Insert penyewa data (customers)
INSERT INTO `penyewa` (`id_penyewa`, `tipe_penyewa`, `nama_instansi`, `nama_lengkap`, `nik`, `no_telepon`, `email`, `alamat`, `username`, `password`, `email_terverifikasi`) VALUES
(1, 'individu', NULL, 'Budi Santoso', '3271010101900001', '081234567890', 'budi.santoso@email.com', 'Jl. Merdeka No. 123, Jakarta Pusat', 'budisantoso123', '$2y$10$3o7LMklKPqlg3kU4NGmTK.hcEC7dgvbpUXHmH4Ks9uBhptw1mOjTm', 1),
(2, 'instansi', 'PT. Teknologi Maju', NULL, NULL, '021-8765432', 'info@teknologimaju.com', 'Jl. Sudirman No. 456, Jakarta Selatan', 'ptteknologimaju456', '$2y$10$3o7LMklKPqlg3kU4NGmTK.hcEC7dgvbpUXHmH4Ks9uBhptw1mOjTm', 1),
(3, 'individu', NULL, 'Siti Rahayu', '3271010202900002', '085678901234', 'siti.rahayu@email.com', 'Jl. Kebon Jeruk No. 789, Jakarta Barat', 'sitirahayu789', '$2y$10$3o7LMklKPqlg3kU4NGmTK.hcEC7dgvbpUXHmH4Ks9uBhptw1mOjTm', 1),
(4, 'instansi', 'CV. Kreatif Solusi', NULL, NULL, '021-5551234', 'contact@kreatifsolusi.co.id', 'Jl. Gatot Subroto No. 321, Jakarta Selatan', 'cvkreatifsolusi321', '$2y$10$3o7LMklKPqlg3kU4NGmTK.hcEC7dgvbpUXHmH4Ks9uBhptw1mOjTm', 1),
(5, 'individu', NULL, 'Ahmad Firdaus', '3271010303900003', '087123456789', 'ahmad.firdaus@email.com', 'Jl. Cempaka Putih No. 654, Jakarta Pusat', 'ahmadfirdaus654', '$2y$10$3o7LMklKPqlg3kU4NGmTK.hcEC7dgvbpUXHmH4Ks9uBhptw1mOjTm', 0);

-- Insert pemesanan data (bookings)
INSERT INTO `pemesanan` (`id_pemesanan`, `id_penyewa`, `id_acara`, `tanggal_sewa`, `tanggal_selesai`, `durasi`, `kebutuhan_tambahan`, `total`, `metode_pembayaran`, `tanggal_pesan`, `tipe_pesanan`) VALUES
(1, 1, 1, '2025-02-14', '2025-02-14', 1, 'Dekorasi tambahan untuk pernikahan, fotografer', 5500000.00, 'Transfer_BCA', '2025-01-15 09:30:00', 'online'),
(2, 2, 2, '2025-02-20', '2025-02-20', 1, 'Catering untuk 50 orang, dokumentasi meeting', 4000000.00, 'Transfer_BNI', '2025-01-18 14:15:00', 'offline'),
(3, 3, 3, '2025-03-05', '2025-03-05', 1, 'Materi seminar, goodie bag peserta', 4200000.00, 'Transfer_Mandiri', '2025-02-01 11:20:00', 'online'),
(4, 4, 4, '2025-03-12', '2025-03-13', 2, 'Peralatan workshop khusus, modul training', 5200000.00, 'Transfer_BRI', '2025-02-15 16:45:00', 'offline'),
(5, 1, 5, '2025-03-20', '2025-03-22', 3, 'Stand tambahan, security 24 jam', 15000000.00, 'Transfer_BCA', '2025-02-25 10:00:00', 'online'),
(6, 5, 2, '2025-04-10', '2025-04-10', 1, 'Proyektor cadangan, coffee break premium', 3700000.00, 'QRIS', '2025-03-01 13:30:00', 'online'),
(7, 2, 1, '2025-04-25', '2025-04-25', 1, 'Live streaming equipment, dokumentasi lengkap', 6000000.00, 'Transfer_BNI', '2025-03-10 08:15:00', 'offline'),
(8, 3, 3, '2025-05-15', '2025-05-15', 1, 'Sistem registrasi online, sertifikat peserta', 4300000.00, 'Transfer_Mandiri', '2025-04-01 15:45:00', 'online');

-- Insert pembayaran data (payments)
INSERT INTO `pembayaran` (`id_pembayaran`, `id_pemesanan`, `bukti_pembayaran`, `tanggal_upload`, `status_pembayaran`) VALUES
(1, 1, 'bukti_bayar_001.jpg', '2025-01-16 10:30:00', 'Lunas'),
(2, 2, 'bukti_bayar_002.jpg', '2025-01-19 09:15:00', 'Lunas'),
(3, 3, 'bukti_bayar_003.jpg', '2025-02-02 14:20:00', 'Lunas'),
(4, 4, NULL, NULL, 'Belum Lunas'),
(5, 5, 'bukti_bayar_005.jpg', '2025-02-26 11:45:00', 'Lunas'),
(6, 6, NULL, NULL, 'Belum Lunas'),
(7, 7, 'bukti_bayar_007.jpg', '2025-03-11 16:30:00', 'Lunas'),
(8, 8, NULL, NULL, 'Belum Lunas');

-- Insert verifikasi_email data
INSERT INTO `verifikasi_email` (`id_verifikasi`, `id_penyewa`, `token`, `created_at`, `expires_at`) VALUES
(1, 5, 'abc123def456ghi789', '2025-01-01 12:00:00', '2025-01-08 12:00:00');

-- ===============================================
-- AUTO INCREMENT SETTINGS
-- ===============================================

ALTER TABLE `admin` AUTO_INCREMENT = 2;
ALTER TABLE `acara` AUTO_INCREMENT = 6;
ALTER TABLE `penyewa` AUTO_INCREMENT = 6;
ALTER TABLE `pemesanan` AUTO_INCREMENT = 9;
ALTER TABLE `pembayaran` AUTO_INCREMENT = 9;
ALTER TABLE `verifikasi_email` AUTO_INCREMENT = 2;

-- ===============================================
-- DEFAULT ADMIN LOGIN CREDENTIALS
-- ===============================================
-- Username: admin
-- Password: admin123

-- ===============================================
-- SAMPLE USER LOGIN CREDENTIALS
-- ===============================================
-- All sample users have password: password123
-- Examples:
-- Username: budisantoso123, Password: password123
-- Username: ptteknologimaju456, Password: password123

-- ===============================================
-- NOTES
-- ===============================================
-- 1. Database sudah include semua tabel yang diperlukan
-- 2. Foreign key constraints sudah diatur dengan CASCADE
-- 3. Data sample sudah termasuk untuk testing
-- 4. Password sudah di-hash menggunakan bcrypt
-- 5. Ada data pembayaran dengan status Lunas dan Belum Lunas
-- 6. Tanggal pemesanan dan acara sudah disesuaikan untuk testing
-- 7. Email verification token included untuk testing

-- End of SQL Export
