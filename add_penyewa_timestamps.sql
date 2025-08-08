-- SQL untuk menambahkan kolom created_at dan updated_at ke tabel penyewa
-- Jalankan ini jika database sudah ada dan tidak ingin import ulang

-- Menambahkan kolom created_at ke tabel penyewa
ALTER TABLE `penyewa` 
ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp() AFTER `email_terverifikasi`;

-- Menambahkan kolom updated_at ke tabel penyewa  
ALTER TABLE `penyewa` 
ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`;

-- Update existing records dengan timestamp saat ini untuk created_at
UPDATE `penyewa` SET `created_at` = current_timestamp() WHERE `created_at` IS NULL OR `created_at` = '0000-00-00 00:00:00';
