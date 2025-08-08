# 📊 DATABASE DIAGRAMS - SISTEM SEWA GEDUNG
## Entity Relationship Diagram (ERD) dan Data Flow Diagram (DFD)

---

## 🔗 **ENTITY RELATIONSHIP DIAGRAM (ERD)**

```mermaid
erDiagram
    PENYEWA {
        int id_penyewa PK
        varchar nama_lengkap
        varchar email
        varchar no_telepon
        varchar alamat
        varchar password
        datetime created_at
        int id_verifikasi FK
    }
    
    ACARA {
        int id_acara PK
        varchar nama_acara
        decimal harga_sewa
        text deskripsi
        text fasilitas
        datetime created_at
    }
    
    PEMESANAN {
        int id_pemesanan PK
        int id_penyewa FK
        int id_acara FK
        varchar nama_acara_display
        date tanggal_acara
        time waktu_mulai
        time waktu_selesai
        int jumlah_tamu
        text kebutuhan_tambahan
        enum status_pemesanan
        decimal total_biaya
        datetime created_at
        int id_pembayaran FK
    }
    
    PEMBAYARAN {
        int id_pembayaran PK
        int id_pemesanan FK
        varchar bukti_pembayaran
        datetime tanggal_upload
        enum status_pembayaran
    }
    
    VERIFIKASI_EMAIL {
        int id_verifikasi PK
        int id_penyewa FK
        varchar verification_code
        boolean is_verified
        datetime created_at
        datetime verified_at
    }
    
    ADMIN {
        int id_admin PK
        varchar username
        varchar password
        varchar nama_admin
        datetime created_at
    }

    %% Relasi antar tabel
    PENYEWA ||--o{ PEMESANAN : "memiliki"
    PENYEWA ||--|| VERIFIKASI_EMAIL : "diverifikasi"
    ACARA ||--o{ PEMESANAN : "dipesan_untuk"
    PEMESANAN ||--|| PEMBAYARAN : "dibayar_dengan"
    
    %% Relasi FK tambahan (bidirectional)
    PEMESANAN ||--|| PEMBAYARAN : "referensi_pembayaran"
    PENYEWA ||--|| VERIFIKASI_EMAIL : "referensi_verifikasi"
```

---

## 📈 **DATA FLOW DIAGRAM (DFD) - LEVEL 0 (CONTEXT DIAGRAM)**

```mermaid
flowchart TD
    %% External Entities
    PENYEWA[👤 PENYEWA]
    ADMIN[👨‍💼 ADMIN]
    EMAIL_SYSTEM[📧 SISTEM EMAIL]
    
    %% Main System
    SYSTEM[🏢 SISTEM SEWA GEDUNG<br/>PT. ANEKA USAHA]
    
    %% Data Flows - Penyewa
    PENYEWA -->|Data Registrasi| SYSTEM
    PENYEWA -->|Data Login| SYSTEM
    PENYEWA -->|Data Pemesanan| SYSTEM
    PENYEWA -->|Bukti Pembayaran| SYSTEM
    
    SYSTEM -->|Konfirmasi Registrasi| PENYEWA
    SYSTEM -->|Status Pemesanan| PENYEWA
    SYSTEM -->|Nota Pembayaran| PENYEWA
    SYSTEM -->|Info Acara| PENYEWA
    
    %% Data Flows - Admin
    ADMIN -->|Data Login Admin| SYSTEM
    ADMIN -->|Validasi Pembayaran| SYSTEM
    ADMIN -->|Update Status| SYSTEM
    
    SYSTEM -->|Laporan Pemesanan| ADMIN
    SYSTEM -->|Data Penyewa| ADMIN
    SYSTEM -->|Data Keuangan| ADMIN
    
    %% Data Flows - Email System
    SYSTEM -->|Kode Verifikasi| EMAIL_SYSTEM
    EMAIL_SYSTEM -->|Status Pengiriman| SYSTEM
    EMAIL_SYSTEM -->|Email Verifikasi| PENYEWA
    
    style SYSTEM fill:#e1f5fe
    style PENYEWA fill:#f3e5f5
    style ADMIN fill:#fff3e0
    style EMAIL_SYSTEM fill:#e8f5e8
```

