# Sistem Peminjaman Aset

**Technical Assessment MagangHub – PT Akui Bird Nest Indonesia**

Aplikasi RESTful API dan Fullstack sederhana untuk mengelola data aset, peminjaman, dan pengembalian aset perusahaan.

## Fitur Utama

1. **Manajemen Aset**
   - Menambah, melihat, mengubah, dan menghapus data aset.
   - Melihat status dan ketersediaan aset.
   - Mencari dan memfilter data aset.

2. **Peminjaman Aset**
   - Mengajukan peminjaman aset.
   - Validasi ketersediaan aset sebelum dipinjam.
   - Mengubah status aset secara otomatis setelah peminjaman.

3. **Pengembalian Aset**
   - Memproses pengembalian aset.
   - Mencatat tanggal dan kondisi pengembalian.
   - Mengembalikan status aset menjadi tersedia.

4. **Dashboard**
   - Menampilkan statistik aset.
   - Menampilkan jumlah aset tersedia dan dipinjam.
   - Menampilkan riwayat transaksi peminjaman.

## Panduan Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/Sheva1012/RESTful-API.git
cd RESTful-API
```

### 2. Install Dependency

```bash
composer install
```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`.

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 4. Buat Database

- Jalankan Apache dan MySQL melalui XAMPP.
- Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
- Buat database baru dengan nama `restful_api`.
- Buka file `.env` dan sesuaikan konfigurasi berikut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restful_api
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Jalankan Migration dan Seeder

```bash
php artisan migrate --seed
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Buka aplikasi melalui browser:

`http://127.0.0.1:8000`

---

## Repository

GitHub: https://github.com/Sheva1012/RESTful-API
