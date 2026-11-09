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
