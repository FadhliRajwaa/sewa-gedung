# 📋 DOKUMENTASI TEKNIS SISTEM SEWA GEDUNG

## 🎯 OVERVIEW SISTEM

Sistem Sewa Gedung PT Aneka adalah aplikasi web yang memungkinkan penyewa (individu/instansi) untuk memesan gedung/ruangan untuk berbagai acara dengan sistem pembayaran dan verifikasi yang terintegrasi.

## 🏗️ ARSITEKTUR SISTEM

### Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript ES6+, Bootstrap 4.5.2
- **Backend**: PHP 8.x dengan PDO
- **Database**: MySQL/MariaDB
- **Web Server**: Apache/Nginx
- **Additional**: SweetAlert2, FontAwesome, Inter Font

### Design Pattern
- **MVC Pattern**: Separation of concerns
- **Session-based Authentication**: Secure user management
- **RESTful Approach**: Clean URL structure

## 🔄 ALUR PROSES BISNIS

### 1. REGISTRASI PENYEWA
```
1. User mengakses halaman registrasi
2. Pilih tipe: Individu atau Instansi
3. Input data sesuai tipe yang dipilih
4. Submit form registrasi
5. Sistem generate token verifikasi
6. Kirim email verifikasi
7. User klik link verifikasi
8. Akun aktif dan bisa login
```

### 2. PROSES PEMESANAN
```
1. User login ke sistem
2. Browse halaman acara/gedung
3. Pilih jenis acara (Seminar/Rapat/Pernikahan)
4. Pilih tanggal mulai dan selesai
5. Sistem cek ketersediaan
6. Tampilkan harga dan total
7. Konfirmasi pemesanan
8. Generate data pemesanan
9. Redirect ke halaman pembayaran
```

### 3. PROSES PEMBAYARAN
```
1. User lihat detail pembayaran
2. Pilih metode pembayaran
3. Upload bukti transfer
4. Admin verifikasi pembayaran
5. Update status menjadi "Lunas"
6. Kirim konfirmasi ke user
```

### 4. MANAJEMEN ADMIN
```
1. Admin login ke dashboard
2. Monitor pemesanan baru
3. Verifikasi bukti pembayaran
4. Update status pembayaran
5. Generate laporan
6. Kelola data acara/gedung
```

## 🗄️ STRUKTUR DATABASE DETAIL

### Tabel PENYEWA
```sql
-- Menyimpan data customer (individu & instansi)
CREATE TABLE penyewa (
    id_penyewa INT PRIMARY KEY AUTO_INCREMENT,
    tipe_penyewa ENUM('individu','instansi'),
    nama_instansi VARCHAR(100) NULL,        -- Untuk tipe instansi
    nama_lengkap VARCHAR(100) NULL,         -- Untuk tipe individu  
    nik VARCHAR(20) NULL,                   -- Untuk tipe individu
    no_telepon VARCHAR(15) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    alamat TEXT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,         -- bcrypt hashed
    email_terverifikasi TINYINT DEFAULT 0
);
```

### Tabel ACARA
```sql
-- Master data jenis acara/gedung
CREATE TABLE acara (
    id_acara INT PRIMARY KEY AUTO_INCREMENT,
    nama_acara VARCHAR(100) NOT NULL,       -- Seminar, Rapat, Pernikahan, dll
    kapasitas INT NOT NULL,                 -- Maksimal orang
    harga DECIMAL(15,2) NOT NULL,          -- Harga per hari
    lokasi VARCHAR(100) NOT NULL,          -- Lokasi gedung
    status ENUM('tersedia','tidak tersedia') DEFAULT 'tersedia',
    fasilitas TEXT                         -- Daftar fasilitas
);
```

### Tabel PEMESANAN
```sql
-- Transaksi pemesanan
CREATE TABLE pemesanan (
    id_pemesanan INT PRIMARY KEY AUTO_INCREMENT,
    id_penyewa INT NOT NULL,
    id_acara INT NOT NULL,
    tanggal_sewa DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    durasi INT NOT NULL,                   -- Dihitung otomatis
    kebutuhan_tambahan TEXT,               -- Optional
    total DECIMAL(15,2) NOT NULL,          -- Harga × durasi
    metode_pembayaran ENUM('QRIS','Transfer_BCA','Transfer_BNI','Transfer_BRI','Transfer_Mandiri'),
    tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipe_pesanan ENUM('online','offline') DEFAULT 'online',
    FOREIGN KEY (id_penyewa) REFERENCES penyewa(id_penyewa) ON DELETE CASCADE,
    FOREIGN KEY (id_acara) REFERENCES acara(id_acara) ON DELETE CASCADE
);
```

### Tabel PEMBAYARAN
```sql
-- Data pembayaran dan bukti transfer
CREATE TABLE pembayaran (
    id_pembayaran INT PRIMARY KEY AUTO_INCREMENT,
    id_pemesanan INT NOT NULL,
    bukti_pembayaran VARCHAR(255),         -- Path file upload
    tanggal_upload DATETIME,
    status_pembayaran ENUM('Lunas','Belum Lunas') DEFAULT 'Belum Lunas',
    FOREIGN KEY (id_pemesanan) REFERENCES pemesanan(id_pemesanan) ON DELETE CASCADE
);
```

