<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Borrower;
use Illuminate\Database\Seeder;

class AssetLendingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Assets
        $assets = [
            [
                'asset_code'    => 'AST-001',
                'name'          => 'Laptop Lenovo ThinkPad T14',
                'category'      => 'Elektronik',
                'status'        => 'available',
                'condition'     => 'Bagus',
                'description'   => 'Core i7, 16GB RAM, 512GB SSD. Cocok untuk programming dan presentasi.',
                'purchase_date' => '2024-03-15',
            ],
            [
                'asset_code'    => 'AST-002',
                'name'          => 'MacBook Pro M2 14 Inch',
                'category'      => 'Elektronik',
                'status'        => 'borrowed',
                'condition'     => 'Sangat Bagus',
                'description'   => 'Apple Silicon M2 Pro, 16GB RAM. Dipakai untuk evaluasi visual QC sarang burung walet.',
                'purchase_date' => '2024-06-20',
            ],
            [
                'asset_code'    => 'AST-003',
                'name'          => 'Proyektor Epson EB-X500',
                'category'      => 'Peralatan Kantor',
                'status'        => 'available',
                'condition'     => 'Bagus',
                'description'   => 'Proyektor 3600 ANSI Lumens untuk meeting room dan presentasi klien.',
                'purchase_date' => '2023-11-10',
            ],
            [
                'asset_code'    => 'AST-004',
                'name'          => 'Mobil Avanza Veloz Operasional (B 1234 AKU)',
                'category'      => 'Kendaraan',
                'status'        => 'borrowed',
                'condition'     => 'Bagus',
                'description'   => 'Kendaraan operasional inventaris kantor untuk pengiriman dan urusan luar kota.',
                'purchase_date' => '2023-08-01',
            ],
            [
                'asset_code'    => 'AST-005',
                'name'          => 'Kamera Mirrorless Sony A7 IV',
                'category'      => 'Dokumentasi',
                'status'        => 'available',
                'condition'     => 'Bagus',
                'description'   => 'Full frame camera + Lensa FE 24-70mm GM untuk dokumentasi produk & event perusahaan.',
                'purchase_date' => '2024-01-25',
            ],
            [
                'asset_code'    => 'AST-006',
                'name'          => 'Wireless Microphone Hollyland Lark M1',
                'category'      => 'Dokumentasi',
                'status'        => 'available',
                'condition'     => 'Bagus',
                'description'   => 'Dual wireless mic untuk liputan video edukasi bird nest.',
                'purchase_date' => '2024-02-14',
            ],
            [
                'asset_code'    => 'AST-007',
                'name'          => 'Motor Honda Vario 160 (B 5678 WAL)',
                'category'      => 'Kendaraan',
                'status'        => 'maintenance',
                'condition'     => 'Perlu Servis Rutin',
                'description'   => 'Sedang jadwal ganti oli dan servis rem berkala di bengkel resmi.',
                'purchase_date' => '2023-05-12',
            ],
            [
                'asset_code'    => 'AST-008',
                'name'          => 'Printer Multifungsi Epson EcoTank L3210',
                'category'      => 'Peralatan Kantor',
                'status'        => 'available',
                'condition'     => 'Bagus',
                'description'   => 'Printer color scanner serbaguna divisi administrasi.',
                'purchase_date' => '2024-04-10',
            ],
        ];

        $createdAssets = [];
        foreach ($assets as $item) {
            $createdAssets[$item['asset_code']] = Asset::updateOrCreate(
                ['asset_code' => $item['asset_code']],
                $item
            );
        }

        // 2. Seed Borrowers
        $borrowers = [
            [
                'borrower_code' => 'EMP-001',
                'name'          => 'Andi Pratama',
                'department'    => 'IT Support',
                'phone'         => '081298765431',
                'email'         => 'andi.pratama@akuibirdnest.co.id',
            ],
            [
                'borrower_code' => 'EMP-002',
                'name'          => 'Siti Rahma',
                'department'    => 'Quality Control',
                'phone'         => '081298765432',
                'email'         => 'siti.rahma@akuibirdnest.co.id',
            ],
            [
                'borrower_code' => 'EMP-003',
                'name'          => 'Hendra Wijaya',
                'department'    => 'Operasional & Logistik',
                'phone'         => '081298765433',
                'email'         => 'hendra.wijaya@akuibirdnest.co.id',
            ],
            [
                'borrower_code' => 'EMP-004',
                'name'          => 'Dian Kusuma',
                'department'    => 'Marketing & Sales',
                'phone'         => '081298765434',
                'email'         => 'dian.kusuma@akuibirdnest.co.id',
            ],
        ];

        $createdBorrowers = [];
        foreach ($borrowers as $item) {
            $createdBorrowers[$item['borrower_code']] = Borrower::updateOrCreate(
                ['borrower_code' => $item['borrower_code']],
                $item
            );
        }

        // 3. Seed Asset Loans
        $loans = [
            // Active Loan 1: MacBook Pro to Siti Rahma
            [
                'loan_code'            => 'LOAN-20261108-0001',
                'asset_id'             => $createdAssets['AST-002']->id,
                'borrower_id'          => $createdBorrowers['EMP-002']->id,
                'borrow_date'          => '2026-11-08',
                'expected_return_date' => '2026-11-15',
                'actual_return_date'   => null,
                'status'               => 'active',
                'purpose'              => 'Pemeriksaan standar mutu dan grading sarang burung walet batch ekspor.',
                'notes'                => 'Peminjaman dalam kondisi sangat baik.',
            ],
            // Active Loan 2: Mobil Avanza to Hendra Wijaya
            [
                'loan_code'            => 'LOAN-20261109-0002',
                'asset_id'             => $createdAssets['AST-004']->id,
                'borrower_id'          => $createdBorrowers['EMP-003']->id,
                'borrow_date'          => '2026-11-09',
                'expected_return_date' => '2026-11-10',
                'actual_return_date'   => null,
                'status'               => 'active',
                'purpose'              => 'Pengiriman sampel sarang burung walet ke laboratorium uji sertifikasi.',
                'notes'                => 'Bensin posisi setengah tangki saat berangkat.',
            ],
            // Returned Loan 1: Proyektor Epson to Dian Kusuma
            [
                'loan_code'            => 'LOAN-20261101-0003',
                'asset_id'             => $createdAssets['AST-003']->id,
                'borrower_id'          => $createdBorrowers['EMP-004']->id,
                'borrow_date'          => '2026-11-01',
                'expected_return_date' => '2026-11-02',
                'actual_return_date'   => '2026-11-02',
                'status'               => 'returned',
                'purpose'              => 'Presentasi prospek ekspor walet ke buyer dari Tiongkok.',
                'notes'                => 'Catatan Kembali: Dikembalikan tepat waktu beserta kabel HDMI lengkap.',
            ],
            // Returned Loan 2: Laptop Thinkpad to Andi Pratama
            [
                'loan_code'            => 'LOAN-20261025-0004',
                'asset_id'             => $createdAssets['AST-001']->id,
                'borrower_id'          => $createdBorrowers['EMP-001']->id,
                'borrow_date'          => '2026-10-25',
                'expected_return_date' => '2026-10-30',
                'actual_return_date'   => '2026-10-30',
                'status'               => 'returned',
                'purpose'              => 'Setup workstation cabang baru dan monitoring jaringan.',
                'notes'                => 'Catatan Kembali: Selesai tepat waktu tanpa kendala.',
            ],
        ];

        foreach ($loans as $loan) {
            AssetLoan::updateOrCreate(
                ['loan_code' => $loan['loan_code']],
                $loan
            );
        }
    }
}

