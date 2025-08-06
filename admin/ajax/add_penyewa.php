<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$telepon = $_POST['telepon'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$jenis = $_POST['jenis'] ?? '';

if (empty($nama) || empty($email) || empty($password) || empty($telepon) || empty($alamat) || empty($jenis)) {
    echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
    exit;
}

try {
    // Check if email already exists
    $checkQuery = "SELECT id_penyewa FROM penyewa WHERE email = ?";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
        exit;
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    if ($jenis === 'instansi') {
        $query = "INSERT INTO penyewa (nama_instansi, email, password, no_telepon, alamat, tipe_penyewa, username) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $username = strtolower(str_replace(' ', '', $nama)) . rand(100, 999);
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$nama, $email, $hashedPassword, $telepon, $alamat, $jenis, $username]);
    } else {
        $query = "INSERT INTO penyewa (nama_lengkap, email, password, no_telepon, alamat, tipe_penyewa, username) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $username = strtolower(str_replace(' ', '', $nama)) . rand(100, 999);
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([$nama, $email, $hashedPassword, $telepon, $alamat, $jenis, $username]);
    }
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Penyewa berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan penyewa']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
