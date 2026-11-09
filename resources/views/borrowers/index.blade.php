@extends('layouts.app')

@section('title', 'Data Peminjam')

@section('content')
<div x-data="borrowerPage()">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Data Peminjam (Karyawan)</h1>
            <p class="text-xs text-gray-500">Daftar staf/karyawan yang dapat meminjam aset</p>
        </div>
        <div>
            <button @click="modalOpen = true" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition">
                + Tambah Peminjam
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <form method="GET" action="{{ route('web.borrowers') }}" class="bg-white p-3 rounded-lg border border-gray-200 mb-6 flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK, nama karyawan, email..." class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex gap-2">
            <select name="department" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                <option value="">Semua Departemen</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'department']))
                <a href="{{ route('web.borrowers') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-xs font-medium">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Borrowers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @forelse($borrowers as $b)
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="font-bold text-gray-900 text-sm">{{ $b->name }}</div>
                <div class="text-[11px] font-mono text-gray-500">{{ $b->borrower_code }}</div>

                <div class="space-y-1 text-xs text-gray-600 mt-3 pt-2 border-t border-gray-100">
                    <div><span class="text-gray-400">Divisi:</span> {{ $b->department }}</div>
                    <div class="truncate"><span class="text-gray-400">Email:</span> {{ $b->email }}</div>
                    <div><span class="text-gray-400">Telp:</span> {{ $b->phone ?? '-' }}</div>
                    <div class="pt-1 text-[11px]">
                        <span class="text-gray-400">Pinjaman Aktif:</span>
                        <span class="font-semibold {{ $b->active_loans > 0 ? 'text-yellow-700' : 'text-gray-500' }}">
                            {{ $b->active_loans }} unit
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-6 text-center text-gray-400 text-xs rounded-lg border border-gray-200">
                Tidak ada data peminjam
            </div>
        @endforelse
    </div>

    @if($borrowers->hasPages())
        <div class="px-3.5 py-2.5 border border-gray-200 bg-white rounded-lg mb-6">
            {{ $borrowers->links() }}
        </div>
    @endif

    <!-- Modal Tambah Peminjam -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" x-transition>
        <div class="bg-white rounded-lg max-w-md w-full p-5 shadow-lg" @click.outside="modalOpen = false">
            <div class="flex items-center justify-between mb-3 border-b pb-2">
                <h3 class="font-bold text-gray-900 text-sm">Tambah Peminjam (Karyawan)</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form @submit.prevent="submitBorrower()">
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">NIK / Kode Karyawan *</label>
                        <input type="text" x-model="form.borrower_code" required placeholder="Contoh: EMP-005" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Nama Lengkap *</label>
                        <input type="text" x-model="form.name" required placeholder="Nama Karyawan" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Departemen *</label>
                        <input type="text" x-model="form.department" required placeholder="Quality Control, IT, dll" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Email *</label>
                            <input type="email" x-model="form.email" required placeholder="email@perusahaan.com" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">No. HP / WA</label>
                            <input type="text" x-model="form.phone" placeholder="0812xxxxxxxx" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2 text-xs">
                    <button type="button" @click="modalOpen = false" class="px-3 py-1.5 border border-gray-300 rounded text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function borrowerPage() {
        return {
            modalOpen: false,
            form: { borrower_code: '', name: '', department: '', email: '', phone: '' },
            async submitBorrower() {
                try {
                    const res = await fetch('/api/borrowers', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(this.form)
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menyimpan data');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan.');
                }
            }
        };
    }
</script>
@endpush
