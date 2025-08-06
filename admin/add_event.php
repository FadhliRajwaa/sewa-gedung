<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../config.php';  // Mengakses file config.php
include '../includes/db.php';  // Mengakses file db.php

// Proses penambahan acara
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_acara = mysqli_real_escape_string($conn, $_POST['nama_acara']);
    $kapasitas = mysqli_real_escape_string($conn, $_POST['kapasitas']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $fasilitas = mysqli_real_escape_string($conn, $_POST['fasilitas']);

    // Query untuk menambah acara
    $sql = "INSERT INTO acara (nama_acara, kapasitas, harga, lokasi, status, fasilitas) 
            VALUES ('$nama_acara', '$kapasitas', '$harga', '$lokasi', '$status', '$fasilitas')";

    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert success'>Acara berhasil ditambahkan!</div>";
    } else {
        echo "<div class='alert error'>Error: " . $sql . "<br>" . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Acara</title>
    <link rel="stylesheet" href="path/to/your/styles.css">
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 150px;
        }

        button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 16px;
        }

        .success {
            background-color: #4caf50;
            color: white;
        }

        .error {
            background-color: #f44336;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Acara</h2>

        <form method="POST" action="add_event.php">
            <div class="form-group">
                <label for="nama_acara">Nama Acara</label>
                <input type="text" name="nama_acara" id="nama_acara" required>
            </div>
            <div class="form-group">
                <label for="kapasitas">Kapasitas</label>
                <input type="number" name="kapasitas" id="kapasitas" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" name="harga" id="harga" required>
            </div>
            <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <input type="text" name="lokasi" id="lokasi" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="tidak tersedia">Tidak Tersedia</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fasilitas">Fasilitas</label>
                <textarea name="fasilitas" id="fasilitas" required></textarea>
            </div>
            <button type="submit">Tambah Acara</button>
        </form>
    </div>
</body>
</html>
