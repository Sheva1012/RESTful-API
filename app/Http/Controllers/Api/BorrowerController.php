<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BorrowerController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of borrowers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Borrower::query();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        if ($request->boolean('with_counts')) {
            $query->withCount([
                'loans as total_loans',
                'loans as active_loans' => function ($q) {
                    $q->where('status', 'active');
                },
            ]);
        }

        $perPage = $request->input('per_page', 15);
        $borrowers = $request->boolean('all') ? $query->orderBy('name')->get() : $query->latest()->paginate($perPage);

        return $this->successResponse($borrowers, 'Daftar peminjam berhasil diambil');
    }

    /**
     * Store a newly created borrower.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'borrower_code' => 'required|string|max:50|unique:borrowers,borrower_code',
            'name'          => 'required|string|max:255',
            'department'    => 'required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'required|email|max:255|unique:borrowers,email',
        ], [
            'borrower_code.required' => 'Kode peminjam wajib diisi.',
            'borrower_code.unique'   => 'Kode peminjam sudah terdaftar.',
            'name.required'          => 'Nama peminjam wajib diisi.',
            'department.required'    => 'Departemen wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.unique'           => 'Email sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $borrower = Borrower::create($validator->validated());

        return $this->successResponse($borrower, 'Peminjam berhasil ditambahkan', 201);
    }

    /**
     * Display the specified borrower with loan history.
     */
    public function show(int|string $id): JsonResponse
    {
        $borrower = Borrower::with(['loans.asset'])->find($id);

        if (! $borrower) {
            return $this->errorResponse('Data peminjam tidak ditemukan', 404);
        }

        return $this->successResponse($borrower, 'Detail peminjam berhasil diambil');
    }

    /**
     * Update the specified borrower.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $borrower = Borrower::find($id);

        if (! $borrower) {
            return $this->errorResponse('Data peminjam tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'borrower_code' => 'sometimes|required|string|max:50|unique:borrowers,borrower_code,'.$borrower->id,
            'name'          => 'sometimes|required|string|max:255',
            'department'    => 'sometimes|required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'sometimes|required|email|max:255|unique:borrowers,email,'.$borrower->id,
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $borrower->update($validator->validated());

        return $this->successResponse($borrower, 'Data peminjam berhasil diperbarui');
    }

    /**
     * Remove the specified borrower.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $borrower = Borrower::find($id);

        if (! $borrower) {
            return $this->errorResponse('Data peminjam tidak ditemukan', 404);
        }

        // Check if borrower has active loans
        if ($borrower->loans()->where('status', 'active')->exists()) {
            return $this->errorResponse('Peminjam tidak dapat dihapus karena masih memiliki pinjaman aktif!', 422);
        }

        $borrower->delete();

        return $this->successResponse(null, 'Data peminjam berhasil dihapus');
    }
}

