-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Nov 2026 pada 06.57
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restful api`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `status` enum('available','borrowed','maintenance','retired') NOT NULL DEFAULT 'available',
  `condition` varchar(255) NOT NULL DEFAULT 'Bagus',
  `description` text DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `assets`
--

INSERT INTO `assets` (`id`, `asset_code`, `name`, `category`, `status`, `condition`, `description`, `purchase_date`, `created_at`, `updated_at`) VALUES
(1, 'AST-001', 'Laptop Lenovo ThinkPad T14', 'Elektronik', 'available', 'Bagus', 'Core i7, 16GB RAM, 512GB SSD. Cocok untuk programming dan presentasi.', '2024-03-15', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(2, 'AST-002', 'MacBook Pro M2 14 Inch', 'Elektronik', 'borrowed', 'Sangat Bagus', 'Apple Silicon M2 Pro, 16GB RAM. Dipakai untuk evaluasi visual QC sarang burung walet.', '2024-06-20', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(3, 'AST-003', 'Proyektor Epson EB-X500', 'Peralatan Kantor', 'available', 'Bagus', 'Proyektor 3600 ANSI Lumens untuk meeting room dan presentasi klien.', '2023-11-10', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(4, 'AST-004', 'Mobil Avanza Veloz Operasional (B 1234 AKU)', 'Kendaraan', 'borrowed', 'Bagus', 'Kendaraan operasional inventaris kantor untuk pengiriman dan urusan luar kota.', '2023-08-01', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(5, 'AST-005', 'Kamera Mirrorless Sony A7 IV', 'Dokumentasi', 'available', 'Bagus', 'Full frame camera + Lensa FE 24-70mm GM untuk dokumentasi produk & event perusahaan.', '2024-01-25', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(6, 'AST-006', 'Wireless Microphone Hollyland Lark M1', 'Dokumentasi', 'available', 'Bagus', 'Dual wireless mic untuk liputan video edukasi bird nest.', '2024-02-14', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(7, 'AST-007', 'Motor Honda Vario 160 (B 5678 WAL)', 'Kendaraan', 'maintenance', 'Perlu Servis Rutin', 'Sedang jadwal ganti oli dan servis rem berkala di bengkel resmi.', '2023-05-12', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(8, 'AST-008', 'Printer Multifungsi Epson EcoTank L3210', 'Peralatan Kantor', 'available', 'Bagus', 'Printer color scanner serbaguna divisi administrasi.', '2024-04-10', '2026-11-08 23:31:32', '2026-11-08 23:31:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `asset_loans`
--

CREATE TABLE `asset_loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_code` varchar(255) NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `borrower_id` bigint(20) UNSIGNED NOT NULL,
  `borrow_date` date NOT NULL,
  `expected_return_date` date NOT NULL,
  `actual_return_date` date DEFAULT NULL,
  `status` enum('active','returned','overdue') NOT NULL DEFAULT 'active',
  `purpose` text NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `asset_loans`
--

INSERT INTO `asset_loans` (`id`, `loan_code`, `asset_id`, `borrower_id`, `borrow_date`, `expected_return_date`, `actual_return_date`, `status`, `purpose`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'LOAN-20261108-0001', 2, 2, '2026-11-08', '2026-11-15', NULL, 'active', 'Pemeriksaan standar mutu dan grading sarang burung walet batch ekspor.', 'Peminjaman dalam kondisi sangat baik.', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(2, 'LOAN-20261109-0002', 4, 3, '2026-11-09', '2026-11-10', NULL, 'active', 'Pengiriman sampel sarang burung walet ke laboratorium uji sertifikasi.', 'Bensin posisi setengah tangki saat berangkat.', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(3, 'LOAN-20261101-0003', 3, 4, '2026-11-01', '2026-11-02', '2026-11-02', 'returned', 'Presentasi prospek ekspor walet ke buyer dari Tiongkok.', 'Catatan Kembali: Dikembalikan tepat waktu beserta kabel HDMI lengkap.', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(4, 'LOAN-20261025-0004', 1, 1, '2026-10-25', '2026-10-30', '2026-10-30', 'returned', 'Setup workstation cabang baru dan monitoring jaringan.', 'Catatan Kembali: Selesai tepat waktu tanpa kendala.', '2026-11-08 23:31:32', '2026-11-08 23:31:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `borrowers`
--

CREATE TABLE `borrowers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `borrower_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `borrowers`
--

INSERT INTO `borrowers` (`id`, `borrower_code`, `name`, `department`, `phone`, `email`, `created_at`, `updated_at`) VALUES
(1, 'EMP-001', 'Andi Pratama', 'IT Support', '081298765431', 'andi.pratama@akuibirdnest.co.id', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(2, 'EMP-002', 'Siti Rahma', 'Quality Control', '081298765432', 'siti.rahma@akuibirdnest.co.id', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(3, 'EMP-003', 'Hendra Wijaya', 'Operasional & Logistik', '081298765433', 'hendra.wijaya@akuibirdnest.co.id', '2026-11-08 23:31:32', '2026-11-08 23:31:32'),
(4, 'EMP-004', 'Dian Kusuma', 'Marketing & Sales', '081298765434', 'dian.kusuma@akuibirdnest.co.id', '2026-11-08 23:31:32', '2026-11-08 23:31:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_11_09_000001_create_assets_table', 1),
(5, '2026_11_09_000002_create_borrowers_table', 1),
(6, '2026_11_09_000003_create_asset_loans_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('sKtVUARkgyyo4M6eThnirSlW1L0WwS5L4c4y7IQx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIyV1BSY05rN0NoYlBnZEpDTXRFTEw0MWt5R1FqcTNRWDhzbU8xMGxyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9ib3Jyb3dlcnM/ZGVwYXJ0bWVudD0mc2VhcmNoPSIsInJvdXRlIjoid2ViLmJvcnJvd2VycyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1794206960);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assets_asset_code_unique` (`asset_code`);

--
-- Indeks untuk tabel `asset_loans`
--
ALTER TABLE `asset_loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_loans_loan_code_unique` (`loan_code`),
  ADD KEY `asset_loans_asset_id_foreign` (`asset_id`),
  ADD KEY `asset_loans_borrower_id_foreign` (`borrower_id`);

--
-- Indeks untuk tabel `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borrowers_borrower_code_unique` (`borrower_code`),
  ADD UNIQUE KEY `borrowers_email_unique` (`email`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `asset_loans`
--
ALTER TABLE `asset_loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `asset_loans`
--
ALTER TABLE `asset_loans`
  ADD CONSTRAINT `asset_loans_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_loans_borrower_id_foreign` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
