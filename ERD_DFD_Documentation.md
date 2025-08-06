# ERD DAN DFD SISTEM SEWA GEDUNG PT ANEKA

## ENTITY RELATIONSHIP DIAGRAM (ERD)

```mermaid
erDiagram
    ADMIN {
        int id_admin PK
        varchar username UK
        varchar password
        varchar nama_lengkap
        varchar email UK
        timestamp created_at
    }

    PENYEWA {
        int id_penyewa PK
        enum tipe_penyewa
        varchar nama_instansi
        varchar nama_lengkap
        varchar nik
        varchar no_telepon
        varchar email UK
        text alamat
        varchar username UK
        varchar password
        tinyint email_terverifikasi
    }

    ACARA {
        int id_acara PK
        varchar nama_acara
        int kapasitas
        decimal harga
        varchar lokasi
        enum status
        text fasilitas
    }

    PEMESANAN {
        int id_pemesanan PK
        int id_penyewa FK
        int id_acara FK
        date tanggal_sewa
        date tanggal_selesai
        int durasi
        text kebutuhan_tambahan
        decimal total
        enum metode_pembayaran
        timestamp tanggal_pesan
        enum tipe_pesanan
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
        varchar token
        timestamp created_at
        timestamp expires_at
    }

    %% Relationships
    PENYEWA ||--o{ PEMESANAN : "membuat"
    ACARA ||--o{ PEMESANAN : "dipesan untuk"
    PEMESANAN ||--|| PEMBAYARAN : "memiliki"
    PENYEWA ||--o{ VERIFIKASI_EMAIL : "memiliki"
```

## STRUKTUR RELASI DATABASE

### 1. **ADMIN**
- **Primary Key**: id_admin
- **Unique Keys**: username, email
- **Fungsi**: Mengelola sistem sewa gedung

### 2. **PENYEWA** 
- **Primary Key**: id_penyewa
- **Unique Keys**: email, username
- **Types**: individu, instansi
- **Fungsi**: Customer yang menyewa gedung

### 3. **ACARA**
- **Primary Key**: id_acara
- **Fungsi**: Jenis acara/gedung yang bisa disewa
- **Status**: tersedia, tidak tersedia

### 4. **PEMESANAN**
- **Primary Key**: id_pemesanan
- **Foreign Keys**: 
  - id_penyewa → PENYEWA(id_penyewa)
  - id_acara → ACARA(id_acara)
- **Fungsi**: Transaksi pemesanan gedung

### 5. **PEMBAYARAN**
- **Primary Key**: id_pembayaran
- **Foreign Key**: id_pemesanan → PEMESANAN(id_pemesanan)
- **Fungsi**: Data pembayaran dan bukti transfer

### 6. **VERIFIKASI_EMAIL**
- **Primary Key**: id_verifikasi
- **Foreign Key**: id_penyewa → PENYEWA(id_penyewa)
- **Fungsi**: Token verifikasi email penyewa

## KARDINALITAS RELASI

| Relasi | Kardinalitas | Deskripsi |
|--------|--------------|-----------|
| PENYEWA - PEMESANAN | 1:N | Satu penyewa dapat membuat banyak pemesanan |
| ACARA - PEMESANAN | 1:N | Satu acara dapat dipesan berkali-kali |
| PEMESANAN - PEMBAYARAN | 1:1 | Setiap pemesanan memiliki satu pembayaran |
| PENYEWA - VERIFIKASI_EMAIL | 1:N | Satu penyewa dapat memiliki banyak token verifikasi |

---

## DATA FLOW DIAGRAM (DFD)

### DFD LEVEL 0 (CONTEXT DIAGRAM)

```mermaid
graph LR
    A[PENYEWA] --> B[SISTEM SEWA GEDUNG]
    C[ADMIN] --> B
    B --> A
    B --> C
    
    A -.->|"• Data Registrasi<br/>• Data Login<br/>• Data Pemesanan<br/>• Bukti Pembayaran"| B
    B -.->|"• Konfirmasi Registrasi<br/>• Info Acara<br/>• Status Pemesanan<br/>• Laporan"| A
    
    C -.->|"• Data Login Admin<br/>• Kelola Acara<br/>• Verifikasi Pembayaran"| B
    B -.->|"• Dashboard Admin<br/>• Laporan Pemesanan<br/>• Data Penyewa"| C
```

### DFD LEVEL 1 (DECOMPOSITION)

