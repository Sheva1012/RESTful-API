@extends('layouts.app')

@section('title', 'Dokumentasi API')

@section('content')
<div x-data="docsPage()">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Dokumentasi RESTful API</h1>
        <p class="text-xs text-gray-500">Daftar endpoint sistem peminjaman asset</p>
    </div>

    <!-- Base URL Info -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <div class="text-xs text-gray-500">Base URL:</div>
            <div class="font-mono text-sm font-semibold text-gray-800">http://127.0.0.1:8000/api</div>
        </div>
        <div>
            <button @click="testEndpoint('/api/ping')" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white text-xs rounded font-medium">
                Test Ping
            </button>
        </div>
    </div>

    <!-- Endpoints List -->
    <div class="space-y-3">
        <!-- 1: Dashboard -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 font-bold rounded">GET</span>
                    <span class="font-bold text-gray-800">/api/dashboard</span>
                </div>
                <button @click="testEndpoint('/api/dashboard')" class="text-xs text-blue-600 hover:underline">Test</button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Mengambil data statistik total aset, unit tersedia, dan pinjaman terbaru.</p>
        </div>

        <!-- 2: List Assets -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 font-bold rounded">GET</span>
                    <span class="font-bold text-gray-800">/api/assets</span>
                </div>
                <button @click="testEndpoint('/api/assets?all=1')" class="text-xs text-blue-600 hover:underline">Test</button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Daftar aset dengan parameter pencarian dan filter: ?status=available&category=Elektronik</p>
        </div>

        <!-- 3: Create Asset -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 bg-green-100 text-green-800 font-bold rounded">POST</span>
                    <span class="font-bold text-gray-800">/api/assets</span>
                </div>
                <span class="text-xs text-gray-400 font-mono">JSON Body</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Menambahkan master data asset baru (asset_code, name, category, condition).</p>
        </div>

        <!-- 4: List Loans -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 font-bold rounded">GET</span>
                    <span class="font-bold text-gray-800">/api/loans</span>
                </div>
                <button @click="testEndpoint('/api/loans?all=1')" class="text-xs text-blue-600 hover:underline">Test</button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Daftar transaksi peminjaman lengkap dengan data asset dan peminjam.</p>
        </div>

        <!-- 5: Create Loan -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 bg-green-100 text-green-800 font-bold rounded">POST</span>
                    <span class="font-bold text-gray-800">/api/loans</span>
                </div>
                <span class="text-xs text-gray-500">Validasi Stok</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Peminjaman aset baru. Sistem memvalidasi ketersediaan unit. Jika berhasil, status otomatis berubah ke borrowed.</p>
        </div>

        <!-- 6: Return Loan -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 font-bold rounded">POST</span>
                    <span class="font-bold text-gray-800">/api/loans/{id}/return</span>
                </div>
                <span class="text-xs text-gray-500">Update Status</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Pengembalian aset. Status peminjaman menjadi returned dan status unit aset otomatis kembali available.</p>
        </div>

        <!-- 7: Borrowers -->
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 font-bold rounded">GET</span>
                    <span class="font-bold text-gray-800">/api/borrowers</span>
                </div>
                <button @click="testEndpoint('/api/borrowers?all=1')" class="text-xs text-blue-600 hover:underline">Test</button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Daftar staf/karyawan yang terdaftar di sistem.</p>
        </div>
    </div>

    <!-- Live Tester Result Box -->
    <div x-show="apiResult.show" class="mt-6 bg-white rounded-lg border border-gray-200 p-4 shadow-sm" x-transition>
        <div class="flex items-center justify-between mb-2 border-b pb-2">
            <span class="font-mono text-xs font-semibold text-gray-700" x-text="'Response: ' + apiResult.url"></span>
            <button @click="apiResult.show = false" class="text-xs text-gray-400 hover:text-gray-600">Tutup</button>
        </div>
        <pre class="bg-gray-50 p-3 rounded text-xs font-mono text-gray-800 overflow-x-auto max-h-80" x-text="JSON.stringify(apiResult.data, null, 2)"></pre>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function docsPage() {
        return {
            apiResult: { show: false, url: '', data: null },
            async testEndpoint(url) {
                try {
                    const res = await fetch(url);
                    const data = await res.json();
                    this.apiResult = { show: true, url, data };
                } catch (e) {
                    this.apiResult = { show: true, url, data: { error: e.message } };
                }
            }
        };
    }
</script>
@endpush
