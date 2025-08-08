-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2025 at 08:02 PM
-- Server version: 10.4.32-MariaDB-log
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sewa_gedung`
--

-- --------------------------------------------------------

--
-- Table structure for table `acara`
--

CREATE TABLE `acara` (
  `id_acara` int(11) NOT NULL,
  `nama_acara` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `status` enum('tersedia','tidak tersedia') NOT NULL DEFAULT 'tersedia',
  `fasilitas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acara`
--

INSERT INTO `acara` (`id_acara`, `nama_acara`, `kapasitas`, `harga`, `lokasi`, `status`, `fasilitas`) VALUES
(1, 'Pernikahan', 200, 6150000.00, 'Gedung Utama Lantai 1', 'tersedia', 'AC, Sound System, Panggung, Dekorasi, Catering Area'),
(2, 'Rapat/Meeting', 50, 3885000.00, 'Ruang Meeting Lantai 2', 'tersedia', 'AC, Proyektor, WiFi, Whiteboard, Coffee Break'),
(3, 'Seminar', 100, 4350000.00, 'Auditorium Lantai 2', 'tersedia', 'AC, Proyektor, Sound System, WiFi, Podium');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$ZsmdmFvUHjJUH2RyjySPp.C.Nl4j.3YyeQ8ImI/m.rY209GWvaHPK', 'Administrator', 'admin@gedungptaneka.com', '2025-01-01 03:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pemesanan` int(11) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL,
  `status_pembayaran` enum('Lunas','Belum Lunas') NOT NULL DEFAULT 'Belum Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` int(11) NOT NULL,
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
  `status` varchar(50) DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penyewa`
--

CREATE TABLE `penyewa` (
  `id_penyewa` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_email`
--

