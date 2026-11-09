<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of assets with optional search and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Asset::query();

        // Search by keyword
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        // Include relations if requested
        if ($request->boolean('with_active_loan')) {
            $query->with(['activeLoan.borrower']);
        }

        $perPage = $request->input('per_page', 15);
        $assets = $request->boolean('all') ? $query->latest()->get() : $query->latest()->paginate($perPage);

        return $this->successResponse($assets, 'Daftar asset berhasil diambil');
    }

    /**
     * Store a newly created asset.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asset_code'    => 'required|string|max:50|unique:assets,asset_code',
            'name'          => 'required|string|max:255',
            'category'      => 'required|string|max:100',
            'status'        => 'nullable|in:available,borrowed,maintenance,retired',
            'condition'     => 'nullable|string|max:100',
            'description'   => 'nullable|string',
            'purchase_date' => 'nullable|date',
        ], [
            'asset_code.required' => 'Kode asset wajib diisi.',
            'asset_code.unique'   => 'Kode asset sudah terdaftar.',
            'name.required'       => 'Nama asset wajib diisi.',
            'category.required'   => 'Kategori asset wajib diisi.',
            'status.in'           => 'Status harus berupa available, borrowed, maintenance, atau retired.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $asset = Asset::create($validator->validated());

        return $this->successResponse($asset, 'Asset berhasil ditambahkan', 201);
    }

    /**
     * Display the specified asset with loan history.
     */
    public function show(int|string $id): JsonResponse
    {
        $asset = Asset::with(['loans.borrower'])->find($id);

        if (! $asset) {
            return $this->errorResponse('Asset tidak ditemukan', 404);
        }

        return $this->successResponse($asset, 'Detail asset berhasil diambil');
    }

    /**
     * Update the specified asset.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $asset = Asset::find($id);

        if (! $asset) {
            return $this->errorResponse('Asset tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'asset_code'    => 'sometimes|required|string|max:50|unique:assets,asset_code,'.$asset->id,
            'name'          => 'sometimes|required|string|max:255',
            'category'      => 'sometimes|required|string|max:100',
            'status'        => 'sometimes|required|in:available,borrowed,maintenance,retired',
            'condition'     => 'nullable|string|max:100',
            'description'   => 'nullable|string',
            'purchase_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        // If asset is currently borrowed and someone attempts to change status to available directly
        if ($request->has('status') && $request->input('status') !== $asset->status && $asset->status === 'borrowed') {
            $hasActiveLoan = $asset->loans()->where('status', 'active')->exists();
            if ($hasActiveLoan && $request->input('status') === 'available') {
                return $this->errorResponse('Asset sedang dalam masa peminjaman aktif. Silakan lakukan proses pengembalian terlebih dahulu melalui endpoint return loan.', 422);
            }
        }

        $asset->update($validator->validated());

        return $this->successResponse($asset, 'Asset berhasil diperbarui');
    }

    /**
     * Remove the specified asset.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $asset = Asset::find($id);

        if (! $asset) {
            return $this->errorResponse('Asset tidak ditemukan', 404);
        }

        // Check if currently borrowed
        if ($asset->status === 'borrowed' || $asset->loans()->where('status', 'active')->exists()) {
            return $this->errorResponse('Asset tidak dapat dihapus karena sedang dipinjam!', 422);
        }

        $asset->delete();

        return $this->successResponse(null, 'Asset berhasil dihapus');
    }

    /**
     * Get distinct categories list.
     */
    public function categories(): JsonResponse
    {
        $categories = Asset::select('category')->distinct()->pluck('category');

        return $this->successResponse($categories, 'Daftar kategori berhasil diambil');
    }
}

