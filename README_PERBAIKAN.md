# README - Perbaikan Project Sewa Gedung

## Masalah yang Diperbaiki:

### 1. ✅ Masalah SMTP (Error mail() function)
- **Masalah**: Error `mail(): Failed to connect to mailserver at "localhost" port 25`
- **Solusi**: Nonaktifkan verifikasi email untuk development dan otomatis set email sebagai terverifikasi

### 2. ✅ Masalah Login Tidak Bisa
- **Masalah**: Password verification tidak bekerja dengan benar
- **Solusi**: Perbaiki verifikasi password menggunakan `password_verify()` dan skip email verification untuk development

### 3. ✅ Masalah Database Connection
- **Masalah**: Inkonsistensi nama database antara config.php dan SQL file
- **Solusi**: Update config.php untuk menggunakan database `gedung_pt_aneka`

### 4. ✅ Perbaikan Tampilan UI/UX
- **Update**: Tampilan login dan registrasi sesuai dengan theme modern
- **Fitur**: Gradient background, logo placement, better form styling
- **Responsif**: Mobile-friendly design

### 5. ✅ Sistem Admin yang Proper
- **Update**: Admin login menggunakan PDO dan password hash
- **Keamanan**: Password admin di-hash dengan bcrypt
- **UI**: Tampilan admin login yang modern

## Langkah-langkah Setup:

### 1. Import Database
```sql
-- Import file: gedung_pt_aneka (3).sql ke MySQL/MariaDB
-- Nama database: gedung_pt_aneka
```

### 2. Jalankan Script Perbaikan Database
```
http://localhost/sewa-gedung/fix_database.php
```

### 3. Test Koneksi Database
```
http://localhost/sewa-gedung/test_database.php
```

### 4. Akses Aplikasi

#### User/Penyewa:
- **Registrasi**: http://localhost/sewa-gedung/register.php
- **Login**: http://localhost/sewa-gedung/login.php
- **Home**: http://localhost/sewa-gedung/index.php

#### Admin:
- **Login**: http://localhost/sewa-gedung/admin/login.php
- **Username**: will
- **Password**: will123
- **Atau Username**: admin, **Password**: admin123 (jika ditambahkan)

## Kredensial Default:

### Admin:
- Username: `will`
- Password: `will123`

### Test User (setelah registrasi):
- Buat akun baru melalui form registrasi
- Login langsung tanpa verifikasi email

## Fitur yang Sudah Diperbaiki:

1. ✅ **Registrasi User** - Bekerja tanpa SMTP error
2. ✅ **Login User** - Password verification diperbaiki
3. ✅ **Login Admin** - Sistem keamanan yang proper
4. ✅ **UI/UX Modern** - Sesuai dengan theme project
5. ✅ **Database Structure** - Kolom yang diperlukan sudah ditambahkan
6. ✅ **Error Handling** - Pesan error yang user-friendly

## File yang Dimodifikasi:

1. `config.php` - Database name consistency
2. `register.php` - UI improvement & SMTP fix
3. `login.php` - Password verification fix & UI
4. `admin/login.php` - Admin authentication fix & UI
5. `index.php` - Session management
6. `fix_database.php` - Database structure repair script
7. `test_database.php` - Database connection testing

## File Baru yang Ditambahkan:

1. `fix_database.php` - Script untuk perbaikan database
2. `test_database.php` - Script untuk test koneksi
3. `update_admin_password.php` - Script untuk update password admin

## Catatan Penting:

- Untuk production, aktifkan kembali email verification
- Ganti password default admin
- Konfigurasi SMTP yang proper untuk email notifications
- Backup database sebelum menjalankan script perbaikan

## Support:

Jika ada masalah, periksa:
1. XAMPP/WAMP running
2. Database imported correctly
3. File permissions
4. PHP error logs