## 🔐 SISTEM KEAMANAN

### 1. Authentication & Authorization
```php
// Session-based authentication
session_start();
if (!isset($_SESSION['id_penyewa'])) {
    header("Location: login.php");
    exit();
}

// Role-based access (Admin vs User)
if (!isset($_SESSION['admin_logged_in'])) {
    // Admin only pages
}
```

### 2. Password Security
```php
// Hashing saat registrasi
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Verifikasi saat login
if (password_verify($input_password, $stored_hash)) {
    // Login berhasil
}
```

### 3. SQL Injection Prevention
```php
// Prepared statements
$stmt = $pdo->prepare("SELECT * FROM penyewa WHERE email = ? AND password = ?");
$stmt->execute([$email, $password]);
```

### 4. Input Validation
```php
// Sanitasi input
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
```

## 📊 BUSINESS RULES

### 1. Aturan Pemesanan
- Tanggal pemesanan tidak boleh kurang dari hari ini
- Tanggal selesai harus sama atau setelah tanggal mulai
- Tidak boleh double booking untuk tanggal yang sama
- Durasi dihitung otomatis (tanggal_selesai - tanggal_mulai + 1)

### 2. Aturan Pembayaran
- Setiap pemesanan wajib memiliki pembayaran
- Status default: "Belum Lunas"
- Admin harus verifikasi bukti pembayaran
- Setelah lunas, pemesanan dikonfirmasi

### 3. Aturan Verifikasi Email
- Token berlaku 7 hari sejak dibuat
- Setelah verifikasi, akun aktif dan bisa login
- Token expired otomatis dihapus

## 🎨 UI/UX DESIGN SYSTEM

### Color Palette
```css
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --light-bg: #f8f9fa;
    --dark-text: #333;
}
```

### Typography
```css
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.6;
    color: var(--dark-text);
}
```

### Component Structure
- **Cards**: Rounded corners, subtle shadows
- **Buttons**: Gradient backgrounds, hover effects
- **Forms**: Clean inputs, validation feedback
- **Modals**: SweetAlert2 for modern alerts

## 📱 RESPONSIVE DESIGN

### Breakpoints
```css
/* Mobile First Approach */
@media (max-width: 480px) { /* Mobile */ }
@media (max-width: 768px) { /* Tablet */ }
@media (min-width: 769px) { /* Desktop */ }
```

### Grid System
- Bootstrap 4.5.2 grid system
- Flexbox untuk alignment
- CSS Grid untuk complex layouts

## 🚀 DEPLOYMENT GUIDE

### Requirements
- PHP 8.0+
- MySQL 5.7+ atau MariaDB 10.3+
- Apache 2.4+ dengan mod_rewrite
- PHP Extensions: PDO, mysqli, mbstring, openssl

### Installation Steps
1. Upload files ke web server
2. Import database dari `gedung_pt_aneka_complete.sql`
3. Configure `config.php` dengan database credentials
4. Set permissions untuk upload folder (755)
5. Test sistem dengan sample data

### Production Checklist
- [ ] Enable HTTPS/SSL
- [ ] Set secure session configuration
- [ ] Configure email server for verification
- [ ] Set up backup system
- [ ] Enable error logging
- [ ] Optimize database indexes

## 📈 PERFORMANCE OPTIMIZATION

### Database Optimization
```sql
-- Index untuk query yang sering digunakan
CREATE INDEX idx_pemesanan_tanggal ON pemesanan(tanggal_sewa, tanggal_selesai);
CREATE INDEX idx_penyewa_email ON penyewa(email);
CREATE INDEX idx_pembayaran_status ON pembayaran(status_pembayaran);
```

### Caching Strategy
- Session caching untuk user data
- Database query optimization
- Static file caching (CSS, JS, images)

### File Upload Optimization
```php
// Limit file size dan type
$allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
$max_file_size = 5 * 1024 * 1024; // 5MB
```

## 🔍 TESTING SCENARIOS

### User Registration Test
1. Test registrasi individu
2. Test registrasi instansi
3. Test validasi email unique
4. Test verifikasi email

### Booking Process Test
1. Test pemilihan tanggal
2. Test konflik jadwal
3. Test kalkulasi harga
4. Test pembayaran upload

### Admin Functions Test
1. Test login admin
2. Test verifikasi pembayaran
3. Test generate laporan
4. Test kelola acara

## 📞 SUPPORT & MAINTENANCE

### Error Handling
```php
// Global error handler
try {
    // Database operations
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Terjadi kesalahan sistem. Silakan coba lagi.");
}
```

### Logging System
```php
// Custom logging function
function logActivity($user_id, $action, $details) {
    $log = date('Y-m-d H:i:s') . " - User: $user_id - Action: $action - Details: $details\n";
    file_put_contents('logs/activity.log', $log, FILE_APPEND);
}
```

### Backup Strategy
- Daily database backup
- Weekly full system backup
- Monthly archive cleanup
- Real-time replication for critical data
