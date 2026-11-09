<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Borrower;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get summary metrics and statistics for dashboard.
     */
    public function index(): JsonResponse
    {
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'available')->count();
        $borrowedAssets = Asset::where('status', 'borrowed')->count();
        $maintenanceAssets = Asset::where('status', 'maintenance')->count();

        $totalBorrowers = Borrower::count();

        $totalLoans = AssetLoan::count();
        $activeLoans = AssetLoan::where('status', 'active')->count();
        $returnedLoans = AssetLoan::where('status', 'returned')->count();
        $overdueLoans = AssetLoan::overdue()->count();

        $recentLoans = AssetLoan::with(['asset', 'borrower'])
            ->latest()
            ->take(5)
            ->get();

        $assetsByCategory = Asset::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->get();

        return $this->successResponse([
            'summary' => [
                'total_assets'        => $totalAssets,
                'available_assets'    => $availableAssets,
                'borrowed_assets'     => $borrowedAssets,
                'maintenance_assets'  => $maintenanceAssets,
                'total_borrowers'     => $totalBorrowers,
                'total_loans'         => $totalLoans,
                'active_loans'        => $activeLoans,
                'returned_loans'      => $returnedLoans,
                'overdue_loans'       => $overdueLoans,
            ],
            'assets_by_category' => $assetsByCategory,
            'recent_loans'       => $recentLoans,
        ], 'Statistik dashboard berhasil diambil');
    }
}

