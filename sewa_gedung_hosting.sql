-- phpMyAdmin SQL Dump for Hosting
-- Compatible version without stored procedures and DEFINER
-- Generated for sewa_gedung database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sewa_gedung` or your hosting database name
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
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', '2024-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pemesanan` int(11) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `metode_pembayaran` enum('transfer','tunai','kartu_kredit') NOT NULL,
  `status_pembayaran` enum('pending','konfirmasi','selesai','gagal') NOT NULL DEFAULT 'pending',
  `tanggal_pembayaran` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pemesanan`, `jumlah`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`, `bukti_pembayaran`, `catatan`) VALUES
(1, 1, 6150000.00, 'transfer', 'konfirmasi', '2024-12-20 17:00:00', 'bukti_transfer_001.jpg', 'Pembayaran untuk pernikahan tanggal 25 Desember 2024'),
(2, 2, 7770000.00, 'transfer', 'pending', '2024-12-21 10:30:00', 'bukti_transfer_002.jpg', 'Pembayaran untuk meeting 2 hari'),
(3, 3, 8700000.00, 'tunai', 'selesai', '2024-12-22 14:15:00', NULL, 'Pembayaran tunai untuk seminar 2 hari');

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
  `total` decimal(15,2) NOT NULL,
  `status` enum('pending','dikonfirmasi','dibatalkan','selesai') NOT NULL DEFAULT 'pending',
  `tanggal_pemesanan` timestamp NOT NULL DEFAULT current_timestamp(),
  `kebutuhan_tambahan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `id_penyewa`, `id_acara`, `tanggal_sewa`, `tanggal_selesai`, `durasi`, `total`, `status`, `tanggal_pemesanan`, `kebutuhan_tambahan`) VALUES
(1, 1, 1, '2024-12-25', '2024-12-25', 1, 6150000.00, 'dikonfirmasi', '2024-12-20 10:00:00', 'Dekorasi tema merah putih, catering untuk 200 orang, live music'),
(2, 2, 2, '2024-12-30', '2024-12-31', 2, 7770000.00, 'pending', '2024-12-21 09:00:00', 'Setup ruangan gaya U-shape, coffee break pagi dan siang, flipchart tambahan'),
(3, 3, 3, '2025-01-15', '2025-01-16', 2, 8700000.00, 'dikonfirmasi', '2024-12-22 11:30:00', 'Layar proyektor besar, sound system berkualitas tinggi, area registrasi terpisah');

-- --------------------------------------------------------

--
-- Table structure for table `penyewa`
--

CREATE TABLE `penyewa` (
  `id_penyewa` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `tipe_penyewa` enum('individu','instansi') NOT NULL DEFAULT 'individu',
  `nama_instansi` varchar(100) DEFAULT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `email_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyewa`
--

INSERT INTO `penyewa` (`id_penyewa`, `nama_lengkap`, `email`, `password`, `no_telepon`, `alamat`, `tipe_penyewa`, `nama_instansi`, `tanggal_daftar`, `email_verified`) VALUES
(1, 'Ahmad Rizki', 'ahmad.rizki@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'Jl. Merdeka No. 123, Jakarta Pusat', 'individu', NULL, '2024-12-15 08:00:00', 1),
(2, 'Sarah Putri', 'sarah.putri@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081298765432', 'Jl. Sudirman No. 456, Jakarta Selatan', 'instansi', 'PT. Teknologi Maju', '2024-12-18 09:30:00', 1),
(3, 'Dr. Budi Santoso', 'budi.santoso@university.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234598765', 'Jl. Pendidikan No. 789, Depok', 'instansi', 'Universitas Indonesia', '2024-12-20 07:45:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_email`
--

CREATE TABLE `verifikasi_email` (
  `id_verifikasi` int(11) NOT NULL,
  `id_penyewa` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_used` tinyint(1) NOT NULL DEFAULT 0
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
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_penyewa` (`id_penyewa`),
  ADD KEY `id_acara` (`id_acara`);

--
-- Indexes for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id_penyewa`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  ADD PRIMARY KEY (`id_verifikasi`),
  ADD KEY `id_penyewa` (`id_penyewa`);

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
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id_penyewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  MODIFY `id_verifikasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE;

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_acara`) REFERENCES `acara` (`id_acara`) ON DELETE CASCADE;

--
-- Constraints for table `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  ADD CONSTRAINT `verifikasi_email_ibfk_1` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
