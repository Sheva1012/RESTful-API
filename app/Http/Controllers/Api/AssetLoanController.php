<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Borrower;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AssetLoanController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of asset loans.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AssetLoan::with(['asset', 'borrower']);

        // Filter by status (active, returned, overdue)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by asset
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->input('asset_id'));
        }

        // Filter by borrower
        if ($request->filled('borrower_id')) {
            $query->where('borrower_id', $request->input('borrower_id'));
        }

        // Search by loan_code, asset name, or borrower name
        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('loan_code', 'like', "%{$term}%")
                  ->orWhere('purpose', 'like', "%{$term}%")
                  ->orWhereHas('asset', function ($sq) use ($term) {
                      $sq->where('name', 'like', "%{$term}%")
                         ->orWhere('asset_code', 'like', "%{$term}%");
                  })
                  ->orWhereHas('borrower', function ($sq) use ($term) {
                      $sq->where('name', 'like', "%{$term}%")
                         ->orWhere('borrower_code', 'like', "%{$term}%")
                         ->orWhere('department', 'like', "%{$term}%");
                  });
            });
        }

        $perPage = $request->input('per_page', 15);
        $loans = $request->boolean('all') ? $query->latest()->get() : $query->latest()->paginate($perPage);

        return $this->successResponse($loans, 'Daftar peminjaman asset berhasil diambil');
    }

    /**
     * Store a newly created asset loan (Borrow an asset).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asset_id'             => 'required|exists:assets,id',
            'borrower_id'          => 'required|exists:borrowers,id',
            'borrow_date'          => 'required|date',
            'expected_return_date' => 'required|date|after_or_equal:borrow_date',
            'purpose'              => 'required|string|max:500',
            'notes'                => 'nullable|string',
        ], [
            'asset_id.required'             => 'Asset wajib dipilih.',
            'asset_id.exists'               => 'Asset yang dipilih tidak valid.',
            'borrower_id.required'          => 'Peminjam wajib dipilih.',
            'borrower_id.exists'            => 'Peminjam yang dipilih tidak valid.',
            'borrow_date.required'          => 'Tanggal pinjam wajib diisi.',
            'expected_return_date.required' => 'Tanggal rencana kembali wajib diisi.',
            'expected_return_date.after_or_equal' => 'Tanggal rencana kembali tidak boleh sebelum tanggal pinjam.',
            'purpose.required'              => 'Keperluan peminjaman wajib diisi.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $data = $validator->validated();

        // Database transaction for safety
        return DB::transaction(function () use ($data) {
            $asset = Asset::lockForUpdate()->find($data['asset_id']);

            // Business logic check: is asset available?
            if ($asset->status !== 'available') {
                return $this->errorResponse("Asset '{$asset->name}' sedang tidak tersedia untuk dipinjam (Status saat ini: {$asset->status}).", 422);
            }

            // Generate unique loan code (e.g. LOAN-20261109-A1B2)
            $loanCode = 'LOAN-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $loan = AssetLoan::create([
                'loan_code'            => $loanCode,
                'asset_id'             => $data['asset_id'],
                'borrower_id'          => $data['borrower_id'],
                'borrow_date'          => $data['borrow_date'],
                'expected_return_date' => $data['expected_return_date'],
                'actual_return_date'   => null,
                'status'               => 'active',
                'purpose'              => $data['purpose'],
                'notes'                => $data['notes'] ?? null,
            ]);

            // Update asset status to 'borrowed'
            $asset->update(['status' => 'borrowed']);

            // Load relations for response
            $loan->load(['asset', 'borrower']);

            return $this->successResponse($loan, "Peminjaman asset '{$asset->name}' berhasil dibuat", 201);
        });
    }

    /**
     * Display the specified asset loan.
     */
    public function show(int|string $id): JsonResponse
    {
        $loan = AssetLoan::with(['asset', 'borrower'])->find($id);

        if (! $loan) {
            return $this->errorResponse('Data peminjaman tidak ditemukan', 404);
        }

        return $this->successResponse($loan, 'Detail peminjaman berhasil diambil');
    }

    /**
     * Process return of an asset loan.
     */
    public function returnAsset(Request $request, int|string $id): JsonResponse
    {
        $loan = AssetLoan::with(['asset', 'borrower'])->find($id);

        if (! $loan) {
            return $this->errorResponse('Data peminjaman tidak ditemukan', 404);
        }

        if ($loan->status === 'returned') {
            return $this->errorResponse('Asset ini sudah dikembalikan pada tanggal ' . $loan->actual_return_date?->format('d-m-Y'), 400);
        }

        $validator = Validator::make($request->all(), [
            'actual_return_date' => 'nullable|date',
            'notes'              => 'nullable|string',
            'condition'          => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $actualReturnDate = $request->input('actual_return_date')
            ? Carbon::parse($request->input('actual_return_date'))->toDateString()
            : now()->toDateString();

        return DB::transaction(function () use ($loan, $actualReturnDate, $request) {
            // Update loan record
            $notes = $loan->notes;
            if ($request->filled('notes')) {
                $notes = ($notes ? $notes . ' | ' : '') . 'Catatan Kembali: ' . $request->input('notes');
            }

            // Check if overdue
            $isOverdue = Carbon::parse($actualReturnDate)->greaterThan($loan->expected_return_date);
            if ($isOverdue) {
                $notes = ($notes ? $notes . ' | ' : '') . '[Terlambat Dikembalikan]';
            }

            $loan->update([
                'actual_return_date' => $actualReturnDate,
                'status'             => 'returned',
                'notes'              => $notes,
            ]);

            // Update asset status back to 'available'
            $assetData = ['status' => 'available'];
            if ($request->filled('condition')) {
                $assetData['condition'] = $request->input('condition');
            }
            $loan->asset->update($assetData);

            $loan->refresh()->load(['asset', 'borrower']);

            return $this->successResponse($loan, "Asset '{$loan->asset->name}' berhasil dikembalikan");
        });
    }

    /**
     * Cancel/delete a loan record.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $loan = AssetLoan::with('asset')->find($id);

        if (! $loan) {
            return $this->errorResponse('Data peminjaman tidak ditemukan', 404);
        }

        return DB::transaction(function () use ($loan) {
            // If loan is still active, restore asset status to available
            if ($loan->status === 'active' && $loan->asset) {
                $loan->asset->update(['status' => 'available']);
            }

            $loan->delete();

            return $this->successResponse(null, 'Data peminjaman berhasil dihapus');
        });
    }
}