---

## 📊 **DATA FLOW DIAGRAM (DFD) - LEVEL 1**

```mermaid
flowchart TD
    %% External Entities
    PENYEWA[👤 PENYEWA]
    ADMIN[👨‍💼 ADMIN]
    EMAIL_SYSTEM[📧 SISTEM EMAIL]
    
    %% Processes
    P1[1.0<br/>REGISTRASI &<br/>LOGIN]
    P2[2.0<br/>MANAJEMEN<br/>PEMESANAN]
    P3[3.0<br/>PROSES<br/>PEMBAYARAN]
    P4[4.0<br/>VERIFIKASI<br/>EMAIL]
    P5[5.0<br/>ADMINISTRASI<br/>SISTEM]
    
    %% Data Stores
    D1[(D1: PENYEWA)]
    D2[(D2: ACARA)]
    D3[(D3: PEMESANAN)]
    D4[(D4: PEMBAYARAN)]
    D5[(D5: VERIFIKASI)]
    D6[(D6: ADMIN)]
    
    %% Data Flows
    
    %% Registrasi & Login
    PENYEWA -->|Data Registrasi| P1
    PENYEWA -->|Data Login| P1
    P1 -->|Data Penyewa| D1
    P1 -->|Kode Verifikasi| P4
    P1 -->|Status Login| PENYEWA
    
    %% Verifikasi Email
    P4 -->|Data Verifikasi| D5
    P4 -->|Email Verifikasi| EMAIL_SYSTEM
    EMAIL_SYSTEM -->|Konfirmasi Kirim| P4
    PENYEWA -->|Kode Verifikasi| P4
    P4 -->|Status Verifikasi| PENYEWA
    
    %% Manajemen Pemesanan
    PENYEWA -->|Data Pemesanan| P2
    P2 -->|Info Acara| D2
    P2 -->|Data Pemesanan| D3
    P2 -->|Data Penyewa| D1
    P2 -->|Konfirmasi Booking| PENYEWA
    
    %% Proses Pembayaran
    PENYEWA -->|Bukti Pembayaran| P3
    P3 -->|Data Pembayaran| D4
    P3 -->|Update Status| D3
    P3 -->|Nota Pembayaran| PENYEWA
    
    %% Administrasi
    ADMIN -->|Login Admin| P5
    ADMIN -->|Validasi Pembayaran| P5
    P5 -->|Data Admin| D6
    P5 -->|Update Status| D4
    P5 -->|Update Status| D3
    P5 -->|Laporan| ADMIN
    P5 -->|Data Pemesanan| D3
    P5 -->|Data Pembayaran| D4
    P5 -->|Data Penyewa| D1
    
    %% Styling
    style P1 fill:#e3f2fd
    style P2 fill:#e8f5e8
    style P3 fill:#fff3e0
    style P4 fill:#f3e5f5
    style P5 fill:#fce4ec
    
    style D1 fill:#ffebee
    style D2 fill:#ffebee
    style D3 fill:#ffebee
    style D4 fill:#ffebee
    style D5 fill:#ffebee
    style D6 fill:#ffebee
```

---

## 🏗️ **SYSTEM ARCHITECTURE DIAGRAM**