CREATE TABLE `verifikasi_email` (
  `id_verifikasi` int(11) NOT NULL,
  `id_penyewa` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acara`
--
ALTER TABLE `acara`
  ADD PRIMARY KEY (`id_acara`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_pembayaran_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `fk_pemesanan_penyewa` (`id_penyewa`),
  ADD KEY `fk_pemesanan_acara` (`id_acara`);

--
-- Indexes for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id_penyewa`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  ADD PRIMARY KEY (`id_verifikasi`),
  ADD KEY `fk_verifikasi_penyewa` (`id_penyewa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acara`
--
ALTER TABLE `acara`
  MODIFY `id_acara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id_penyewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  MODIFY `id_verifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_pemesanan` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `fk_pemesanan_acara` FOREIGN KEY (`id_acara`) REFERENCES `acara` (`id_acara`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pemesanan_penyewa` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  ADD CONSTRAINT `fk_verifikasi_penyewa` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Stored Procedures for auto-increment reset to maintain sequential IDs
--

DELIMITER $$

--
-- Procedure to reset auto increment for acara table
--
CREATE PROCEDURE `reset_acara_auto_increment`()
BEGIN
    DECLARE next_id INT DEFAULT 1;
    DECLARE max_id INT DEFAULT 0;
    
    SELECT COALESCE(MAX(id_acara), 0) INTO max_id FROM acara;
    
    IF max_id = 0 THEN
        SET next_id = 1;
    ELSE
        SELECT COALESCE(MIN(t1.id_acara + 1), max_id + 1) INTO next_id 
        FROM acara t1 
        LEFT JOIN acara t2 ON t1.id_acara + 1 = t2.id_acara 
        WHERE t2.id_acara IS NULL;
    END IF;
    
    SET @sql = CONCAT('ALTER TABLE acara AUTO_INCREMENT = ', next_id);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

--
-- Procedure to reset auto increment for admin table
--
CREATE PROCEDURE `reset_admin_auto_increment`()
BEGIN
    DECLARE next_id INT DEFAULT 1;
    DECLARE max_id INT DEFAULT 0;
    
    SELECT COALESCE(MAX(id_admin), 0) INTO max_id FROM admin;
    
    IF max_id = 0 THEN
        SET next_id = 1;
    ELSE
        SELECT COALESCE(MIN(t1.id_admin + 1), max_id + 1) INTO next_id 
        FROM admin t1 
        LEFT JOIN admin t2 ON t1.id_admin + 1 = t2.id_admin 
        WHERE t2.id_admin IS NULL;
    END IF;
    
    SET @sql = CONCAT('ALTER TABLE admin AUTO_INCREMENT = ', next_id);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

--
-- Procedure to reset auto increment for pembayaran table
--
CREATE PROCEDURE `reset_pembayaran_auto_increment`()
BEGIN
    DECLARE next_id INT DEFAULT 1;
    DECLARE max_id INT DEFAULT 0;
    
    SELECT COALESCE(MAX(id_pembayaran), 0) INTO max_id FROM pembayaran;
    
    IF max_id = 0 THEN
        SET next_id = 1;
    ELSE
        SELECT COALESCE(MIN(t1.id_pembayaran + 1), max_id + 1) INTO next_id 
        FROM pembayaran t1 
        LEFT JOIN pembayaran t2 ON t1.id_pembayaran + 1 = t2.id_pembayaran 
        WHERE t2.id_pembayaran IS NULL;
    END IF;
    
    SET @sql = CONCAT('ALTER TABLE pembayaran AUTO_INCREMENT = ', next_id);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

--
-- Procedure to reset auto increment for pemesanan table
--
CREATE PROCEDURE `reset_pemesanan_auto_increment`()
BEGIN
    DECLARE next_id INT DEFAULT 1;
    DECLARE max_id INT DEFAULT 0;
    
    SELECT COALESCE(MAX(id_pemesanan), 0) INTO max_id FROM pemesanan;
    
    IF max_id = 0 THEN
        SET next_id = 1;
    ELSE
        SELECT COALESCE(MIN(t1.id_pemesanan + 1), max_id + 1) INTO next_id 
        FROM pemesanan t1 
        LEFT JOIN pemesanan t2 ON t1.id_pemesanan + 1 = t2.id_pemesanan 
        WHERE t2.id_pemesanan IS NULL;
    END IF;
    
    SET @sql = CONCAT('ALTER TABLE pemesanan AUTO_INCREMENT = ', next_id);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

--
-- Procedure to reset auto increment for penyewa table
--
CREATE PROCEDURE `reset_penyewa_auto_increment`()
BEGIN
    DECLARE next_id INT DEFAULT 1;
    DECLARE max_id INT DEFAULT 0;
    
    SELECT COALESCE(MAX(id_penyewa), 0) INTO max_id FROM penyewa;
    
    IF max_id = 0 THEN
        SET next_id = 1;
    ELSE
        SELECT COALESCE(MIN(t1.id_penyewa + 1), max_id + 1) INTO next_id 
        FROM penyewa t1 
        LEFT JOIN penyewa t2 ON t1.id_penyewa + 1 = t2.id_penyewa 
        WHERE t2.id_penyewa IS NULL;
    END IF;
    
    SET @sql = CONCAT('ALTER TABLE penyewa AUTO_INCREMENT = ', next_id);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

--
-- Procedure to reset auto increment for verifikasi_email table
--
CREATE PROCEDURE `reset_verifikasi_email_auto_increment`()
BEGIN
    DECLARE next_id INT DEFAULT 1;
    DECLARE max_id INT DEFAULT 0;
    
    SELECT COALESCE(MAX(id_verifikasi), 0) INTO max_id FROM verifikasi_email;
    
    IF max_id = 0 THEN
        SET next_id = 1;
    ELSE
        SELECT COALESCE(MIN(t1.id_verifikasi + 1), max_id + 1) INTO next_id 
        FROM verifikasi_email t1 
        LEFT JOIN verifikasi_email t2 ON t1.id_verifikasi + 1 = t2.id_verifikasi 
        WHERE t2.id_verifikasi IS NULL;
    END IF;
    
    SET @sql = CONCAT('ALTER TABLE verifikasi_email AUTO_INCREMENT = ', next_id);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DELIMITER ;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
