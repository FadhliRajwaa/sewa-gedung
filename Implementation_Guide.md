# 🔧 PANDUAN IMPLEMENTASI & TROUBLESHOOTING

## 🚀 QUICK START GUIDE

### Langkah 1: Setup Environment
```bash
# Clone atau download project
git clone [repository-url]
cd sewa-gedung

# Setup web server (XAMPP recommended)
# Pastikan Apache dan MySQL running
```

### Langkah 2: Database Setup
```sql
-- 1. Buat database baru
CREATE DATABASE gedung_pt_aneka;

-- 2. Import struktur database
mysql -u root -p gedung_pt_aneka < gedung_pt_aneka_complete.sql

-- 3. Verifikasi tabel tercipta
SHOW TABLES;
```

### Langkah 3: Configuration
```php
// config.php - Sesuaikan dengan environment
$host = 'localhost';
$dbname = 'gedung_pt_aneka';
$username = 'root';
$password = ''; // XAMPP default kosong

// Test koneksi
php test_database.php
```

### Langkah 4: Testing
1. Akses `http://localhost/sewa-gedung`
2. Test registrasi user baru
3. Test login dan booking
4. Test admin dashboard

## 🐛 TROUBLESHOOTING GUIDE

### Problem 1: Database Connection Error
```
Error: "SQLSTATE[HY000] [1049] Unknown database"
```
**Solution:**
```sql
-- Pastikan database sudah dibuat
CREATE DATABASE gedung_pt_aneka;
USE gedung_pt_aneka;

-- Import ulang struktur
SOURCE gedung_pt_aneka_complete.sql;
```

### Problem 2: Session Issues
```
Error: "Headers already sent"
```
**Solution:**
```php
// Pastikan tidak ada output sebelum session_start()
<?php
session_start(); // Harus di line pertama
// Tidak ada echo/print/HTML sebelum ini
```

### Problem 3: Upload File Error
```
Error: "Failed to move uploaded file"
```
**Solution:**
```bash
# Set permission untuk upload folder
chmod 755 uploads/
chown www-data:www-data uploads/

# Atau di Windows/XAMPP
# Klik kanan folder uploads -> Properties -> Security -> Full Control
```

### Problem 4: JavaScript Errors
```
Error: "Cannot read property of null"
```
**Solution:**
```javascript
// Pastikan element ada sebelum mengakses
const element = document.getElementById('myId');
if (element) {
    element.addEventListener('click', function() {
        // Your code here
    });
}
```

### Problem 5: Email Verification Not Working
```
Error: "Email tidak terkirim"
```
**Solution:**
```php
// Setup SMTP di config.php
$mail_host = 'smtp.gmail.com';
$mail_port = 587;
$mail_username = 'your-email@gmail.com';
$mail_password = 'your-app-password';

// Atau gunakan mail() function untuk testing
if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully";
}
```

## 🔄 COMMON WORKFLOWS

### Workflow 1: Adding New Event Type
```php
// 1. Tambah data di tabel acara
INSERT INTO acara (nama_acara, kapasitas, harga, lokasi, fasilitas) 
VALUES ('Workshop', 50, 500000, 'Ruang B', 'Proyektor, AC, Sound System');

// 2. Buat halaman event baru (misal: workshop.php)
// Copy dari seminar.php dan sesuaikan

// 3. Update navigation di includes/navbar.php
<a class="nav-link" href="workshop.php">Workshop</a>

// 4. Test booking flow
```

### Workflow 2: Modifying Price Calculation
```php
// includes/pricing.php
function calculatePrice($id_acara, $durasi) {
    // Tambah logic khusus jika perlu
    $base_price = getBasePrice($id_acara);
    
    // Diskon untuk booking lebih dari 3 hari
    if ($durasi > 3) {
        $discount = 0.1; // 10% discount
        $base_price *= (1 - $discount);
    }
    
    return $base_price * $durasi;
}
```

### Workflow 3: Adding New Payment Method
```php
// 1. Update enum di database
ALTER TABLE pemesanan 
MODIFY COLUMN metode_pembayaran 
ENUM('QRIS','Transfer_BCA','Transfer_BNI','Transfer_BRI','Transfer_Mandiri','GoPay','OVO');

// 2. Update form di proses_sewa.php
<option value="GoPay">GoPay</option>
<option value="OVO">OVO</option>

// 3. Update validation
```

