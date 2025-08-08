# 🚀 Panduan Upload ke Hosting

## ❌ Masalah yang Terjadi
Error `#1044 - Access denied for user 'b7_39639306'@'192.168.%' to database 'b7_39639306_gedung'` terjadi karena:
- User database di hosting tidak memiliki privilege untuk membuat stored procedures
- File SQL original mengandung `DEFINER` dan `CREATE PROCEDURE` yang memerlukan hak akses khusus

## ✅ Solusi - Gunakan File SQL Hosting

### 1. File SQL Bersih untuk Hosting
Gunakan file **`sewa_gedung_hosting.sql`** yang sudah dibersihkan dari:
- ❌ Stored procedures
- ❌ DEFINER statements  
- ❌ Privilege yang memerlukan akses root
- ✅ Hanya struktur tabel dan data

### 2. Langkah-langkah Upload

#### Step 1: Upload File Website
1. **Zip semua file** kecuali `sewa_gedung.sql` (gunakan `sewa_gedung_hosting.sql`)
2. **Upload ke hosting** via File Manager atau FTP
3. **Extract** file di folder public_html atau domain folder

#### Step 2: Konfigurasi Database
1. **Login ke cPanel/Hosting Panel**
2. **Buka phpMyAdmin**
3. **Pilih database** `b7_39639306_gedung`
4. **Import** file `sewa_gedung_hosting.sql`

#### Step 3: Update Konfigurasi
1. **Rename** `config_hosting.php` menjadi `config.php`
2. **Edit** detail database di `config.php`:

```php
$host = 'sql205.byethost7.com'; // Sesuaikan dengan hosting Anda
$dbname = 'b7_39639306_gedung'; 
$username = 'b7_39639306';
$password = 'PASSWORD_DATABASE_ANDA'; // Ganti dengan password sebenarnya
$base_url = 'https://yourdomain.byethost7.com/'; // Ganti dengan domain Anda
```

### 3. Detail Database Hosting Byethost7

**Informasi yang biasanya diberikan hosting:**
- **MySQL Host:** `sql205.byethost7.com`
- **Database Name:** `b7_39639306_gedung`
- **Username:** `b7_39639306`
- **Password:** `[Sesuai yang Anda set]`

### 4. Testing Koneksi

Setelah upload, test dengan mengakses:
- ✅ `https://yourdomain.byethost7.com/` - Homepage
- ✅ `https://yourdomain.byethost7.com/login.php` - Login admin
- ✅ `https://yourdomain.byethost7.com/admin/` - Panel admin

**Login Admin Default:**
- Username: `admin`
- Password: `password` (atau sesuai yang diset)

### 5. Struktur File Upload

```
public_html/
├── admin/
│   ├── dashboard.php
│   ├── data_pemesanan.php
│   └── ... (semua file admin)
├── asset/
├── includes/
├── uploads/
├── config.php ← (dari config_hosting.php)
├── index.php
├── login.php
├── register.php
├── gedung.php
├── dashboard_user.php
├── acara_saya.php
├── panduan.php
├── akun.php
└── ... (file lainnya)
```

### 6. Troubleshooting

**Jika masih error saat import:**
1. **Buka file** `sewa_gedung_hosting.sql`
2. **Hapus baris** yang mengandung:
   - `CREATE PROCEDURE`
   - `DEFINER=`
   - `DELIMITER`

**Jika error koneksi database:**
1. **Cek** detail database di cPanel
2. **Pastikan** password database benar
3. **Test** koneksi dengan script sederhana

**Jika error file upload:**
1. **Set permission** folder `uploads/` ke 755 atau 777
2. **Cek** ukuran file maksimal di hosting

### 7. Script Test Koneksi

Buat file `test_db.php` untuk testing:

```php
<?php
$host = 'sql205.byethost7.com';
$dbname = 'b7_39639306_gedung';
$username = 'b7_39639306';
$password = 'YOUR_PASSWORD';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "✅ Koneksi database berhasil!";
    
    $stmt = $pdo->query("SHOW TABLES");
    echo "<br>📋 Tabel yang ada:<br>";
    while ($row = $stmt->fetch()) {
        echo "- " . $row[0] . "<br>";
    }
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

### 8. Keamanan Hosting

Setelah upload selesai:
1. **Hapus** file `test_db.php`
2. **Hapus** file `sewa_gedung_hosting.sql`
3. **Set** permission folder sensitif
4. **Update** password admin

---

## 📞 Support

Jika masih ada masalah:
1. **Cek error log** di cPanel
2. **Hubungi** support hosting
3. **Pastikan** semua requirement PHP terpenuhi

**Happy hosting! 🎉**
