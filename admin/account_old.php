<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../config.php';  // Mengakses file config.php
include '../includes/db.php';  // Mengakses file db.php

// Query untuk mendapatkan informasi akun admin
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admin WHERE id_admin = '$admin_id' LIMIT 1";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);

// Proses update akun admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_admin = mysqli_real_escape_string($conn, $_POST['nama_admin']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Query untuk memperbarui data akun admin
    $update_sql = "UPDATE admin SET nama_admin = '$nama_admin', no_telepon = '$no_telepon', email = '$email', alamat = '$alamat' WHERE id_admin = '$admin_id'";

    if (mysqli_query($conn, $update_sql)) {
        $message = "Data akun berhasil diperbarui!";
    } else {
        $message = "Error: " . $update_sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Admin</title>
    <!-- Menambahkan link Bootstrap untuk tampilan yang lebih responsif dan rapi -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center">Edit Akun Admin</h2>

        <!-- Menampilkan pesan berhasil atau error -->
        <?php if (isset($message)): ?>
            <div class="message alert <?php echo (strpos($message, 'Error') === false) ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="account.php">
            <div class="form-group">
                <label for="nama_admin">Nama Admin</label>
                <input type="text" class="form-control" name="nama_admin" id="nama_admin" value="<?php echo $admin['nama_admin']; ?>" required>
            </div>
            <div class="form-group">
                <label for="no_telepon">No. Telepon</label>
                <input type="text" class="form-control" name="no_telepon" id="no_telepon" value="<?php echo $admin['no_telepon']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo $admin['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" name="alamat" id="alamat" rows="4" required><?php echo $admin['alamat']; ?></textarea>
            </div>
            <button type="submit">Update Akun</button>
        </form>
    </div>

    <!-- Menambahkan script Bootstrap JS dan jQuery untuk interaktivitas lebih lanjut -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
