# Fix untuk Error Kolom created_at dan updated_at

## Masalah
Error yang muncul:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'updated_at' in 'field list'
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'created_at' in 'field list'
```

## Penyebab
Tabel `penyewa` tidak memiliki kolom `created_at` dan `updated_at` yang dibutuhkan oleh beberapa file PHP.

## Solusi

### Opsi 1: Import Ulang Database (Recommended)
1. Backup database existing terlebih dahulu
2. Import file `sewa_gedung.sql` yang sudah diperbaiki
3. File ini sudah include kolom `created_at` dan `updated_at`

### Opsi 2: Update Database Existing
1. Jalankan script `add_penyewa_timestamps.sql`:
```sql
-- Menambahkan kolom created_at ke tabel penyewa
ALTER TABLE `penyewa` 
ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp() AFTER `email_terverifikasi`;

-- Menambahkan kolom updated_at ke tabel penyewa  
ALTER TABLE `penyewa` 
ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`;

-- Update existing records dengan timestamp saat ini
UPDATE `penyewa` SET `created_at` = current_timestamp() WHERE `created_at` IS NULL;
```

### Opsi 3: Gunakan Script Otomatis
1. Buka browser ke: `http://localhost/sewa-gedung/fix_penyewa_timestamps.php`
2. Klik "Jalankan Semua" untuk memperbaiki masalah secara otomatis
3. Script ini akan:
   - Menambahkan kolom yang hilang
   - Test query database
   - Menampilkan struktur tabel

## File yang Diperbaiki

### 1. `sewa_gedung.sql`
- Menambahkan kolom `created_at` dan `updated_at` ke tabel `penyewa`

### 2. `register.php`
- Memperbaiki query INSERT untuk include kolom `created_at`

### 3. File admin yang sudah menggunakan timestamp
- `admin/penyewa_add.php`
- `admin/penyewa_add_new.php` 
- `admin/penyewa_edit.php`
- `admin/penyewa_edit_new.php`
- `admin/penyewa_view.php`
- `admin/penyewa_view_new.php`

## Verifikasi
Setelah menjalankan salah satu solusi di atas:

1. Coba tambah penyewa baru
2. Coba edit penyewa existing
3. Pastikan tidak ada error lagi

## Struktur Tabel Penyewa yang Benar
```sql
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
```
