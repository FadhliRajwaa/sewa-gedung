<?php
/**
 * Helper functions to reset auto increment for all tables
 * Call these functions after DELETE operations to maintain sequential IDs
 */

require_once 'config.php';

/**
 * Reset auto increment for acara table
 */
function resetAcaraAutoIncrement() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("CALL reset_acara_auto_increment()");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error resetting acara auto increment: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset auto increment for admin table
 */
function resetAdminAutoIncrement() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("CALL reset_admin_auto_increment()");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error resetting admin auto increment: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset auto increment for pembayaran table
 */
function resetPembayaranAutoIncrement() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("CALL reset_pembayaran_auto_increment()");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error resetting pembayaran auto increment: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset auto increment for pemesanan table
 */
function resetPemesananAutoIncrement() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("CALL reset_pemesanan_auto_increment()");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error resetting pemesanan auto increment: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset auto increment for penyewa table
 */
function resetPenyewaAutoIncrement() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("CALL reset_penyewa_auto_increment()");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error resetting penyewa auto increment: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset auto increment for verifikasi_email table
 */
function resetVerifikasiEmailAutoIncrement() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("CALL reset_verifikasi_email_auto_increment()");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log("Error resetting verifikasi_email auto increment: " . $e->getMessage());
        return false;
    }
}

/**
 * Reset all table auto increments
 */
function resetAllAutoIncrements() {
    $results = [
        'acara' => resetAcaraAutoIncrement(),
        'admin' => resetAdminAutoIncrement(),
        'pembayaran' => resetPembayaranAutoIncrement(),
        'pemesanan' => resetPemesananAutoIncrement(),
        'penyewa' => resetPenyewaAutoIncrement(),
        'verifikasi_email' => resetVerifikasiEmailAutoIncrement()
    ];
    
    return $results;
}
?>
