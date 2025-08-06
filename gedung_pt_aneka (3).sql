-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Jul 2025 pada 16.15
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gedung_pt_aneka`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `acara`
--

CREATE TABLE `acara` (
  `id_acara` int(11) NOT NULL,
  `nama_acara` varchar(100) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `status` enum('tersedia','tidak tersedia') DEFAULT 'tersedia',
  `fasilitas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `acara`
--

INSERT INTO `acara` (`id_acara`, `nama_acara`, `kapasitas`, `harga`, `lokasi`, `status`, `fasilitas`) VALUES
(1, 'Acara A', 300, 5000000.00, 'Jl. Raya No. 123, Jakarta', '', 'AC, Audio Visual, Parkir, Wi-Fi'),
(2, 'Acara B', 150, 3500000.00, 'Jl. Sudirman No. 45, Bandung', '', 'AC, Proyektor, Ruang VIP');

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `no_telepon`, `email`, `alamat`, `username`, `password`) VALUES
(1, 'Will Admin', '082144281110', 'pietersonwill@gmail.com', 'Jakarta', 'will', 'will123');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pemesanan` int(11) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT current_timestamp(),
  `status_pembayaran` enum('Lunas','Belum Lunas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` int(11) NOT NULL,
  `id_penyewa` int(11) NOT NULL,
  `id_acara` int(11) NOT NULL,
  `tanggal_sewa` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi` int(11) NOT NULL,
  `kebutuhan_tambahan` text DEFAULT NULL,
  `total` decimal(15,2) DEFAULT NULL,
  `metode_pembayaran` enum('QRIS','Transfer_BCA','Transfer_BNI','Transfer_BRI','Transfer_Mandiri') DEFAULT NULL,
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipe_pesanan` enum('online','offline') NOT NULL DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `id_penyewa`, `id_acara`, `tanggal_sewa`, `tanggal_selesai`, `durasi`, `kebutuhan_tambahan`, `total`, `metode_pembayaran`, `tanggal_pesan`, `tipe_pesanan`) VALUES
(1, 1, 1, '2025-01-10', '2025-01-11', 1, 'AC, Wi-Fi', 5000000.00, 'Transfer_BCA', '2025-01-05 05:30:00', 'offline'),
(2, 1, 2, '2025-02-15', '2025-02-16', 1, 'Proyektor', 3500000.00, 'Transfer_BCA', '2025-02-12 03:30:00', 'online'),
(3, 1, 1, '2025-03-20', '2025-03-21', 1, 'AC, Parkir', 5000000.00, 'Transfer_BNI', '2025-03-10 02:00:00', 'offline'),
(4, 1, 2, '2025-04-05', '2025-04-06', 1, 'Ruang VIP', 3500000.00, 'QRIS', '2025-04-01 08:45:00', 'online');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyewa`
--

CREATE TABLE `penyewa` (
  `id_penyewa` int(11) NOT NULL,
  `tipe_penyewa` enum('umum','instansi') NOT NULL,
  `nama_instansi` varchar(100) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nik` char(16) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_terverifikasi` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penyewa`
--

INSERT INTO `penyewa` (`id_penyewa`, `tipe_penyewa`, `nama_instansi`, `nama_lengkap`, `nik`, `no_telepon`, `email`, `alamat`, `username`, `password`, `email_terverifikasi`) VALUES
(1, 'umum', NULL, 'Williem Pieterson Junior', '5371051003990001', '082144281110', 'pietersonwill@gmail.com', 'Jl. Lestari Hijau 2 Blok D3B No.50 Dekat Scientia Square Park RT.7 2, Medang, Kec. Pagedangan, Jakarta, Bali', 'liemqt', 'liem123', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `verifikasi_email`
--

CREATE TABLE `verifikasi_email` (
  `id_verifikasi` int(11) NOT NULL,
  `id_penyewa` int(11) NOT NULL,
  `kode_verifikasi` char(6) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `verifikasi_email`
--

INSERT INTO `verifikasi_email` (`id_verifikasi`, `id_penyewa`, `kode_verifikasi`, `status`, `dibuat_pada`) VALUES
(1, 1, '663506', 0, '2025-07-16 03:09:55');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `acara`
--
ALTER TABLE `acara`
  ADD PRIMARY KEY (`id_acara`);

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_penyewa` (`id_penyewa`),
  ADD KEY `id_acara` (`id_acara`);

--
-- Indeks untuk tabel `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id_penyewa`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  ADD PRIMARY KEY (`id_verifikasi`),
  ADD KEY `id_penyewa` (`id_penyewa`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `acara`
--
ALTER TABLE `acara`
  MODIFY `id_acara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id_penyewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  MODIFY `id_verifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`);

--
-- Ketidakleluasaan untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_acara`) REFERENCES `acara` (`id_acara`);

--
-- Ketidakleluasaan untuk tabel `verifikasi_email`
--
ALTER TABLE `verifikasi_email`
  ADD CONSTRAINT `verifikasi_email_ibfk_1` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