## 📊 MONITORING & ANALYTICS

### Database Performance Monitoring
```sql
-- Cek query yang lambat
SHOW PROCESSLIST;

-- Analyze table performance
ANALYZE TABLE pemesanan;

-- Check index usage
EXPLAIN SELECT * FROM pemesanan WHERE tanggal_sewa = '2024-01-01';
```

### System Health Check
```php
// health_check.php
<?php
// Database connectivity
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "✅ Database: Connected\n";
} catch (PDOException $e) {
    echo "❌ Database: Failed\n";
}

// File permissions
if (is_writable('uploads/')) {
    echo "✅ Upload folder: Writable\n";
} else {
    echo "❌ Upload folder: Not writable\n";
}

// Session functionality
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Sessions: Working\n";
} else {
    echo "❌ Sessions: Failed\n";
}
?>
```

### User Activity Tracking
```php
// activity_logger.php
function logUserActivity($user_id, $action, $page) {
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $user_id,
        'action' => $action,
        'page' => $page,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    ];
    
    file_put_contents('logs/user_activity.json', 
        json_encode($log_data) . "\n", FILE_APPEND);
}

// Usage dalam setiap halaman
logUserActivity($_SESSION['id_penyewa'], 'page_view', basename($_SERVER['PHP_SELF']));
```

## 🔐 SECURITY CHECKLIST

### Authentication Security
- [x] Password hashing dengan bcrypt
- [x] Session regeneration setelah login
- [x] Logout clear session
- [x] Session timeout handling
- [ ] Two-factor authentication (future)
- [ ] Account lockout after failed attempts

### Input Validation
- [x] SQL injection prevention (prepared statements)
- [x] XSS prevention (htmlspecialchars)
- [x] File upload validation
- [x] Email format validation
- [ ] CSRF token implementation
- [ ] Rate limiting

### Data Protection
- [x] Sensitive data tidak di-log
- [x] Upload file type restriction
- [x] Database connection encryption
- [ ] Data encryption at rest
- [ ] GDPR compliance
- [ ] Backup encryption

## 📱 MOBILE OPTIMIZATION

### Responsive Design Checklist
- [x] Bootstrap responsive grid
- [x] Mobile-friendly navigation
- [x] Touch-friendly buttons
- [x] Readable font sizes
- [ ] Swipe gestures
- [ ] Progressive Web App (PWA)

### Performance Mobile
```css
/* Optimize untuk mobile */
@media (max-width: 768px) {
    .hero-section {
        background-attachment: scroll; /* Bukan fixed */
    }
    
    .card-img-top {
        max-height: 200px;
        object-fit: cover;
    }
}
```

## 🎯 FUTURE ENHANCEMENTS

### Phase 1: Core Improvements
- [ ] Real-time availability checker
- [ ] Email notification system
- [ ] PDF invoice generation
- [ ] Payment gateway integration

### Phase 2: Advanced Features
- [ ] Calendar integration
- [ ] SMS notifications
- [ ] Multi-language support
- [ ] API for mobile app

### Phase 3: Business Intelligence
- [ ] Analytics dashboard
- [ ] Revenue reporting
- [ ] Customer insights
- [ ] Predictive booking

## 📋 MAINTENANCE SCHEDULE

### Daily Tasks
- Check system health
- Monitor error logs
- Backup database
- Review user activity

### Weekly Tasks
- Update dependencies
- Clean temporary files
- Archive old logs
- Performance analysis

### Monthly Tasks
- Security audit
- Database optimization
- User feedback review
- Feature planning

### Quarterly Tasks
- Full system backup
- Security penetration test
- Code review and refactoring
- Technology stack update

## 📞 SUPPORT CONTACTS

### Technical Issues
- **Developer**: [your-email@domain.com]
- **System Admin**: [admin@domain.com]
- **Database**: [dba@domain.com]

### Business Issues
- **Product Owner**: [po@domain.com]
- **Customer Support**: [support@domain.com]

### Emergency Contacts
- **24/7 Hotline**: +62-xxx-xxxx-xxxx
- **Emergency Email**: [emergency@domain.com]

---

**📝 Note**: Dokumentasi ini akan diupdate seiring dengan perkembangan sistem. Pastikan selalu menggunakan versi terbaru.
