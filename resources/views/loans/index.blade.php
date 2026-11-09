@extends('layouts.app')

@section('title', 'Peminjaman Asset')

@section('content')
<div x-data="loanPage()">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Transaksi Peminjaman Asset</h1>
            <p class="text-xs text-gray-500">Pencatatan peminjaman dan pengembalian barang</p>
        </div>
        <div>
            <button @click="openNewLoanModal()" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition">
                + Buat Pinjaman Baru
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <form method="GET" action="{{ route('web.loans') }}" class="bg-white p-3 rounded-lg border border-gray-200 mb-6 flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode transaksi, nama asset, peminjam..." class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Dipinjam (Aktif)</option>
                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Sudah Kembali</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
            </select>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('web.loans') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-xs font-medium">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Loans Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-3.5 py-2.5">Kode</th>
                        <th class="px-3.5 py-2.5">Asset</th>
                        <th class="px-3.5 py-2.5">Peminjam</th>
                        <th class="px-3.5 py-2.5">Tgl Pinjam</th>
                        <th class="px-3.5 py-2.5">Rencana Kembali</th>
                        <th class="px-3.5 py-2.5">Status</th>
                        <th class="px-3.5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3.5 py-2.5 font-mono text-gray-700">
                                <div>{{ $loan->loan_code }}</div>
                                <div class="text-[11px] text-gray-400 truncate max-w-xs">{{ $loan->purpose }}</div>
                            </td>
                            <td class="px-3.5 py-2.5 font-medium text-gray-800">{{ $loan->asset ? $loan->asset->name : '-' }}</td>
                            <td class="px-3.5 py-2.5 text-gray-700">{{ $loan->borrower ? $loan->borrower->name : '-' }}</td>
                            <td class="px-3.5 py-2.5 text-gray-600">{{ $loan->borrow_date ? $loan->borrow_date->format('d/m/Y') : '-' }}</td>
                            <td class="px-3.5 py-2.5">
                                <div class="{{ $loan->status === 'active' && $loan->expected_return_date && $loan->expected_return_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ $loan->expected_return_date ? $loan->expected_return_date->format('d/m/Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-3.5 py-2.5">
                                @if($loan->status === 'active')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-yellow-100 text-yellow-800">Dipinjam</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-green-100 text-green-800">Kembali</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-2.5 text-right">
                                @if($loan->status === 'active')
                                    <button @click="openReturnModal({{ $loan->id }}, '{{ addslashes($loan->asset ? $loan->asset->name : '') }}', '{{ $loan->loan_code }}', '{{ addslashes($loan->borrower ? $loan->borrower->name : '') }}', '{{ $loan->expected_return_date ? $loan->expected_return_date->format('d/m/Y') : '' }}', '{{ addslashes($loan->asset ? $loan->asset->condition : 'Bagus') }}')" class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-[11px] font-medium">
                                        Kembalikan
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3.5 py-6 text-center text-gray-400">
                                Tidak ada data peminjaman
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="px-3.5 py-2.5 border-t border-gray-100 bg-gray-50">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Buat Peminjaman -->
    <div x-show="modals.newLoan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" x-transition>
        <div class="bg-white rounded-lg max-w-md w-full p-5 shadow-lg" @click.outside="modals.newLoan = false">
            <div class="flex items-center justify-between mb-3 border-b pb-2">
                <h3 class="font-bold text-gray-900 text-sm">Peminjaman Asset</h3>
                <button @click="modals.newLoan = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form @submit.prevent="submitLoan()">
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Pilih Asset (Hanya yang Tersedia) *</label>
                        <select x-model="formLoan.asset_id" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                            <option value="">-- Pilih Asset --</option>
                            @foreach($availableAssets as $avail)
                                <option value="{{ $avail->id }}">{{ $avail->asset_code }} - {{ $avail->name }} ({{ $avail->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Pilih Peminjam (Karyawan) *</label>
                        <select x-model="formLoan.borrower_id" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($borrowers as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->department }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Tanggal Pinjam *</label>
                            <input type="date" x-model="formLoan.borrow_date" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Rencana Kembali *</label>
                            <input type="date" x-model="formLoan.expected_return_date" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Keperluan *</label>
                        <textarea x-model="formLoan.purpose" required rows="2" placeholder="Tujuan peminjaman barang" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2 text-xs">
                    <button type="button" @click="modals.newLoan = false" class="px-3 py-1.5 border border-gray-300 rounded text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded font-medium">Proses</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Kembalikan Asset -->
    <div x-show="modals.returnLoan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" x-transition>
        <div class="bg-white rounded-lg max-w-md w-full p-5 shadow-lg" @click.outside="modals.returnLoan = false">
            <div class="flex items-center justify-between mb-3 border-b pb-2">
                <h3 class="font-bold text-gray-900 text-sm">Pengembalian Asset</h3>
                <button @click="modals.returnLoan = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form @submit.prevent="submitReturn()">
                <div class="mb-3 bg-gray-50 p-2.5 rounded border border-gray-200 text-xs space-y-1">
                    <div class="font-semibold text-gray-800" x-text="selectedLoan.assetName"></div>
                    <div class="text-gray-600" x-text="'Kode: ' + selectedLoan.loanCode + ' | Peminjam: ' + selectedLoan.borrowerName"></div>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Tanggal Kembali *</label>
                        <input type="date" x-model="formReturn.actual_return_date" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Kondisi Asset</label>
                        <input type="text" x-model="formReturn.condition" placeholder="Bagus" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Catatan</label>
                        <textarea x-model="formReturn.notes" rows="2" placeholder="Catatan opsional" class="w-full px-2.5 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2 text-xs">
                    <button type="button" @click="modals.returnLoan = false" class="px-3 py-1.5 border border-gray-300 rounded text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded font-medium">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loanPage() {
        const today = new Date().toISOString().split('T')[0];
        return {
            modals: { newLoan: false, returnLoan: false },
            formLoan: { asset_id: '', borrower_id: '', borrow_date: today, expected_return_date: today, purpose: '' },
            formReturn: { actual_return_date: today, condition: 'Bagus', notes: '' },
            selectedLoan: { id: null, assetName: '', loanCode: '', borrowerName: '', expectedDate: '' },

            openNewLoanModal() {
                this.formLoan.asset_id = '';
                this.formLoan.borrower_id = '';
                this.formLoan.purpose = '';
                this.modals.newLoan = true;
            },

            async submitLoan() {
                try {
                    const res = await fetch('/api/loans', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(this.formLoan)
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal memproses peminjaman');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan.');
                }
            },

            openReturnModal(id, assetName, loanCode, borrowerName, expectedDate, condition) {
                this.selectedLoan = { id, assetName, loanCode, borrowerName, expectedDate };
                this.formReturn.condition = condition || 'Bagus';
                this.formReturn.notes = '';
                this.modals.returnLoan = true;
            },

            async submitReturn() {
                if (!this.selectedLoan.id) return;
                try {
                    const res = await fetch(`/api/loans/${this.selectedLoan.id}/return`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify(this.formReturn)
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal mengembalikan asset');
                    }
                } catch (e) {
                    alert('Gagal memproses.');
                }
            }
        };
    }
</script>
@endpush
