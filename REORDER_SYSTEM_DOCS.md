# 📋 Sistem Pengurutan Ulang ID Database

## 🎯 Tujuan
Sistem ini memastikan bahwa semua ID dalam database selalu berurutan tanpa gap, bahkan setelah penghapusan data. Jika Anda memiliki data dengan ID 1, 2, 3 dan menghapus ID 2, maka ID 3 akan berubah menjadi ID 2, sehingga data baru akan mendapat ID 3 (bukan ID 4).

## 🔧 File-file yang Terlibat

### 1. `reorder_ids.php`
File utama yang berisi fungsi-fungsi untuk mengatur ulang ID:
- `reorderPenyewaIds()` - Mengatur ulang ID penyewa
- `reorderPemesananIds()` - Mengatur ulang ID pemesanan  
- `reorderPembayaranIds()` - Mengatur ulang ID pembayaran
- `reorderAllIds()` - Mengatur ulang semua ID sekaligus

### 2. `admin/ajax/delete_penyewa.php`
Handler untuk menghapus penyewa dengan auto-reorder ID

### 3. `admin/ajax/delete_pemesanan.php`
Handler untuk menghapus pemesanan dengan auto-reorder ID

### 4. `manual_reorder.php`
Tool web untuk mengatur ulang ID secara manual

### 5. `test_reorder_ids.php`
File untuk testing fungsi reorder

## 🚀 Cara Kerja

### Otomatis (Saat Menghapus Data)
1. User menghapus data melalui admin panel
2. Data dihapus dari database
3. Sistem otomatis menjalankan reorder ID
4. Semua ID menjadi berurutan tanpa gap

### Manual (Menggunakan Tool)
1. Buka `http://localhost/sewa-gedung/manual_reorder.php`
2. Pilih tabel yang ingin diurutkan ulang
3. Klik tombol untuk menjalankan reorder
4. ID akan diurutkan ulang secara otomatis

## 📊 Contoh Cara Kerja

### Sebelum Reorder:
```
Penyewa: ID 1, 3, 5, 8
AUTO_INCREMENT: 9
Data baru akan mendapat ID: 9
```

### Sesudah Reorder:
```
Penyewa: ID 1, 2, 3, 4
AUTO_INCREMENT: 5  
Data baru akan mendapat ID: 5
```

## 🔗 Relasi Foreign Key
Sistem ini menangani relasi foreign key dengan benar:

```
penyewa (id_penyewa)
   ↓
pemesanan (id_penyewa, id_pemesanan)
   ↓
pembayaran (id_pemesanan)
```

Ketika ID diurutkan ulang, semua referensi foreign key juga ikut diupdate.

## 🛡️ Keamanan
- Menggunakan transaksi database untuk konsistensi
- Menonaktifkan foreign key checks sementara saat reorder
- Error handling yang lengkap
- Rollback otomatis jika terjadi error

## 📱 Penggunaan di Admin Panel

### Data Penyewa
- URL: `http://localhost/sewa-gedung/admin/data_penyewa.php`
- Saat menghapus penyewa, ID otomatis diurutkan ulang

### Data Pemesanan  
- URL: `http://localhost/sewa-gedung/admin/data_pemesanan.php`
- Saat menghapus pemesanan, ID pemesanan dan pembayaran diurutkan ulang

### Riwayat Pemesanan
- URL: `http://localhost/sewa-gedung/admin/riwayat_pemesanan.php`
- Hanya menampilkan data, tidak ada fungsi hapus

## ⚙️ Konfigurasi

### Requirements
- PHP 7.4+
- MySQL/MariaDB dengan PDO
- Foreign key constraints aktif

### Setup
1. Import `sewa_gedung.sql` ke database
2. Pastikan semua file PHP ada di direktori yang benar
3. Sistem siap digunakan

## 🧪 Testing

### Test Otomatis
Jalankan `test_reorder_ids.php` untuk melihat hasil reorder:
```
http://localhost/sewa-gedung/test_reorder_ids.php
```

### Test Manual
1. Tambah beberapa data penyewa/pemesanan
2. Hapus data di tengah (misal ID 2 dari 1,2,3,4)
3. Lihat bahwa ID berubah menjadi 1,2,3
4. Tambah data baru, akan mendapat ID 4

## 🎯 Hasil Akhir

✅ **ID selalu berurutan tanpa gap**  
✅ **Foreign key relationships tetap konsisten**  
✅ **Proses otomatis saat menghapus data**  
✅ **Tool manual untuk maintenance**  
✅ **Error handling yang robust**  

## 📝 Catatan Penting

1. **Backup Database**: Selalu backup sebelum menjalankan reorder manual
2. **Concurrent Access**: Hindari akses bersamaan saat reorder berjalan
3. **Performance**: Reorder bisa memakan waktu untuk dataset besar
4. **Logging**: Error akan dicatat di error log PHP

## 🔄 Maintenance

Jika diperlukan, Anda bisa menjalankan reorder manual secara berkala:
1. Buka `manual_reorder.php`
2. Klik "Urutkan Semua ID"
3. Semua tabel akan diurutkan ulang

---

**✨ Sistem ini memastikan database Anda selalu rapi dan terorganisir!**
