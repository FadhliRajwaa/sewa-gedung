# Panduan Auto Increment Reset System

## Masalah yang Dipecahkan
Ketika data dihapus dari database, ID auto increment tidak akan menggunakan kembali ID yang sudah dihapus. Contoh:
- Data dengan ID 1, 2, 3
- Hapus ID 2
- Insert data baru → dapat ID 4 (bukan ID 2)

Dengan sistem ini, ID akan tetap berurutan tanpa gap.

## Cara Kerja Sistem

### 1. Stored Procedures
Database memiliki stored procedures untuk setiap tabel:
- `reset_acara_auto_increment()`
- `reset_admin_auto_increment()`
- `reset_pembayaran_auto_increment()`
- `reset_pemesanan_auto_increment()`
- `reset_penyewa_auto_increment()`
- `reset_verifikasi_email_auto_increment()`

### 2. PHP Helper Functions
File `reset_auto_increment.php` menyediakan fungsi PHP untuk memanggil stored procedures.

### 3. Automatic Call After Delete
File `admin/ajax/delete_penyewa.php` sudah dimodifikasi untuk otomatis memanggil reset setelah delete.

## Testing

### Test Otomatis
1. Buka browser ke: `http://localhost/sewa-gedung/test_auto_increment.php`
2. Klik "Check & Test"
3. Lihat hasil apakah ID mengisi gap yang kosong

### Test Manual
1. Masuk ke admin panel
2. Tambah 3 penyewa baru (akan dapat ID 1, 2, 3)
3. Hapus penyewa dengan ID 2
4. Tambah penyewa baru → seharusnya dapat ID 2 (bukan ID 4)

## Implementasi untuk Tabel Lain

Jika ingin menerapkan ke tabel lain (acara, pemesanan, dll), ikuti pattern ini:

### 1. Di file delete yang sesuai:
```php
// Setelah DELETE query
if ($result) {
    // Reset auto increment
    resetNamaTabelAutoIncrement();
    
    echo json_encode(['success' => true, 'message' => 'Data berhasil dihapus']);
}
```

### 2. Contoh untuk tabel acara:
```php
require_once '../../reset_auto_increment.php';

// ... delete query ...

if ($result) {
    resetAcaraAutoIncrement();
    // ... response ...
}
```

## Troubleshooting

### Error "Procedure not found"
1. Pastikan database sudah di-import dari `sewa_gedung.sql` terbaru
2. Atau jalankan stored procedures secara manual dari file SQL

### Error "Call to undefined function"
1. Pastikan `require_once 'reset_auto_increment.php';` ada di file yang memanggil
2. Pastikan path file benar

### Auto increment tidak reset
1. Periksa apakah stored procedure berhasil dijalankan
2. Cek log error PHP
3. Test manual dengan `test_auto_increment.php`

## Status Implementasi

✅ **Sudah Diimplementasi:**
- ✅ Tabel penyewa (delete via admin panel)
- ✅ Tabel pemesanan (delete via admin panel) 
- ✅ Tabel pembayaran (auto-delete saat pemesanan dihapus - CASCADE)
- ✅ Stored procedures untuk semua tabel
- ✅ PHP helper functions
- ✅ Test scripts

⏳ **Tidak Perlu Diimplementasi:**
- 📋 Tabel acara (tidak ada fitur delete di admin)
- 📋 Tabel admin (tidak ada fitur delete di admin)
- 📋 Riwayat pemesanan (hanya menampilkan data)
- 📋 Laporan penyewaan (hanya menampilkan data)

## Files yang Terlibat

- `sewa_gedung.sql` - Database structure dengan stored procedures
- `reset_auto_increment.php` - PHP helper functions
- `admin/ajax/delete_penyewa.php` - Delete handler untuk penyewa dengan auto reset
- `admin/ajax/delete_pemesanan.php` - Delete handler untuk pemesanan & pembayaran dengan auto reset
- `test_auto_increment.php` - Test script untuk penyewa
- `test_pemesanan_auto_increment.php` - Test script untuk pemesanan & pembayaran
- `test_complete_auto_increment.php` - Complete test untuk semua tabel
- `AUTO_INCREMENT_IMPLEMENTATION_GUIDE.md` - Dokumentasi lengkap

## Testing

### Test Individual
1. **Penyewa:** `http://localhost/sewa-gedung/test_auto_increment.php`
2. **Pemesanan & Pembayaran:** `http://localhost/sewa-gedung/test_pemesanan_auto_increment.php`

### Test Complete
**All-in-one:** `http://localhost/sewa-gedung/test_complete_auto_increment.php`

## Contoh Penggunaan

```php
// Di file yang menangani DELETE
require_once 'reset_auto_increment.php';

// Delete query
$stmt = $pdo->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
$result = $stmt->execute([$id]);

if ($result) {
    // Reset auto increment
    resetPenyewaAutoIncrement();
    
    echo "Data berhasil dihapus dan ID direset";
}
```

## Verifikasi

Setelah implementasi, pastikan:
1. Delete penyewa berhasil
2. ID baru mengisi gap yang kosong
3. Tidak ada error di log PHP
4. Test script menunjukkan hasil SUCCESS
