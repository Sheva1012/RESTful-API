# Sistem Peminjaman Asset - RESTful API & Fullstack App
**Technical Assessment Maganghub — PT Akui Bird Nest Indonesia**

Aplikasi RESTful API dan Dashboard Fullstack sederhana untuk mengelola siklus peminjaman dan pengembalian aset perusahaan secara terintegrasi, aman, dan tervalidasi.

---

## 🌟 Fitur Utama

1. **Manajemen Master Asset (CRUD)**
   - Tambah, lihat, perbarui, dan hapus data aset.
   - Filter aset berdasarkan `status` (*available*, *borrowed*, *maintenance*, *retired*), `category`, dan pencarian keyword.
   - Proteksi keamanan: Aset yang sedang dipinjam **tidak dapat dihapus** dari sistem.

2. **Transaksi Peminjaman Asset (Borrowing)**
   - Validasi ketersediaan real-time: Sistem **menolak (422 Unprocessable Entity)** peminjaman aset yang sedang dipinjam atau berstatus *maintenance*.
   - Otomatisasi status: Ketika aset berhasil dipinjam, status aset otomatis berubah menjadi `borrowed`.
   - Kode transaksi otomatis unik (contoh: `LOAN-20261109-XXXX`).

3. **Pengembalian Asset (Returning)**
   - Proses pengembalian unit dengan pencatatan tanggal aktual kembali.
   - Deteksi keterlambatan pengembalian (*overdue*).
   - Otomatisasi status: Setelah pengembalian disetujui, status aset langsung dipulihkan menjadi `available` dan dapat dipinjam kembali.
   - Catatan kondisi barang saat dikembalikan.

4. **Dashboard Statistik & Analitik**
   - Ringkasan total aset, unit tersedia, unit dipinjam, unit dalam perbaikan.
   - Total transaksi aktif dan selesai.
   - Distribusi aset per kategori.
   - Riwayat transaksi peminjaman terbaru.

5. **Antarmuka Interaktif (Fullstack Single Page)**
   - Dashboard responsif berbasis Tailwind CSS & Alpine.js di endpoint root (`/`).
   - Modal interaktif untuk membuat pinjaman baru, input aset baru, dan 1-click pengembalian.
   - Dilengkapi *Live API Tester* langsung di browser.

6. **Automated Testing (PHPUnit / Pest)**
   - 12 Feature & Unit Tests dengan 44 Assertions (100% Pass) mencakup seluruh skenario *happy path* dan *edge cases* bisnis.

---

## 🛠️ Tech Stack

- **Framework**: Laravel 11 / 12
- **Bahasa**: PHP 8.4+
- **Database**: MySQL (XAMPP / phpMyAdmin) / SQLite ready
- **Frontend**: Blade (Modular Layout & Pages) + Tailwind CSS CDN + Alpine.js
- **API Architecture**: RESTful API JSON standar (`success`, `message`, `data`, `errors`)
- **Testing**: PHPUnit (16 Tests, 48 Assertions - 100% Passed)

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### 1. Clone Repository
```bash
git clone https://github.com/<username>/<repo-name>.git
cd <repo-name>
```

### 2. Install Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Environment File
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
```
*(Di Windows PowerShell: `Copy-Item .env.example .env`)*

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Setup Database & Seeding Data Dummy
Pastikan file database SQLite telah siap dan jalankan migrasi beserta data awal:
```bash
# Untuk SQLite:
touch database/database.sqlite

# Jalankan migrasi dan seeder
php artisan migrate --seed
```

Data awal yang otomatis di-seed:
- **8 Master Asset** (MacBook Pro M2, Laptop ThinkPad, Proyektor Epson, Mobil Avanza Operasional, Kamera Sony A7 IV, dsb.)
- **4 Data Peminjam/Karyawan PT Akui Bird Nest** (IT Support, QC, Operasional, Marketing)
- **4 Transaksi Peminjaman** (2 status aktif dipinjam, 2 status sudah dikembalikan)

### 6. Jalankan Server Development
```bash
php artisan serve
```
Aplikasi dapat diakses melalui browser di: **`http://127.0.0.1:8000`**

---

## 🧪 Menjalankan Automated Test Suite

Untuk memastikan seluruh endpoint dan logika validasi berjalan 100%:
```bash
php artisan test
```