```mermaid
graph TD
    %% External Entities
    A[PENYEWA]
    B[ADMIN]
    
    %% Processes
    P1[1.0<br/>MANAJEMEN<br/>PENYEWA]
    P2[2.0<br/>MANAJEMEN<br/>ACARA]
    P3[3.0<br/>MANAJEMEN<br/>PEMESANAN]
    P4[4.0<br/>MANAJEMEN<br/>PEMBAYARAN]
    P5[5.0<br/>VERIFIKASI<br/>EMAIL]
    
    %% Data Stores
    D1[(D1: PENYEWA)]
    D2[(D2: ACARA)]
    D3[(D3: PEMESANAN)]
    D4[(D4: PEMBAYARAN)]
    D5[(D5: VERIFIKASI_EMAIL)]
    D6[(D6: ADMIN)]
    
    %% Data Flows
    A -->|Data Registrasi| P1
    A -->|Data Login| P1
    P1 -->|Konfirmasi Registrasi| A
    P1 -->|Token Verifikasi| P5
    P5 -->|Email Verifikasi| A
    
    A -->|Request Info Acara| P2
    P2 -->|Info Acara & Harga| A
    
    A -->|Data Pemesanan| P3
    P3 -->|Konfirmasi Booking| A
    P3 -->|Data Pembayaran| P4
    
    A -->|Bukti Pembayaran| P4
    P4 -->|Status Pembayaran| A
    
    B -->|Login Admin| P1
    B -->|Kelola Acara| P2
    B -->|Verifikasi Pembayaran| P4
    P4 -->|Status Verifikasi| B
    P3 -->|Laporan Pemesanan| B
    
    %% Process to Data Store connections
    P1 <--> D1
    P1 <--> D6
    P2 <--> D2
    P3 <--> D3
    P4 <--> D4
    P5 <--> D5
    
    P3 --> D1
    P3 --> D2
    P4 --> D3
```

### DFD LEVEL 2 - PROSES MANAJEMEN PEMESANAN (3.0)

```mermaid
graph TD
    %% External Entities & Higher Level Process
    A[PENYEWA]
    D1[(D1: PENYEWA)]
    D2[(D2: ACARA)]
    D3[(D3: PEMESANAN)]
    
    %% Level 2 Processes
    P31[3.1<br/>CEK KETERSEDIAAN<br/>ACARA]
    P32[3.2<br/>BUAT PEMESANAN<br/>BARU]
    P33[3.3<br/>UPDATE STATUS<br/>PEMESANAN]
    P34[3.4<br/>GENERATE<br/>LAPORAN]
    
    %% Data Flows
    A -->|Pilih Tanggal & Acara| P31
    P31 -->|Cek Konflik Jadwal| D3
    P31 -->|Info Ketersediaan| A
    P31 -->|Data Acara| D2
    
    A -->|Konfirmasi Booking| P32
    P32 -->|Simpan Pemesanan| D3
    P32 -->|Konfirmasi Booking| A
    P32 -->|Validasi Penyewa| D1
    
    P33 -->|Update Status| D3
    P33 -->|Notifikasi Update| A
    
    P34 -->|Data Pemesanan| D3
    P34 -->|Laporan| B[ADMIN]
```

---

## ALUR PROSES UTAMA

### 1. **REGISTRASI PENYEWA**
```
Penyewa → Input Data → Validasi → Simpan ke DB → Generate Token → Kirim Email Verifikasi
```

### 2. **PEMESANAN GEDUNG**
```
Penyewa Login → Pilih Acara → Pilih Tanggal → Cek Ketersediaan → Konfirmasi → Buat Pemesanan → Generate Payment
```

### 3. **PEMBAYARAN**
```
Upload Bukti → Validasi Admin → Update Status → Notifikasi Penyewa
```

### 4. **MANAJEMEN ADMIN**
```
Login Admin → Dashboard → Kelola Acara → Verifikasi Pembayaran → Generate Laporan
```

---

## TEKNOLOGI YANG DIGUNAKAN

| Komponen | Teknologi |
|----------|-----------|
| **Frontend** | HTML5, CSS3, JavaScript, Bootstrap |
| **Backend** | PHP 8.x |
| **Database** | MySQL/MariaDB |
| **Authentication** | Session-based with bcrypt |
| **File Upload** | PHP File Handling |
| **Email** | PHP Mailer (untuk verifikasi) |

---

## KEAMANAN DATABASE

### 1. **Foreign Key Constraints**
- ON DELETE CASCADE
- ON UPDATE CASCADE

### 2. **Data Validation**
- ENUM untuk status terbatas
- NOT NULL untuk field wajib
- UNIQUE untuk email dan username

### 3. **Password Security**
- Hashing dengan bcrypt
- Salt otomatis

### 4. **Email Verification**
- Token-based verification
- Expiration time untuk token
