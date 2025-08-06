<?php
session_start();

// Jika admin sudah login, alihkan ke dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

require_once '../config.php';  // Mengakses file config.php untuk koneksi database

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Query untuk mencari admin berdasarkan username
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika username ditemukan, cek password
        if ($admin) {
            // Cek apakah password di database sudah di-hash atau belum
            if (password_verify($password, $admin['password']) || $password == $admin['password']) {
                // Menyimpan informasi login ke session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['admin_name'] = $admin['nama_admin'];

                // Arahkan ke halaman dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Pesan error jika password salah
                $error_message = "Password yang Anda masukkan salah.";
            }
        } else {
            // Pesan error jika username tidak ditemukan
            $error_message = "Username tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Terjadi kesalahan sistem.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        body {
            background: #B8860B;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
        }

        .container {
            max-width: 400px;
            width: 100%;
            padding: 40px;
            background-color: #B8860B;
            border-radius: 15px;
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
            margin-bottom: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            font-size: 32px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
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

        input::placeholder {
            color: #999;
        }

        .btn-login {
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

        .btn-login:hover {
            background-color: rgba(0,0,0,0.5);
            transform: translateY(-2px);
        }

        .error-message {
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: white;
            text-decoration: underline;
            font-weight: 500;
            font-size: 14px;
        }

        .back-link a:hover {
            color: #FFD700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="../logoAU.png" alt="Logo">
        </div>
        <div class="title">Sistem Persewaan Gedung Serbaguna</div>
        
        <h2>Login</h2>
        
        <!-- Menampilkan pesan error jika ada -->
        <?php if (isset($error_message) && !empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Form login -->
        <form method="POST" action="login.php">
            <div class="form-group">
                <input type="text" name="username" required placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" name="password" required placeholder="Password">
            </div>
            <button type="submit" class="btn-login">MASUK</button>
        </form>
        
        <div class="back-link">
            <p><a href="../login.php">← Kembali ke Login Penyewa</a></p>
        </div>
    </div>
</body>
        </form>
    </div>
</body>
</html>
