@extends('layouts.app')

@section('title', 'Manajemen Asset')

@section('content')
<div x-data="assetPage()">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Manajemen Asset</h1>
            <p class="text-xs text-gray-500">Daftar inventaris dan status aset</p>
        </div>
        <div>
            <button @click="openModal()" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition">
                + Tambah Asset
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <form method="GET" action="{{ route('web.assets') }}" class="bg-white p-3 rounded-lg border border-gray-200 mb-6 flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama asset, kategori..." class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="retired" {{ request('status') === 'retired' ? 'selected' : '' }}>Rusak</option>
            </select>

            <select name="category" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'status', 'category']))
                <a href="{{ route('web.assets') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-xs font-medium">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Assets Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-3.5 py-2.5">Kode</th>
                        <th class="px-3.5 py-2.5">Nama Asset</th>
                        <th class="px-3.5 py-2.5">Kategori</th>
                        <th class="px-3.5 py-2.5">Kondisi</th>
                        <th class="px-3.5 py-2.5">Status</th>
                        <th class="px-3.5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3.5 py-2.5 font-mono text-gray-700">{{ $item->asset_code }}</td>
                            <td class="px-3.5 py-2.5">
                                <div class="font-medium text-gray-800">{{ $item->name }}</div>
                                <div class="text-[11px] text-gray-400">{{ $item->description ?? '-' }}</div>
                            </td>
                            <td class="px-3.5 py-2.5 text-gray-600">{{ $item->category }}</td>
                            <td class="px-3.5 py-2.5 text-gray-600">{{ $item->condition ?? 'Bagus' }}</td>
                            <td class="px-3.5 py-2.5">
                                @if($item->status === 'available')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-green-100 text-green-800">Tersedia</span>
                                @elseif($item->status === 'borrowed')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-yellow-100 text-yellow-800">Dipinjam</span>
                                @elseif($item->status === 'maintenance')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-orange-100 text-orange-800">Maintenance</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-red-100 text-red-800">Rusak</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-2.5 text-right space-x-2">
                                <button @click="showDetail('{{ $item->asset_code }}', '{{ addslashes($item->name) }}', '{{ $item->category }}', '{{ $item->status }}', '{{ $item->condition }}', '{{ addslashes($item->description ?? '') }}')" class="text-blue-600 hover:underline">
                                    Detail
                                </button>
                                <button @click="deleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')" class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3.5 py-6 text-center text-gray-400">
                                Tidak ada data asset
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="px-3.5 py-2.5 border-t border-gray-100 bg-gray-50">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Asset -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" x-transition>
        <div class="bg-white rounded-lg max-w-md w-full p-5 shadow-lg" @click.outside="modalOpen = false">
            <div class="flex items-center justify-between mb-3 border-b pb-2">
                <h3 class="font-bold text-gray-900 text-sm">Tambah Asset Baru</h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form @submit.prevent="submitAsset()">
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Kode Asset *</label>
                        <input type="text" x-model="form.asset_code" required placeholder="Contoh: AST-009" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Nama Asset *</label>
                        <input type="text" x-model="form.name" required placeholder="Nama barang / inventaris" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Kategori *</label>
                            <input type="text" x-model="form.category" required placeholder="Elektronik, dll" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Kondisi</label>
                            <input type="text" x-model="form.condition" placeholder="Bagus" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Deskripsi</label>
                        <textarea x-model="form.description" rows="2" placeholder="Keterangan singkat" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500"></textarea>
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
    function assetPage() {
        return {
            modalOpen: false,
            form: { asset_code: '', name: '', category: '', condition: 'Bagus', description: '' },
            openModal() {
                this.form = { asset_code: '', name: '', category: '', condition: 'Bagus', description: '' };
                this.modalOpen = true;
            },
            async submitAsset() {
                try {
                    const res = await fetch('/api/assets', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(this.form)
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert('Asset berhasil disimpan!');
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menyimpan asset');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan koneksi.');
                }
            },
            async deleteItem(id, name) {
                if (!confirm(`Hapus asset '${name}'?`)) return;
                try {
                    const res = await fetch(`/api/assets/${id}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert('Asset berhasil dihapus');
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus');
                    }
                } catch (e) {
                    alert('Gagal menghapus asset.');
                }
            },
            showDetail(code, name, category, status, condition, desc) {
                alert(`DETAIL ASSET:\nKode: ${code}\nNama: ${name}\nKategori: ${category}\nStatus: ${status}\nKondisi: ${condition}\nDeskripsi: ${desc || '-'}`);
            }
        };
    }
</script>
@endpush
