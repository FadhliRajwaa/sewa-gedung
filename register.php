<?php
require_once 'config.php';

function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipe_penyewa = sanitize($_POST['tipe_penyewa']);
    $nama_instansi = $tipe_penyewa === 'instansi' ? sanitize($_POST['nama_instansi']) : NULL;
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $nik = sanitize($_POST['nik']);
    $no_telepon = sanitize($_POST['no_telepon']);
    $email = sanitize($_POST['email']);
    $alamat = sanitize($_POST['alamat']);
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if (!password_verify($konfirmasi_password, $password)) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM penyewa WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Email atau username sudah digunakan.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO penyewa (tipe_penyewa, nama_instansi, nama_lengkap, nik, no_telepon, email, alamat, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $success = $stmt->execute([
                $tipe_penyewa,
                $nama_instansi,
                $nama_lengkap,
                $nik,
                $no_telepon,
                $email,
                $alamat,
                $username,
                $password
            ]);

            if ($success) {
                $id = $pdo->lastInsertId();
                
                try {
                    // Check if verifikasi_email table exists, if not create it
                    $pdo->exec("CREATE TABLE IF NOT EXISTS `verifikasi_email` (
                        `id_verifikasi` int(11) NOT NULL AUTO_INCREMENT,
                        `id_penyewa` int(11) NOT NULL,
                        `token` varchar(255) NOT NULL,
                        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                        `expires_at` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id_verifikasi`),
                        KEY `fk_verifikasi_penyewa` (`id_penyewa`),
                        CONSTRAINT `fk_verifikasi_penyewa` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`) ON DELETE CASCADE ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
                    
                    // Check if email_terverifikasi column exists in penyewa table, if not add it
                    try {
                        $pdo->exec("ALTER TABLE penyewa ADD COLUMN email_terverifikasi tinyint(1) NOT NULL DEFAULT 0");
                    } catch (PDOException $e) {
                        // Column already exists, ignore error
                    }
                    
                    $token = bin2hex(random_bytes(32)); // Generate secure token
                    $expires_at = date('Y-m-d H:i:s', strtotime('+7 days')); // Token expires in 7 days
                    
                    $stmt = $pdo->prepare("INSERT INTO verifikasi_email (id_penyewa, token, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$id, $token, $expires_at]);
                    
                    // Untuk development, skip email verification
                    // mail($email, "Verifikasi Akun", "Link verifikasi: http://localhost/verify.php?token=$token");
                    
                    // Set email sebagai sudah diverifikasi untuk development
                    $stmt = $pdo->prepare("UPDATE penyewa SET email_terverifikasi = 1 WHERE id_penyewa = ?");
                    $stmt->execute([$id]);
                    
                    $sukses = "Registrasi berhasil. Anda sudah bisa login.";
                } catch (PDOException $e) {
                    // If verifikasi_email operations fail, still allow registration but skip email verification
                    error_log("Email verification table error: " . $e->getMessage());
                    
                    // Just mark email as verified for development
                    try {
                        // Check if email_terverifikasi column exists in penyewa table, if not add it
                        try {
                            $pdo->exec("ALTER TABLE penyewa ADD COLUMN email_terverifikasi tinyint(1) NOT NULL DEFAULT 0");
                        } catch (PDOException $e2) {
                            // Column already exists, ignore error
                        }
                        
                        $stmt = $pdo->prepare("UPDATE penyewa SET email_terverifikasi = 1 WHERE id_penyewa = ?");
                        $stmt->execute([$id]);
                    } catch (PDOException $e2) {
                        // Ignore this error, email_terverifikasi column might not exist
                        error_log("Email verification column error: " . $e2->getMessage());
                    }
                    
                    $sukses = "Registrasi berhasil. Anda sudah bisa login.";
                }
            } else {
                $error = "Gagal menyimpan data.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi Penyewa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: #B8860B;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 450px;
            width: 100%;
            padding: 40px;
            background-color: #B8860B;
            border-radius: 15px;
            margin: 20px;
            color: white;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 60px;
            height: auto;
            background: white;
            padding: 10px;
            border-radius: 10px;
        }

        .title {
            text-align: center;
            color: white;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            font-size: 32px;
            font-weight: 700;
        }

        .tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            position: relative;
        }

        .tab.active {
            border-bottom: 3px solid white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            border: none;
            border-radius: 50px;
            background-color: white;
            color: #333;
            box-sizing: border-box;
            outline: none;
        }

        textarea {
            border-radius: 20px;
            resize: vertical;
            min-height: 80px;
        }

        input::placeholder,
        textarea::placeholder {
            color: #999;
        }

        .btn-register {
            width: 100%;
            padding: 15px;
            background-color: rgba(0,0,0,0.3);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-register:hover {
            background-color: rgba(0,0,0,0.5);
            transform: translateY(-2px);
        }

        .error {
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            background-color: rgba(40, 167, 69, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: white;
            text-decoration: underline;
            font-weight: 500;
        }

        .login-link a:hover {
            color: #FFD700;
        }

        .radio-group {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background-color: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 25px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
            color: white;
        }

        .radio-group input[type="radio"] {
            margin-right: 8px;
            width: auto;
        }

        #instansi-fields {
            transition: all 0.3s ease;
        }
    </style>
    <script>
        function toggleInstansiFields() {
            const tipe = document.querySelector('input[name="tipe_penyewa"]:checked').value;
            document.getElementById('instansi-fields').style.display = tipe === 'instansi' ? 'block' : 'none';
        }
    </script>
</head>
<body onload="toggleInstansiFields()">
    <div class="container">
        <div class="logo">
            <img src="logoAU.png" alt="Logo">
        </div>
        <div class="title">Sistem Persewaan Gedung Serbaguna</div>
        
        <h2>Registrasi</h2>
        
        <div class="tabs">
            <div class="tab" id="tab-umum" onclick="setTipeUmum()">Umum</div>
            <div class="tab active" id="tab-instansi" onclick="setTipeInstansi()">Instansi</div>
        </div>
        
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (!empty($sukses)) echo "<p class='success'>$sukses</p>"; ?>
        
        <form action="" method="POST">
            <input type="hidden" name="tipe_penyewa" id="tipe_penyewa" value="instansi">
            
            <div class="form-group">
                <input type="text" name="nik" required placeholder="NIK">
            </div>
            
            <div id="instansi-fields">
                <div class="form-group">
                    <input type="text" name="nama_instansi" placeholder="Nama Instansi">
                </div>
            </div>
            
            <div class="form-group">
                <input type="text" name="nama_lengkap" required placeholder="Nama Lengkap Penyewa">
            </div>
            
            <div class="form-group">
                <input type="tel" name="no_telepon" required placeholder="No. Telepon">
            </div>
            
            <div class="form-group">
                <textarea name="alamat" rows="3" required placeholder="Alamat Instansi"></textarea>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" required placeholder="Email">
            </div>
            
            <div class="form-group">
                <input type="text" name="username" required placeholder="Username">
            </div>
            
            <div class="form-group">
                <input type="password" name="password" required placeholder="Password">
            </div>
            
            <div class="form-group">
                <input type="password" name="konfirmasi_password" required placeholder="Konfirmasi Password">
            </div>

            <button type="submit" class="btn-register">DAFTAR</button>
        </form>
        
        <div class="login-link">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script>
        function setTipeUmum() {
            document.getElementById('tipe_penyewa').value = 'umum';
            document.getElementById('tab-umum').classList.add('active');
            document.getElementById('tab-instansi').classList.remove('active');
            document.getElementById('instansi-fields').style.display = 'none';
            // Update placeholder for alamat
            document.querySelector('textarea[name="alamat"]').placeholder = 'Alamat';
        }
        
        function setTipeInstansi() {
            document.getElementById('tipe_penyewa').value = 'instansi';
            document.getElementById('tab-instansi').classList.add('active');
            document.getElementById('tab-umum').classList.remove('active');
            document.getElementById('instansi-fields').style.display = 'block';
            // Update placeholder for alamat
            document.querySelector('textarea[name="alamat"]').placeholder = 'Alamat Instansi';
        }
        
        function toggleInstansiFields() {
            // Default to instansi view as shown in the image
            setTipeInstansi();
        }
    </script>
</body>
</html>
