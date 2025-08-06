<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan Mobile - FIXED!</title>
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
            max-width: 1200px;
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
        .problem-solution {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .problem, .solution {
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #e2e8f0;
        }
        .problem {
            background: #fee2e2;
            border-color: #ef4444;
        }
        .solution {
            background: #d1fae5;
            border-color: #10b981;
        }
        .problem h3, .solution h3 {
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .problem h3 {
            color: #991b1b;
        }
        .solution h3 {
            color: #065f46;
        }
        .fix-list {
            list-style: none;
            padding: 0;
        }
        .fix-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .fix-list .check {
            color: #10b981;
            font-size: 1.2em;
        }
        .fix-list .cross {
            color: #ef4444;
            font-size: 1.2em;
        }
        .mobile-preview {
            background: #f8fafc;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #e2e8f0;
            margin: 30px 0;
        }
        .mobile-preview h3 {
            color: #4f46e5;
            margin-top: 0;
        }
        .device-mockup {
            max-width: 300px;
            margin: 20px auto;
            background: #333;
            border-radius: 30px;
            padding: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .screen {
            background: white;
            border-radius: 20px;
            padding: 15px;
            min-height: 400px;
            font-size: 12px;
        }
        .mobile-card-demo {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .mobile-card-demo .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 0;
        }
        .mobile-card-demo .title {
            font-weight: 600;
            font-size: 14px;
        }
        .mobile-card-demo .id {
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 10px;
        }
        .mobile-card-demo .body {
            background: white;
            color: #333;
            padding: 10px;
            border-radius: 8px;
            font-size: 11px;
        }
        .mobile-field-demo {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .mobile-field-demo:last-child {
            border-bottom: none;
        }
        .technical-fixes {
            background: #fef3c7;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #f59e0b;
            margin: 30px 0;
        }
        .technical-fixes h3 {
            color: #92400e;
            margin-top: 0;
        }
        .code-snippet {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
            margin: 10px 0;
            overflow-x: auto;
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
            margin: 10px 5px;
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
        @media (max-width: 768px) {
            .problem-solution {
                grid-template-columns: 1fr;
            }
            .container {
                padding: 20px;
            }
            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-mobile-alt"></i> RIWAYAT PEMESANAN MOBILE - FIXED!</h1>
            <p>Data mobile sekarang sudah muncul dengan sempurna!</p>
        </div>

        <div class="problem-solution">
            <div class="problem">
                <h3><i class="fas fa-times-circle"></i> Masalah Sebelumnya</h3>
                <ul class="fix-list">
                    <li><i class="fas fa-times cross"></i> Data tidak muncul di tampilan mobile</li>
                    <li><i class="fas fa-times cross"></i> Mobile cards kosong</li>
                    <li><i class="fas fa-times cross"></i> Table disembunyikan tanpa alternatif</li>
                    <li><i class="fas fa-times cross"></i> JavaScript tidak mengisi mobile cards</li>
                    <li><i class="fas fa-times cross"></i> CSS responsive tidak optimal</li>
                    <li><i class="fas fa-times cross"></i> Hamburger menu tidak responsive</li>
                </ul>
            </div>

            <div class="solution">
                <h3><i class="fas fa-check-circle"></i> Solusi Diterapkan</h3>
                <ul class="fix-list">
                    <li><i class="fas fa-check check"></i> Server-side rendering untuk mobile cards</li>
                    <li><i class="fas fa-check check"></i> Data dari database langsung ke mobile view</li>
                    <li><i class="fas fa-check check"></i> CSS responsive yang proper</li>
                    <li><i class="fas fa-check check"></i> Mobile-first design approach</li>
                    <li><i class="fas fa-check check"></i> Hamburger menu dengan event listeners</li>
                    <li><i class="fas fa-check check"></i> Touch-optimized interface</li>
                </ul>
            </div>
        </div>

        <div class="mobile-preview">
            <h3><i class="fas fa-eye"></i> Preview Mobile View</h3>
            <p>Ini adalah tampilan mobile cards yang sekarang sudah muncul dengan data lengkap:</p>
            
            <div class="device-mockup">
                <div class="screen">
                    <div style="text-align: center; margin-bottom: 15px; font-weight: 600; color: #667eea;">
                        <i class="fas fa-history"></i> Riwayat Pemesanan
                    </div>
                    
                    <div class="mobile-card-demo">
                        <div class="header">
                            <div class="title">Pernikahan</div>
                            <div class="id">#1</div>
                        </div>
                        <div class="body">
                            <div class="mobile-field-demo">
                                <span>Tanggal:</span>
                                <span>25/12/2024</span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Penyewa:</span>
                                <span>Budi Santoso</span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Email:</span>
                                <span>budi@email.com</span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Total:</span>
                                <span><strong>Rp 5.000.000</strong></span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Status:</span>
                                <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 8px; border-radius: 10px; font-size: 10px;">Lunas</span>
                            </div>
                        </div>
                    </div>

                    <div class="mobile-card-demo">
                        <div class="header">
                            <div class="title">Seminar</div>
                            <div class="id">#2</div>
                        </div>
                        <div class="body">
                            <div class="mobile-field-demo">
                                <span>Tanggal:</span>
                                <span>20/12/2024</span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Penyewa:</span>
                                <span>PT. Teknologi</span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Email:</span>
                                <span>info@teknologi.com</span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Total:</span>
                                <span><strong>Rp 3.000.000</strong></span>
                            </div>
                            <div class="mobile-field-demo">
                                <span>Status:</span>
                                <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 8px; border-radius: 10px; font-size: 10px;">Belum Lunas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="technical-fixes">
            <h3><i class="fas fa-code"></i> Technical Fixes Implemented</h3>
            
            <h4>🔧 Server-Side Mobile Cards Generation:</h4>
            <div class="code-snippet">
&lt;!-- Mobile Cards (populated with server-side data) --&gt;
&lt;div class="mobile-cards" id="mobileCards"&gt;
    &lt;?php
    // Generate mobile cards for the same data
    if (isset($result) && $result && mysqli_num_rows($result) > 0) {
        mysqli_data_seek($result, 0); // Reset pointer to beginning
        while ($row = mysqli_fetch_assoc($result)) {
            echo '&lt;div class="mobile-card"&gt;';
            echo '&lt;div class="mobile-card-header"&gt;';
            echo '&lt;div class="mobile-card-title"&gt;' . htmlspecialchars($row['nama_acara']) . '&lt;/div&gt;';
            echo '&lt;div class="mobile-card-id"&gt;#' . htmlspecialchars($row['id']) . '&lt;/div&gt;';
            // ... more fields
        }
    }
    ?&gt;
&lt;/div&gt;</div>

            <h4>📱 CSS Responsive Improvements:</h4>
            <div class="code-snippet">
@media (max-width: 768px) {
    /* Hide desktop table on mobile */
    .table-responsive table {
        display: none !important;
    }

    /* Show mobile cards on mobile */
    .mobile-cards {
        display: block !important;
        padding: 16px 0;
    }

    .mobile-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        margin-bottom: 16px;
        box-shadow: var(--shadow);
    }
}</div>

            <h4>🍔 Hamburger Menu Fix:</h4>
            <div class="code-snippet">
// Proper event listeners
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleMobileMenu();
        });
        
        mobileToggle.addEventListener('touchstart', function(e) {
            e.preventDefault();
            toggleMobileMenu();
        });
    }
});</div>

            <h4>💾 Database Integration:</h4>
            <ul>
                <li><strong>Dynamic Column Detection:</strong> Auto-detect available database columns</li>
                <li><strong>JOIN Queries:</strong> Proper joins with penyewa, acara, and pembayaran tables</li>
                <li><strong>Fallback Data:</strong> Sample data when database tables don't exist</li>
                <li><strong>Error Handling:</strong> Comprehensive error catching and user feedback</li>
            </ul>
        </div>

        <div style="background: #e6fffa; padding: 25px; border-radius: 15px; border: 2px solid #38b2ac; margin: 30px 0;">
            <h3 style="color: #2c7a7b; margin-top: 0;"><i class="fas fa-clipboard-check"></i> Test Instructions:</h3>
            
            <h4>📱 Mobile View Test:</h4>
            <ol>
                <li>Buka riwayat pemesanan di browser</li>
                <li>Resize window ke ukuran mobile (≤768px)</li>
                <li>Atau gunakan Device Mode di Developer Tools</li>
                <li>Data akan otomatis beralih ke format card</li>
                <li>Scroll untuk melihat semua data</li>
            </ol>

            <h4>🍔 Hamburger Menu Test:</h4>
            <ol>
                <li>Di mode mobile, klik hamburger di kiri atas</li>
                <li>Sidebar akan slide smooth dari kiri</li>
                <li>Klik overlay atau tombol X untuk menutup</li>
                <li>Menu responsif dengan touch support</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="riwayat_pemesanan.php" class="btn-test" target="_blank">
                <i class="fas fa-history"></i> Test Riwayat Pemesanan Mobile
            </a>
            <a href="riwayat_pemesanan.php" class="btn-test" target="_blank" onclick="setTimeout(() => { alert('💡 Tip: Resize browser window atau gunakan Device Mode untuk test mobile view!'); }, 1000);">
                <i class="fas fa-mobile-alt"></i> Test Mobile Mode
            </a>
        </div>

        <div class="footer">
            <h3>🎉 RIWAYAT PEMESANAN MOBILE PERFECT!</h3>
            <p><strong>✅ Data Mobile:</strong> Mobile cards dengan data lengkap dari database</p>
            <p><strong>✅ Responsive Design:</strong> Auto-switch antara table dan cards</p>
            <p><strong>✅ Hamburger Menu:</strong> Smooth animation dengan touch support</p>
            <p><strong>✅ Server-Side Rendering:</strong> Data langsung dari PHP tanpa AJAX</p>
            
            <div style="margin-top: 20px; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                <p style="font-style: italic; margin: 0;">
                    <i class="fas fa-star"></i> 
                    Sekarang admin dapat melihat riwayat pemesanan dengan nyaman di mobile! 
                    <i class="fas fa-star"></i>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
