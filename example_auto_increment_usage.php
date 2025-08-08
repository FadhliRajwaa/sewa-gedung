<?php
/**
 * Example usage of auto increment reset functions
 * This file shows how to properly use the reset functions after DELETE operations
 */

require_once 'reset_auto_increment.php';

// Contoh 1: Setelah menghapus data penyewa
function deletePenyewa($id_penyewa) {
    global $conn;
    
    try {
        // Mulai transaction
        $conn->autocommit(FALSE);
        
        // Hapus data penyewa
        $stmt = $conn->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
        $stmt->bind_param("i", $id_penyewa);
        $stmt->execute();
        
        // Reset auto increment untuk penyewa
        resetPenyewaAutoIncrement();
        
        // Commit transaction
        $conn->commit();
        $conn->autocommit(TRUE);
        
        return true;
    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        $conn->autocommit(TRUE);
        error_log("Error deleting penyewa: " . $e->getMessage());
        return false;
    }
}

// Contoh 2: Setelah menghapus data acara
function deleteAcara($id_acara) {
    global $conn;
    
    try {
        // Mulai transaction
        $conn->autocommit(FALSE);
        
        // Hapus data acara
        $stmt = $conn->prepare("DELETE FROM acara WHERE id_acara = ?");
        $stmt->bind_param("i", $id_acara);
        $stmt->execute();
        
        // Reset auto increment untuk acara
        resetAcaraAutoIncrement();
        
        // Commit transaction
        $conn->commit();
        $conn->autocommit(TRUE);
        
        return true;
    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        $conn->autocommit(TRUE);
        error_log("Error deleting acara: " . $e->getMessage());
        return false;
    }
}

// Contoh 3: Setelah menghapus data pemesanan
function deletePemesanan($id_pemesanan) {
    global $conn;
    
    try {
        // Mulai transaction
        $conn->autocommit(FALSE);
        
        // Hapus data pemesanan (akan otomatis hapus pembayaran karena CASCADE)
        $stmt = $conn->prepare("DELETE FROM pemesanan WHERE id_pemesanan = ?");
        $stmt->bind_param("i", $id_pemesanan);
        $stmt->execute();
        
        // Reset auto increment untuk pemesanan dan pembayaran
        resetPemesananAutoIncrement();
        resetPembayaranAutoIncrement();
        
        // Commit transaction
        $conn->commit();
        $conn->autocommit(TRUE);
        
        return true;
    } catch (Exception $e) {
        // Rollback jika ada error
        $conn->rollback();
        $conn->autocommit(TRUE);
        error_log("Error deleting pemesanan: " . $e->getMessage());
        return false;
    }
}

// Contoh 4: Reset semua auto increment sekaligus
function resetAllTableAutoIncrements() {
    $results = resetAllAutoIncrements();
    
    echo "<h3>Auto Increment Reset Results:</h3>";
    foreach ($results as $table => $success) {
        $status = $success ? "✓ Success" : "✗ Failed";
        echo "<p>{$table}: {$status}</p>";
    }
    
    return $results;
}

// Contoh penggunaan:
/*
// Hapus penyewa dengan ID 1
if (deletePenyewa(1)) {
    echo "Penyewa berhasil dihapus dan auto increment direset";
} else {
    echo "Gagal menghapus penyewa";
}

// Reset semua auto increment
resetAllTableAutoIncrements();
*/
?>
