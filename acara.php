<?php
// File: acara.php
require_once 'config.php';
session_start();

// Query to fetch events and their available dates
$stmt = $pdo->query("
    SELECT a.*, p.tanggal_sewa
    FROM acara a
    LEFT JOIN pemesanan p ON a.id_acara = p.id_acara
    ORDER BY a.nama_acara ASC
");
$acaras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Acara</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background-color: #8C4B12; /* Dark gold color */
            color: white;
            padding: 20px 0;
        }
        .header-logo {
            width: 150px; /* Larger size for the logo */
            height: auto;
            margin-right: 15px;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav {
            text-align: right; /* Align the nav to the right */
        }
        nav a {
            color: white;
            margin: 0 15px;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #F6A800; /* Light gold on hover */
        }
        .background-quote {
            background-image: url('asset/gambar/bg.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            height: 400px;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }
        .background-quote h2 {
            font-size: 2.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }
        main {
            padding: 40px 0;
        }
        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card:hover {
            transform: scale(1.05);
        }
        .status {
            margin-top: 10px;
        }
        .tersedia { background-color: #aaffaa; }
        .tidak-tersedia { background-color: #f8d7da; }
        .footer {
            text-align: center;
            padding: 20px 0;
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div>
                <img src="logo.png" alt="Logo PT. Aneka Usaha" class="header-logo">
            </div>
            <nav>
                <a href="index.php">Home</a> |
                <a href="acara.php">Acara</a> |
                <a href="panduan.php">Panduan Cara Sewa</a> |
                <a href="kontak.php">Kontak</a> |
                <?php if (!empty($_SESSION['id_penyewa'])): ?>
                    <a href="akun.php">Akun</a> |
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a> |
                    <a href="register.php">Daftar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Background Section with Quote -->
    <section class="background-quote">
        <h2>Acara Gedung PT. Aneka Usaha Kabupaten Pemalang (PERSERODA)</h2>
    </section>

    <!-- Daftar Acara Section -->
    <main class="container my-5">
        <h2 class="text-center mb-4">Daftar Acara Gedung</h2>
        <div class="row">
            <?php foreach ($acaras as $acara): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($acara['nama_acara']) ?></h3>
                            <p><strong>Kapasitas:</strong> <?= $acara['kapasitas'] ?></p>
                            <p><strong>Harga:</strong> Rp<?= number_format($acara['harga'], 0, ',', '.') ?></p>
                            <p><strong>Lokasi:</strong> <?= htmlspecialchars($acara['lokasi']) ?></p>
                            <p><strong>Fasilitas:</strong> <?= htmlspecialchars($acara['fasilitas']) ?></p>

                            <!-- Display Dates and Availability -->
                            <p><strong>Tanggal Acara:</strong></p>
                            <?php
                                // Check if 'tanggal_acara' exists and is not empty
                                if (isset($acara['tanggal_acara']) && !empty($acara['tanggal_acara'])) {
                                    // Check availability of event dates
                                    $eventDates = explode(',', $acara['tanggal_acara']);
                                    foreach ($eventDates as $date) {
                                        $booked = false;
                                        // Check if any booking exists for the event date
                                        foreach ($acaras as $booking) {
                                            if ($booking['tanggal_sewa'] == $date) {
                                                $booked = true; // If the date is already booked
                                            }
                                        }
                                        echo "<p>" . $date . " - " . ($booked ? "<span class='badge tidak-tersedia'>Sudah Dipesan</span>" : "<span class='badge tersedia'>Tersedia</span>") . "</p>";
                                    }
                                } else {
                                    echo "<p>Tanggal acara tidak tersedia.</p>"; // If no dates available
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> PT. Aneka Usaha Perseroda</p>
    </footer>
</body>
</html>
