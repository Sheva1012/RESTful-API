@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div>
    <!-- Simple Page Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-xs text-gray-500">Ringkasan sistem inventaris dan peminjaman asset</p>
    </div>

    <!-- Simple Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Assets -->
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="text-xs font-medium text-gray-500">Total Asset</div>
            <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total_assets'] }}</div>
            <a href="{{ route('web.assets') }}" class="mt-2 text-xs text-blue-600 hover:underline block">Lihat semua asset &rarr;</a>
        </div>

        <!-- Available Assets -->
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="text-xs font-medium text-gray-500">Asset Tersedia</div>
            <div class="mt-2 text-2xl font-bold text-green-600">{{ $stats['available_assets'] }}</div>
            <a href="{{ route('web.assets', ['status' => 'available']) }}" class="mt-2 text-xs text-blue-600 hover:underline block">Siap dipinjam &rarr;</a>
        </div>

        <!-- Borrowed Assets -->
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="text-xs font-medium text-gray-500">Sedang Dipinjam</div>
            <div class="mt-2 text-2xl font-bold text-yellow-600">{{ $stats['borrowed_assets'] }}</div>
            <a href="{{ route('web.assets', ['status' => 'borrowed']) }}" class="mt-2 text-xs text-blue-600 hover:underline block">Dalam peminjaman &rarr;</a>
        </div>

        <!-- Total Loans -->
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="text-xs font-medium text-gray-500">Total Transaksi</div>
            <div class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total_loans'] }}</div>
            <div class="mt-2 text-xs text-gray-500">{{ $stats['active_loans'] }} aktif, {{ $stats['returned_loans'] }} kembali</div>
        </div>
    </div>

    <!-- Main Grid: Recent Loans & Category Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Loans Table -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-sm text-gray-900">Peminjaman Terkini</h2>
                <a href="{{ route('web.loans') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-semibold border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5">Kode</th>
                            <th class="px-3 py-2.5">Asset</th>
                            <th class="px-3 py-2.5">Peminjam</th>
                            <th class="px-3 py-2.5">Rencana Kembali</th>
                            <th class="px-3 py-2.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentLoans as $loan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2.5 font-mono text-gray-700">{{ $loan->loan_code }}</td>
                                <td class="px-3 py-2.5 font-medium text-gray-800">{{ $loan->asset ? $loan->asset->name : '-' }}</td>
                                <td class="px-3 py-2.5 text-gray-600">{{ $loan->borrower ? $loan->borrower->name : '-' }}</td>
                                <td class="px-3 py-2.5 text-gray-600">{{ $loan->expected_return_date ? $loan->expected_return_date->format('d/m/Y') : '-' }}</td>
                                <td class="px-3 py-2.5">
                                    @if($loan->status === 'active')
                                        <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-yellow-100 text-yellow-800">
                                            Dipinjam
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-green-100 text-green-800">
                                            Kembali
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category & Quick Links -->
        <div class="space-y-6">
            <!-- Category Breakdown -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="font-bold text-sm text-gray-900 mb-3">Kategori Asset</h3>
                <div class="space-y-2 text-xs">
                    @foreach($assetsByCategory as $cat)
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-gray-600">{{ $cat->category }}</span>
                            <span class="font-semibold text-gray-800">{{ $cat->count }} unit</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <h3 class="font-bold text-sm text-gray-900 mb-3">Menu Cepat</h3>
                <div class="space-y-2 text-xs">
                    <a href="{{ route('web.loans') }}" class="block p-2 rounded border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium">
                        &rarr; Form Peminjaman Asset
                    </a>
                    <a href="{{ route('web.assets') }}" class="block p-2 rounded border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium">
                        &rarr; Tambah / Kelola Asset
                    </a>
                    <a href="{{ route('web.docs') }}" class="block p-2 rounded border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium">
                        &rarr; Dokumentasi API
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
