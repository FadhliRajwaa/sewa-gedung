-- ===================================================================
-- FIX DATABASE RELATIONS - PERBAIKAN RELASI TABEL
-- Memperbaiki relasi PK-FK sesuai standar normalisasi database
-- ===================================================================

-- 1. TABEL PEMBAYARAN - Tambah relasi ke tabel lain
-- Tambahkan kolom untuk referensi pembayaran di tabel pemesanan
ALTER TABLE pemesanan ADD COLUMN id_pembayaran INT(11) NULL;

-- Buat foreign key constraint
ALTER TABLE pemesanan 
ADD CONSTRAINT fk_pemesanan_pembayaran 
FOREIGN KEY (id_pembayaran) REFERENCES pembayaran(id_pembayaran) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- 2. TABEL VERIFIKASI EMAIL - Tambah relasi ke tabel lain
-- Tambahkan kolom untuk referensi verifikasi di tabel penyewa
ALTER TABLE penyewa ADD COLUMN id_verifikasi INT(11) NULL;

-- Buat foreign key constraint
ALTER TABLE penyewa 
ADD CONSTRAINT fk_penyewa_verifikasi 
FOREIGN KEY (id_verifikasi) REFERENCES verifikasi_email(id_verifikasi) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. UPDATE DATA EXISTING - Sinkronisasi data yang sudah ada
-- Update relasi pembayaran berdasarkan id_pemesanan yang sama
UPDATE pemesanan p 
SET id_pembayaran = (
    SELECT pb.id_pembayaran 
    FROM pembayaran pb 
    WHERE pb.id_pemesanan = p.id_pemesanan 
    LIMIT 1
) 
WHERE EXISTS (
    SELECT 1 FROM pembayaran pb2 
    WHERE pb2.id_pemesanan = p.id_pemesanan
);

-- Update relasi verifikasi berdasarkan id_penyewa yang sama
UPDATE penyewa py 
SET id_verifikasi = (
    SELECT v.id_verifikasi 
    FROM verifikasi_email v 
    WHERE v.id_penyewa = py.id_penyewa 
    LIMIT 1
) 
WHERE EXISTS (
    SELECT 1 FROM verifikasi_email v2 
    WHERE v2.id_penyewa = py.id_penyewa
);

-- 4. TAMBAH INDEX UNTUK PERFORMA
CREATE INDEX idx_pemesanan_pembayaran ON pemesanan(id_pembayaran);
CREATE INDEX idx_penyewa_verifikasi ON penyewa(id_verifikasi);

-- 5. ALTERNATIF SOLUSI - Jika ingin membuat tabel junction
-- Untuk relasi many-to-many jika diperlukan

-- Tabel junction untuk pembayaran-pemesanan (jika satu pembayaran bisa untuk multiple pemesanan)
CREATE TABLE IF NOT EXISTS pemesanan_pembayaran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_pemesanan INT(11) NOT NULL,
    id_pembayaran INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pemesanan) REFERENCES pemesanan(id_pemesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_pembayaran) REFERENCES pembayaran(id_pembayaran) ON DELETE CASCADE,
    UNIQUE KEY unique_pemesanan_pembayaran (id_pemesanan, id_pembayaran)
);

-- Tabel junction untuk penyewa-verifikasi (jika satu penyewa bisa punya multiple verifikasi)
CREATE TABLE IF NOT EXISTS penyewa_verifikasi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_penyewa INT(11) NOT NULL,
    id_verifikasi INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_penyewa) REFERENCES penyewa(id_penyewa) ON DELETE CASCADE,
    FOREIGN KEY (id_verifikasi) REFERENCES verifikasi_email(id_verifikasi) ON DELETE CASCADE,
    UNIQUE KEY unique_penyewa_verifikasi (id_penyewa, id_verifikasi)
);

-- ===================================================================
-- DOKUMENTASI RELASI SETELAH PERBAIKAN:
-- ===================================================================

/*
RELASI SETELAH PERBAIKAN:

1. PENYEWA (1) -> PEMESANAN (M)
   - penyewa.id_penyewa = pemesanan.id_penyewa

2. ACARA (1) -> PEMESANAN (M)
   - acara.id_acara = pemesanan.id_acara

3. PEMESANAN (1) -> PEMBAYARAN (1)
   - pemesanan.id_pemesanan = pembayaran.id_pemesanan
   - pemesanan.id_pembayaran = pembayaran.id_pembayaran (NEW)

4. PENYEWA (1) -> VERIFIKASI_EMAIL (1)
   - penyewa.id_penyewa = verifikasi_email.id_penyewa
   - penyewa.id_verifikasi = verifikasi_email.id_verifikasi (NEW)

SEMUA PK SEKARANG PUNYA RELASI FK:
✅ id_penyewa (PK) -> FK di pemesanan, verifikasi_email
✅ id_acara (PK) -> FK di pemesanan
✅ id_pemesanan (PK) -> FK di pembayaran
✅ id_pembayaran (PK) -> FK di pemesanan (NEW)
✅ id_verifikasi (PK) -> FK di penyewa (NEW)
✅ id_admin (PK) -> standalone (untuk admin tidak perlu relasi)
*/
