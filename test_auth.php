<?php
// Test registration and login
require_once 'config.php';

echo "=== TESTING REGISTRATION ===\n";

// Test data
$test_user = [
    'tipe_penyewa' => 'individu',
    'nama_lengkap' => 'Test User',
    'nik' => '1234567890123456',
    'no_telepon' => '081234567899',
    'email' => 'testuser@email.com',
    'alamat' => 'Jl. Test No. 123',
    'username' => 'testuser123',
    'password' => 'password123'
];

try {
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM penyewa WHERE email = ? OR username = ?");
    $stmt->execute([$test_user['email'], $test_user['username']]);
    
    if ($stmt->fetchColumn() > 0) {
        echo "Test user already exists, deleting first...\n";
        $stmt = $pdo->prepare("DELETE FROM penyewa WHERE email = ? OR username = ?");
        $stmt->execute([$test_user['email'], $test_user['username']]);
    }
    
    // Insert new user
    $hashedPassword = password_hash($test_user['password'], PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("INSERT INTO penyewa (tipe_penyewa, nama_lengkap, nik, no_telepon, email, alamat, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([
        $test_user['tipe_penyewa'],
        $test_user['nama_lengkap'],
        $test_user['nik'],
        $test_user['no_telepon'],
        $test_user['email'],
        $test_user['alamat'],
        $test_user['username'],
        $hashedPassword
    ]);
    
    if ($success) {
        $id = $pdo->lastInsertId();
        echo "✅ User registered successfully with ID: $id\n";
        
        // Add verification token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        $stmt = $pdo->prepare("INSERT INTO verifikasi_email (id_penyewa, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$id, $token, $expires_at]);
        echo "✅ Verification token created\n";
        
        // Set email as verified for testing
        $stmt = $pdo->prepare("UPDATE penyewa SET email_terverifikasi = 1 WHERE id_penyewa = ?");
        $stmt->execute([$id]);
        echo "✅ Email marked as verified\n";
        
        // Test login
        echo "\n=== TESTING LOGIN ===\n";
        $stmt = $pdo->prepare("SELECT * FROM penyewa WHERE username = ?");
        $stmt->execute([$test_user['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($test_user['password'], $user['password'])) {
            echo "✅ Login test successful!\n";
            echo "User ID: " . $user['id_penyewa'] . "\n";
            echo "Username: " . $user['username'] . "\n";
            echo "Name: " . $user['nama_lengkap'] . "\n";
            echo "Email Verified: " . ($user['email_terverifikasi'] ? 'Yes' : 'No') . "\n";
        } else {
            echo "❌ Login test failed!\n";
        }
        
    } else {
        echo "❌ Registration failed\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING ADMIN LOGIN ===\n";
try {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify('admin123', $admin['password'])) {
        echo "✅ Admin login test successful!\n";
        echo "Admin ID: " . $admin['id_admin'] . "\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Name: " . $admin['nama_lengkap'] . "\n";
    } else {
        echo "❌ Admin login test failed!\n";
    }
} catch (PDOException $e) {
    echo "❌ Admin Error: " . $e->getMessage() . "\n";
}
?>
