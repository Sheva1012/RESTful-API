<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Borrower;
use Illuminate\Http\Request;

class WebController extends Controller
{
    /**
     * Dashboard page overview
     */
    public function dashboard()
    {
        $stats = [
            'total_assets'       => Asset::count(),
            'available_assets'   => Asset::where('status', 'available')->count(),
            'borrowed_assets'    => Asset::where('status', 'borrowed')->count(),
            'maintenance_assets' => Asset::where('status', 'maintenance')->count(),
            'total_borrowers'    => Borrower::count(),
            'total_loans'        => AssetLoan::count(),
            'active_loans'       => AssetLoan::where('status', 'active')->count(),
            'returned_loans'     => AssetLoan::where('status', 'returned')->count(),
            'overdue_loans'      => AssetLoan::overdue()->count(),
        ];

        $recentLoans = AssetLoan::with(['asset', 'borrower'])
            ->latest()
            ->take(6)
            ->get();

        $assetsByCategory = Asset::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->get();

        return view('dashboard', compact('stats', 'recentLoans', 'assetsByCategory'));
    }

    /**
     * Assets listing & management page
     */
    public function assets(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $assets = $query->latest()->paginate(10)->withQueryString();
        $categories = Asset::select('category')->distinct()->pluck('category');

        return view('assets.index', compact('assets', 'categories'));
    }

    /**
     * Loans / Peminjaman transactions page
     */
    public function loans(Request $request)
    {
        $query = AssetLoan::with(['asset', 'borrower']);

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('loan_code', 'like', "%{$term}%")
                  ->orWhere('purpose', 'like', "%{$term}%")
                  ->orWhereHas('asset', fn ($sq) => $sq->where('name', 'like', "%{$term}%")->orWhere('asset_code', 'like', "%{$term}%"))
                  ->orWhereHas('borrower', fn ($sq) => $sq->where('name', 'like', "%{$term}%")->orWhere('borrower_code', 'like', "%{$term}%"));
            });
        }

        $loans = $query->latest()->paginate(10)->withQueryString();
        $availableAssets = Asset::where('status', 'available')->orderBy('name')->get();
        $borrowers = Borrower::orderBy('name')->get();

        return view('loans.index', compact('loans', 'availableAssets', 'borrowers'));
    }

    /**
     * Borrowers / Karyawan listing page
     */
    public function borrowers(Request $request)
    {
        $query = Borrower::withCount([
            'loans as total_loans',
            'loans as active_loans' => fn ($q) => $q->where('status', 'active'),
        ]);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        $borrowers = $query->orderBy('name')->paginate(12)->withQueryString();
        $departments = Borrower::select('department')->distinct()->pluck('department');

        return view('borrowers.index', compact('borrowers', 'departments'));
    }

    /**
     * API Documentation page
     */
    public function docs()
    {
        return view('docs.index');
    }
}

