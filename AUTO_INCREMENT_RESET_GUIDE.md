# Auto Increment Reset System

## Deskripsi
Sistem ini dibuat untuk memastikan ID pada semua tabel tetap berurutan walaupun ada data yang dihapus. Ketika data dengan ID tertentu dihapus, ID tersebut akan diisi kembali oleh data baru yang ditambahkan.

## Cara Kerja
1. **Stored Procedures**: Database memiliki stored procedures untuk setiap tabel yang akan mengatur ulang AUTO_INCREMENT
2. **PHP Helper Functions**: File `reset_auto_increment.php` berisi fungsi-fungsi PHP untuk memanggil stored procedures
3. **Manual Call**: Setiap kali melakukan DELETE, harus memanggil fungsi reset yang sesuai

## Files yang Terlibat
- `sewa_gedung.sql` - Database structure dengan stored procedures
- `reset_auto_increment.php` - Helper functions untuk reset auto increment
- `example_auto_increment_usage.php` - Contoh penggunaan

## Stored Procedures yang Tersedia
- `reset_acara_auto_increment()`
- `reset_admin_auto_increment()`
- `reset_pembayaran_auto_increment()`
- `reset_pemesanan_auto_increment()`
- `reset_penyewa_auto_increment()`
- `reset_verifikasi_email_auto_increment()`

## PHP Functions yang Tersedia
- `resetAcaraAutoIncrement()`
- `resetAdminAutoIncrement()`
- `resetPembayaranAutoIncrement()`
- `resetPemesananAutoIncrement()`
- `resetPenyewaAutoIncrement()`
- `resetVerifikasiEmailAutoIncrement()`
- `resetAllAutoIncrements()` - Reset semua tabel sekaligus

## Cara Penggunaan

### 1. Include Helper File
```php
require_once 'reset_auto_increment.php';
```

### 2. Setelah DELETE Operation
```php
// Contoh: Setelah menghapus penyewa
$stmt = $conn->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
$stmt->bind_param("i", $id_penyewa);
$stmt->execute();

// Reset auto increment
resetPenyewaAutoIncrement();
```

### 3. Dengan Transaction (Recommended)
```php
function deletePenyewa($id_penyewa) {
    global $conn;
    
    try {
        $conn->autocommit(FALSE);
        
        // Delete data
        $stmt = $conn->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
        $stmt->bind_param("i", $id_penyewa);
        $stmt->execute();
        
        // Reset auto increment
        resetPenyewaAutoIncrement();
        
        $conn->commit();
        $conn->autocommit(TRUE);
        
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        $conn->autocommit(TRUE);
        return false;
    }
}
```

## Contoh Skenario

### Before (Tanpa Reset)
1. Insert penyewa → ID: 1
2. Insert penyewa → ID: 2  
3. Insert penyewa → ID: 3
4. Delete penyewa ID: 2
5. Insert penyewa → ID: 4 ❌ (Ada gap di ID 2)

### After (Dengan Reset)
1. Insert penyewa → ID: 1
2. Insert penyewa → ID: 2
3. Insert penyewa → ID: 3
4. Delete penyewa ID: 2 + resetPenyewaAutoIncrement()
5. Insert penyewa → ID: 2 ✅ (Tidak ada gap)

## Implementasi di File Existing

### Untuk Admin Panel
Tambahkan di file admin yang menangani DELETE:
```php
// Di admin/data_penyewa.php atau similar
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Delete operation
    $stmt = $conn->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Reset auto increment setelah delete
        resetPenyewaAutoIncrement();
        echo "Data berhasil dihapus";
    }
}
```

### Untuk User Registration
Jika ada fitur user bisa delete akun sendiri:
```php
// Di profile atau account settings
if (isset($_POST['delete_account'])) {
    $id_penyewa = $_SESSION['id_penyewa'];
    
    // Delete account
    $stmt = $conn->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
    $stmt->bind_param("i", $id_penyewa);
    
    if ($stmt->execute()) {
        resetPenyewaAutoIncrement();
        session_destroy();
        header("Location: index.php");
    }
}
```

## Testing

### Test Reset Function
```php
// Test script
require_once 'reset_auto_increment.php';

echo "<h2>Testing Auto Increment Reset</h2>";

// Test reset semua tabel
$results = resetAllAutoIncrements();

foreach ($results as $table => $success) {
    echo "<p>Table {$table}: " . ($success ? "✅ Success" : "❌ Failed") . "</p>";
}
```

## Notes
- Pastikan `config.php` sudah ter-include dengan benar
- Fungsi ini harus dipanggil SETELAH operasi DELETE
- Gunakan transaction untuk memastikan konsistensi data
- Test terlebih dahulu di development environment

## Troubleshooting

### Error "Call to unknown method"
Pastikan menggunakan MySQLi, bukan PDO. Jika menggunakan PDO, ubah sintaks sesuai PDO.

### Error "Procedure not found"
Pastikan stored procedures sudah di-import dari file `sewa_gedung.sql`.

### Performance Issues
Jika ada concern performance, bisa membuat reset function yang lebih efisien atau hanya reset di waktu tertentu (misal: daily maintenance).
