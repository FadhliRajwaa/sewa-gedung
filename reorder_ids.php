<?php
/**
 * Fungsi untuk mengatur ulang ID agar berurutan tanpa gap
 * Mengurutkan ulang semua ID dan mengatur AUTO_INCREMENT
 */

require_once 'config.php';

/**
 * Mengatur ulang ID penyewa agar berurutan
 */
function reorderPenyewaIds() {
    global $pdo;
    try {
        // Pastikan tidak ada transaksi yang sedang berjalan
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        $pdo->beginTransaction();
        
        // Ambil semua data penyewa dengan urutan berdasarkan id_penyewa
        $stmt = $pdo->query("SELECT * FROM penyewa ORDER BY id_penyewa ASC");
        $penyewaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($penyewaList)) {
            // Jika tidak ada data, set AUTO_INCREMENT ke 1
            $pdo->exec("ALTER TABLE penyewa AUTO_INCREMENT = 1");
            $pdo->commit();
            return true;
        }
        
        // Nonaktifkan foreign key checks sementara
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Update ID agar berurutan mulai dari 1
        $newId = 1;
        foreach ($penyewaList as $penyewa) {
            $oldId = $penyewa['id_penyewa'];
            
            if ($oldId != $newId) {
                // Update tabel penyewa
                $stmt = $pdo->prepare("UPDATE penyewa SET id_penyewa = ? WHERE id_penyewa = ?");
                $stmt->execute([$newId, $oldId]);
                
                // Update tabel pemesanan yang mengacu ke penyewa ini
                $stmt = $pdo->prepare("UPDATE pemesanan SET id_penyewa = ? WHERE id_penyewa = ?");
                $stmt->execute([$newId, $oldId]);
                
                // Update tabel verifikasi_email yang mengacu ke penyewa ini
                $stmt = $pdo->prepare("UPDATE verifikasi_email SET id_penyewa = ? WHERE id_penyewa = ?");
                $stmt->execute([$newId, $oldId]);
            }
            
            $newId++;
        }
        
        // Aktifkan kembali foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        // Set AUTO_INCREMENT ke nilai berikutnya
        $pdo->exec("ALTER TABLE penyewa AUTO_INCREMENT = $newId");
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1"); // Pastikan foreign key checks aktif kembali
        error_log("Error reordering penyewa IDs: " . $e->getMessage());
        return false;
    }
}

/**
 * Mengatur ulang ID pemesanan agar berurutan
 */
function reorderPemesananIds() {
    global $pdo;
    try {
        // Pastikan tidak ada transaksi yang sedang berjalan
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        $pdo->beginTransaction();
        
        // Ambil semua data pemesanan dengan urutan berdasarkan id_pemesanan
        $stmt = $pdo->query("SELECT * FROM pemesanan ORDER BY id_pemesanan ASC");
        $pemesananList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($pemesananList)) {
            // Jika tidak ada data, set AUTO_INCREMENT ke 1
            $pdo->exec("ALTER TABLE pemesanan AUTO_INCREMENT = 1");
            $pdo->commit();
            return true;
        }
        
        // Nonaktifkan foreign key checks sementara
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Update ID agar berurutan mulai dari 1
        $newId = 1;
        foreach ($pemesananList as $pemesanan) {
            $oldId = $pemesanan['id_pemesanan'];
            
            if ($oldId != $newId) {
                // Update tabel pemesanan
                $stmt = $pdo->prepare("UPDATE pemesanan SET id_pemesanan = ? WHERE id_pemesanan = ?");
                $stmt->execute([$newId, $oldId]);
                
                // Update tabel pembayaran yang mengacu ke pemesanan ini
                $stmt = $pdo->prepare("UPDATE pembayaran SET id_pemesanan = ? WHERE id_pemesanan = ?");
                $stmt->execute([$newId, $oldId]);
            }
            
            $newId++;
        }
        
        // Aktifkan kembali foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        // Set AUTO_INCREMENT ke nilai berikutnya
        $pdo->exec("ALTER TABLE pemesanan AUTO_INCREMENT = $newId");
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1"); // Pastikan foreign key checks aktif kembali
        error_log("Error reordering pemesanan IDs: " . $e->getMessage());
        return false;
    }
}

/**
 * Mengatur ulang ID pembayaran agar berurutan
 */
function reorderPembayaranIds() {
    global $pdo;
    try {
        // Pastikan tidak ada transaksi yang sedang berjalan
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        $pdo->beginTransaction();
        
        // Ambil semua data pembayaran dengan urutan berdasarkan id_pembayaran
        $stmt = $pdo->query("SELECT * FROM pembayaran ORDER BY id_pembayaran ASC");
        $pembayaranList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($pembayaranList)) {
            // Jika tidak ada data, set AUTO_INCREMENT ke 1
            $pdo->exec("ALTER TABLE pembayaran AUTO_INCREMENT = 1");
            $pdo->commit();
            return true;
        }
        
        // Update ID agar berurutan mulai dari 1
        $newId = 1;
        foreach ($pembayaranList as $pembayaran) {
            $oldId = $pembayaran['id_pembayaran'];
            
            if ($oldId != $newId) {
                // Update tabel pembayaran
                $stmt = $pdo->prepare("UPDATE pembayaran SET id_pembayaran = ? WHERE id_pembayaran = ?");
                $stmt->execute([$newId, $oldId]);
            }
            
            $newId++;
        }
        
        // Set AUTO_INCREMENT ke nilai berikutnya
        $pdo->exec("ALTER TABLE pembayaran AUTO_INCREMENT = $newId");
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        error_log("Error reordering pembayaran IDs: " . $e->getMessage());
        return false;
    }
}

/**
 * Mengatur ulang semua ID tabel utama
 */
function reorderAllIds() {
    $results = [
        'penyewa' => reorderPenyewaIds(),
        'pemesanan' => reorderPemesananIds(),
        'pembayaran' => reorderPembayaranIds()
    ];
    
    return $results;
}
?>