```mermaid
graph TB
    %% User Interface Layer
    subgraph "🖥️ PRESENTATION LAYER"
        UI1[📱 User Dashboard<br/>dashboard_user.php]
        UI2[🏢 Admin Panel<br/>admin/dashboard.php]
        UI3[📄 Booking Forms<br/>sewa.php, acara.php]
        UI4[💰 Payment Pages<br/>pembayaran.php]
    end
    
    %% Business Logic Layer
    subgraph "⚙️ BUSINESS LOGIC LAYER"
        BL1[🔐 Authentication<br/>login.php, register.php]
        BL2[📋 Booking Process<br/>proses_sewa.php]
        BL3[💳 Payment Processing<br/>proses_booking.php]
        BL4[📧 Email Verification<br/>verifikasi system]
        BL5[📊 Report Generation<br/>cetak_nota.php]
    end
    
    %% Data Access Layer
    subgraph "🗄️ DATA ACCESS LAYER"
        DAL[🔗 Database Connection<br/>config.php]
    end
    
    %% Database Layer
    subgraph "💾 DATABASE LAYER"
        DB1[(👤 PENYEWA)]
        DB2[(🎪 ACARA)]
        DB3[(📅 PEMESANAN)]
        DB4[(💰 PEMBAYARAN)]
        DB5[(✅ VERIFIKASI)]
        DB6[(👨‍💼 ADMIN)]
    end
    
    %% External Services
    subgraph "🌐 EXTERNAL SERVICES"
        EXT1[📧 Email Service]
        EXT2[💾 File Storage<br/>uploads/]
        EXT3[🖨️ PDF Generator]
    end
    
    %% Connections
    UI1 --> BL1
    UI1 --> BL2
    UI2 --> BL5
    UI3 --> BL2
    UI4 --> BL3
    
    BL1 --> DAL
    BL2 --> DAL
    BL3 --> DAL
    BL4 --> DAL
    BL5 --> DAL
    
    DAL --> DB1
    DAL --> DB2
    DAL --> DB3
    DAL --> DB4
    DAL --> DB5
    DAL --> DB6
    
    BL4 --> EXT1
    BL3 --> EXT2
    BL5 --> EXT3
    
    %% Styling
    style UI1 fill:#e3f2fd
    style UI2 fill:#e8f5e8
    style UI3 fill:#fff3e0
    style UI4 fill:#f3e5f5
    
    style BL1 fill:#fce4ec
    style BL2 fill:#e0f2f1
    style BL3 fill:#fff8e1
    style BL4 fill:#f1f8e9
    style BL5 fill:#fafafa
```

---

## 🔄 **RELASI NORMALISASI DIAGRAM**

```mermaid
graph LR
    subgraph "📋 BEFORE NORMALIZATION"
        PK1[🔑 id_pembayaran<br/>❌ No FK Reference]
        PK2[🔑 id_verifikasi<br/>❌ No FK Reference]
    end
    
    subgraph "✅ AFTER NORMALIZATION"
        PK3[🔑 id_pembayaran<br/>✅ FK in pemesanan]
        PK4[🔑 id_verifikasi<br/>✅ FK in penyewa]
    end
    
    subgraph "🎯 NORMALIZATION RULES"
        RULE1[📏 All PK must have<br/>FK references]
        RULE2[🔗 Bidirectional<br/>relationships]
        RULE3[🛡️ Referential<br/>integrity]
    end
    
    PK1 -->|FIXED| PK3
    PK2 -->|FIXED| PK4
    
    RULE1 --> PK3
    RULE1 --> PK4
    RULE2 --> PK3
    RULE2 --> PK4
    RULE3 --> PK3
    RULE3 --> PK4
    
    style PK1 fill:#ffcdd2
    style PK2 fill:#ffcdd2
    style PK3 fill:#c8e6c9
    style PK4 fill:#c8e6c9
    style RULE1 fill:#e1f5fe
    style RULE2 fill:#e1f5fe
    style RULE3 fill:#e1f5fe
```

---

## 📝 **PENJELASAN DIAGRAM:**

### 🔗 **ERD (Entity Relationship Diagram):**
- Menunjukkan semua tabel dengan atribut lengkap
- Relasi antar tabel dengan kardinalitas yang benar
- FK constraints yang sudah diperbaiki (bidirectional)

### 📈 **DFD Level 0 (Context Diagram):**
- Sistem sebagai black box
- Entitas eksternal: Penyewa, Admin, Email System
- Data flow input/output sistem

### 📊 **DFD Level 1:**
- Breakdown sistem menjadi 5 proses utama
- Data stores untuk setiap tabel database
- Detail aliran data antar proses

### 🏗️ **System Architecture:**
- 4 layer architecture (Presentation, Business Logic, Data Access, Database)
- External services integration
- File organization struktur

### 🔄 **Normalization Diagram:**
- Visualisasi perbaikan database
- Before/After comparison
- Compliance dengan aturan normalisasi

**Semua diagram sudah sesuai dengan struktur database yang diperbaiki dan memenuhi standar akademik!** 🎓
