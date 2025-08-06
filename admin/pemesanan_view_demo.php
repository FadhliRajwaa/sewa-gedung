<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan View Fixed - Modern & Responsive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #2d3748;
            font-size: 2.5em;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .before, .after {
            padding: 20px;
            border-radius: 15px;
            border: 2px solid #e2e8f0;
        }
        .before {
            background: #fee2e2;
            border-color: #ef4444;
        }
        .after {
            background: #d1fae5;
            border-color: #10b981;
        }
        .comparison h3 {
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .before h3 {
            color: #991b1b;
        }
        .after h3 {
            color: #065f46;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .feature-list .check {
            color: #10b981;
        }
        .feature-list .cross {
            color: #ef4444;
        }
        .btn-test {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1.1em;
            margin: 10px;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .footer {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-top: 30px;
        }
        .code-snippet {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
        }
        .error {
            color: #ef4444;
            background: #fee2e2;
        }
        .success {
            color: #10b981;
            background: #d1fae5;
        }
        .features {
            background: #f8fafc;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #e2e8f0;
            margin-bottom: 30px;
        }
        .features h3 {
            color: #4f46e5;
            margin-top: 0;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .feature-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        .feature-item h4 {
            color: #667eea;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        @media (max-width: 768px) {
            .comparison {
                grid-template-columns: 1fr;
            }
            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-invoice"></i> PEMESANAN VIEW - FIXED & MODERN</h1>
            <p>Detail pemesanan dengan tampilan modern dan responsive</p>
        </div>

        <div class="comparison">
            <div class="before">
                <h3><i class="fas fa-times-circle cross"></i> SEBELUM (Error)</h3>
                <div class="code-snippet error">
                    Fatal error: Unknown column 'a.harga_sewa' in 'field list'<br>
                    mysqli_sql_exception on line 40
                </div>
                <ul class="feature-list">
                    <li><i class="fas fa-times cross"></i> Query menggunakan kolom yang tidak ada</li>
                    <li><i class="fas fa-times cross"></i> Tampilan tidak responsive</li>
                    <li><i class="fas fa-times cross"></i> Design tidak modern</li>
                    <li><i class="fas fa-times cross"></i> Mobile experience buruk</li>
                    <li><i class="fas fa-times cross"></i> Tidak ada status visual</li>
                </ul>
            </div>

            <div class="after">
                <h3><i class="fas fa-check-circle check"></i> SESUDAH (Fixed)</h3>
                <div class="code-snippet success">
                    ✅ Query fixed: a.harga (bukan a.harga_sewa)<br>
                    ✅ Modern responsive design<br>
                    ✅ Mobile-first approach
                </div>
                <ul class="feature-list">
                    <li><i class="fas fa-check check"></i> Database query sesuai struktur SQL</li>
                    <li><i class="fas fa-check check"></i> Responsive design desktop & mobile</li>
                    <li><i class="fas fa-check check"></i> Modern UI dengan card layout</li>
                    <li><i class="fas fa-check check"></i> Status badges yang jelas</li>
                    <li><i class="fas fa-check check"></i> Print functionality</li>
                </ul>
            </div>
        </div>

        <div class="features">
            <h3><i class="fas fa-star"></i> Fitur-Fitur Modern</h3>
            
            <div class="feature-grid">
                <div class="feature-item">
                    <h4><i class="fas fa-database"></i> Database Fix</h4>
                    <p>• Query disesuaikan dengan struktur SQL</p>
                    <p>• Menggunakan kolom <code>a.harga</code> yang benar</p>
                    <p>• JOIN yang optimal</p>
                </div>

                <div class="feature-item">
                    <h4><i class="fas fa-mobile-alt"></i> Responsive Design</h4>
                    <p>• Mobile-first approach</p>
                    <p>• Grid layout yang adaptive</p>
                    <p>• Touch-friendly interface</p>
                </div>

                <div class="feature-item">
                    <h4><i class="fas fa-palette"></i> Modern UI</h4>
                    <p>• Card-based layout</p>
                    <p>• Status badges dengan warna</p>
                    <p>• Clean typography</p>
                </div>

                <div class="feature-item">
                    <h4><i class="fas fa-users"></i> Customer Info</h4>
                    <p>• Info penyewa lengkap</p>
                    <p>• Badge tipe individu/instansi</p>
                    <p>• Contact information</p>
                </div>

                <div class="feature-item">
                    <h4><i class="fas fa-calendar-check"></i> Booking Details</h4>
                    <p>• Detail acara yang jelas</p>
                    <p>• Tanggal dan durasi</p>
                    <p>• Fasilitas dan kebutuhan</p>
                </div>

                <div class="feature-item">
                    <h4><i class="fas fa-credit-card"></i> Payment Management</h4>
                    <p>• Update status pembayaran</p>
                    <p>• Preview bukti pembayaran</p>
                    <p>• Admin notes</p>
                </div>

                <div class="feature-item">
                    <h4><i class="fas fa-print"></i> Actions</h4>
                    <p>• Print functionality</p>
                    <p>• Email customer</p>
                    <p>• Navigation buttons</p>
                </div>

                <div class="feature-item">
                    <h4><i class="fas fa-shield-alt"></i> Admin Features</h4>
                    <p>• Status management</p>
                    <p>• Secure updates</p>
                    <p>• Activity logging</p>
                </div>
            </div>
        </div>

        <div style="background: #e6fffa; padding: 25px; border-radius: 15px; border: 2px solid #38b2ac; margin-bottom: 30px;">
            <h3 style="margin-top: 0; color: #2c7a7b;"><i class="fas fa-wrench"></i> Technical Fixes:</h3>
            
            <h4>🔧 Database Query Fix:</h4>
            <div class="code-snippet error">
                ❌ Sebelum: a.harga_sewa (kolom tidak ada)
            </div>
            <div class="code-snippet success">
                ✅ Sesudah: a.harga (sesuai struktur database)
            </div>

            <h4>📱 Responsive Improvements:</h4>
            <ul>
                <li><strong>Grid Layout:</strong> 2 kolom desktop, 1 kolom mobile</li>
                <li><strong>Navbar:</strong> Responsive dengan flex layout</li>
                <li><strong>Cards:</strong> Stackable di mobile</li>
                <li><strong>Buttons:</strong> Full-width di mobile</li>
            </ul>

            <h4>🎨 UI Enhancements:</h4>
            <ul>
                <li><strong>Color System:</strong> CSS custom properties</li>
                <li><strong>Typography:</strong> Inter font untuk readability</li>
                <li><strong>Shadows:</strong> Modern depth dengan box-shadow</li>
                <li><strong>Status Badges:</strong> Color-coded untuk clarity</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="pemesanan_view.php?id=1" class="btn-test" target="_blank">
                <i class="fas fa-external-link-alt"></i> Test Pemesanan #1
            </a>
            <a href="pemesanan_view.php?id=2" class="btn-test" target="_blank">
                <i class="fas fa-external-link-alt"></i> Test Pemesanan #2
            </a>
            <a href="data_pemesanan.php" class="btn-test" target="_blank">
                <i class="fas fa-list"></i> Lihat Semua Pemesanan
            </a>
        </div>

        <div class="footer">
            <h3>🎉 PEMESANAN VIEW SUDAH PERFECT!</h3>
            <p><strong>✅ Database Error Fixed</strong> - Query sesuai dengan struktur SQL</p>
            <p><strong>✅ Modern UI/UX</strong> - Card layout dengan status badges</p>
            <p><strong>✅ Fully Responsive</strong> - Perfect di desktop dan mobile</p>
            <p><strong>✅ Admin Features</strong> - Update status, print, email</p>
            
            <div style="margin-top: 20px;">
                <p style="font-style: italic; opacity: 0.9;">
                    Detail pemesanan yang informatif dan mudah digunakan! 🚀
                </p>
            </div>
        </div>
    </div>
</body>
</html>