Hasil pengujian:
```text
PASS  Tests\Feature\AssetApiTest
✓ can list assets
✓ can create asset with valid data
✓ cannot create asset with duplicate code
✓ can update asset
✓ cannot delete borrowed asset

PASS  Tests\Feature\LoanApiTest
✓ can borrow available asset
✓ cannot borrow already borrowed asset
✓ can return borrowed asset
✓ cannot return already returned asset

PASS  Tests\Feature\DashboardApiTest
✓ can get dashboard summary

PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\ExampleTest

Tests:    12 passed (44 assertions)
Duration: 1.90s
```

---

## 📖 Dokumentasi RESTful API Endpoints

Base URL: `http://127.0.0.1:8000/api`

### 1. General & Dashboard
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/ping` | Health-check endpoint status API |
| `GET` | `/api/dashboard` | Statistik total aset, unit dipinjam, kategori, & peminjaman terkini |

### 2. Manajemen Asset
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/assets` | Ambil daftar aset (Support query: `?status=available&category=Elektronik&search=macbook&all=1`) |
| `POST` | `/api/assets` | Tambah aset baru |
| `GET` | `/api/assets/categories` | Daftar kategori unik |
| `GET` | `/api/assets/{id}` | Detail aset beserta riwayat peminjamannya |
| `PUT` | `/api/assets/{id}` | Update data aset |
| `DELETE` | `/api/assets/{id}` | Hapus aset (ditolak jika sedang berstatus dipinjam) |

#### Contoh Request Body (Tambah Asset):
```json
POST /api/assets
Content-Type: application/json

{
  "asset_code": "AST-010",
  "name": "Drone DJI Mavic 3",
  "category": "Dokumentasi",
  "status": "available",
  "condition": "Bagus",
  "description": "Kamera thermal untuk inspeksi sarang walet",
  "purchase_date": "2024-05-10"
}
```

---

### 3. Transaksi Peminjaman Asset
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/loans` | Daftar peminjaman (Support query: `?status=active&search=hendra`) |
| `POST` | `/api/loans` | Peminjaman aset baru (Validasi ketersediaan unit) |
| `GET` | `/api/loans/{id}` | Detail peminjaman |
| `POST` | `/api/loans/{id}/return` | Proses pengembalian aset (Status unit otomatis kembali `available`) |
| `DELETE` | `/api/loans/{id}` | Batalkan/hapus catatan peminjaman |

#### Contoh Request Body (Peminjaman Asset):
```json
POST /api/loans
Content-Type: application/json

{
  "asset_id": 1,
  "borrower_id": 2,
  "borrow_date": "2026-11-09",
  "expected_return_date": "2026-11-12",
  "purpose": "Inspeksi grading mutu sarang walet batch 4",
  "notes": "Kelengkapan tas dan charger dibawa"
}
```

#### Contoh Response Sukses:
```json
{
  "success": true,
  "message": "Peminjaman asset 'Laptop Lenovo ThinkPad T14' berhasil dibuat",
  "data": {
    "id": 5,
    "loan_code": "LOAN-20261109-X89AB",
    "asset_id": 1,
    "borrower_id": 2,
    "borrow_date": "2026-11-09",
    "expected_return_date": "2026-11-12",
    "status": "active",
    "purpose": "Inspeksi grading mutu sarang walet batch 4",
    "asset": {
      "id": 1,
      "name": "Laptop Lenovo ThinkPad T14",
      "status": "borrowed"
    }
  }
}
```

#### Contoh Request Body (Pengembalian Asset):
```json
POST /api/loans/5/return
Content-Type: application/json

{
  "actual_return_date": "2026-11-12",
  "condition": "Bagus",
  "notes": "Dikembalikan lengkap tanpa kerusakan"
}
```

---

### 4. Manajemen Peminjam (Karyawan)
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/borrowers` | Daftar peminjam terdaftar |
| `POST` | `/api/borrowers` | Tambah data karyawan/peminjam baru |
| `GET` | `/api/borrowers/{id}` | Detail peminjam beserta seluruh riwayat peminjamannya |
| `PUT` | `/api/borrowers/{id}` | Perbarui data peminjam |
| `DELETE` | `/api/borrowers/{id}` | Hapus peminjam (ditolak jika masih memegang unit aktif) |

---

## 📬 Postman Collection

File Postman Collection siap pakai sudah disertakan di root repository:
- **`postman_collection.json`**

Cukup klik tombol **Import** pada aplikasi Postman dan pilih file tersebut untuk langsung mencoba seluruh request.

---

## 👨‍💻 Informasi Pengembang
- **Nama Pelamar**: Sheva
- **Posisi**: Magang Software Engineer / Backend Developer
- **Tujuan**: Seleksi Maganghub di PT Akui Bird Nest Indonesia
