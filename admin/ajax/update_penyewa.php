<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id = $_POST['id'] ?? 0;
$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$telepon = $_POST['telepon'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$jenis = $_POST['jenis'] ?? '';

if (empty($nama) || empty($email) || empty($telepon) || empty($alamat) || empty($jenis)) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
    exit;
}

try {
    // Check if email already exists for other users
    $checkQuery = "SELECT id_penyewa FROM penyewa WHERE email = ? AND id_penyewa != ?";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([$email, $id]);
    
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email sudah digunakan oleh penyewa lain']);
        exit;
    }
    
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($jenis === 'instansi') {
            $query = "UPDATE penyewa SET nama_instansi = ?, email = ?, password = ?, no_telepon = ?, alamat = ?, tipe_penyewa = ? WHERE id_penyewa = ?";
        } else {
            $query = "UPDATE penyewa SET nama_lengkap = ?, email = ?, password = ?, no_telepon = ?, alamat = ?, tipe_penyewa = ? WHERE id_penyewa = ?";
        }
        $params = [$nama, $email, $hashedPassword, $telepon, $alamat, $jenis, $id];
    } else {
        if ($jenis === 'instansi') {
            $query = "UPDATE penyewa SET nama_instansi = ?, email = ?, no_telepon = ?, alamat = ?, tipe_penyewa = ? WHERE id_penyewa = ?";
        } else {
            $query = "UPDATE penyewa SET nama_lengkap = ?, email = ?, no_telepon = ?, alamat = ?, tipe_penyewa = ? WHERE id_penyewa = ?";
        }
        $params = [$nama, $email, $telepon, $alamat, $jenis, $id];
    }
    
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute($params);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Penyewa berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate penyewa']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
