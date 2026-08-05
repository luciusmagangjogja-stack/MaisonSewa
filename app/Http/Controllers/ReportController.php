<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Rental;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function resolvedBranchId(Request $request): ?int
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $requestedId = $request->input('branch_id');

            if ($requestedId) {
                abort_unless(
                    Branch::where('id', $requestedId)->exists(),
                    404,
                    'Cabang tidak ditemukan.'
                );
                return (int) $requestedId;
            }

            return null;
        }

        return (int) $user->branch_id;
    }

    private function baseRentalQuery(Request $request, ?int $branchId)
    {
        $query = Rental::with(['branch', 'customer', 'createdBy', 'items', 'payments']);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('rental_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('rental_date', '<=', $request->input('end_date'));
        }

        return $query;
    }

    private function sharedViewData(Request $request): array
    {
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $branches = $isSuperAdmin
            ? Branch::orderBy('name')->get()
            : collect();

        $selectedBranchId = $this->resolvedBranchId($request);

        return compact('isSuperAdmin', 'branches', 'selectedBranchId');
    }

    // =========================================================================
    // LAPORAN PENDAPATAN
    // =========================================================================

    public function revenue(Request $request)
    {
        $branchId = $this->resolvedBranchId($request);
        $shared   = $this->sharedViewData($request);

        $query = Rental::selectRaw('
                DATE(rental_date)  AS date,
                SUM(total_amount)  AS total_revenue,
                SUM(late_fee)      AS total_late_fee,
                COUNT(*)           AS total_rentals
            ')
            ->where('payment_status', Rental::PAYMENT_PAID);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('rental_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('rental_date', '<=', $request->input('end_date'));
        }

        $revenueData  = $query->groupByRaw('DATE(rental_date)')->orderBy('date', 'desc')->get();
        $totalRevenue = $revenueData->sum('total_revenue');
        $totalLateFee = $revenueData->sum('total_late_fee');
        $totalRentals = $revenueData->sum('total_rentals');

        return view('reports.revenue', array_merge($shared, [
            'revenueData'  => $revenueData,
            'totalRevenue' => $totalRevenue,
            'totalLateFee' => $totalLateFee,
            'totalRentals' => $totalRentals,
        ]));
    }

    // =========================================================================
    // LAPORAN TRANSAKSI
    // =========================================================================

  public function transactions(Request $request)
{
    $branchId = $this->resolvedBranchId($request);
    $shared   = $this->sharedViewData($request);

    $search   = $request->input('search');
    $status   = $request->input('status');
    $dateFrom = $request->input('date_from');
    $dateTo   = $request->input('date_to');

    $query = Rental::with(['customer', 'branch', 'createdBy', 'items', 'payments']);

    if ($branchId !== null) {
        $query->where('branch_id', $branchId);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhereHas('customer', fn($c) => $c
                  ->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
              );
        });
    }

    if ($status) {
        $query->where('rental_status', $status);
    }

    if ($dateFrom) {
        $query->whereDate('rental_date', '>=', $dateFrom);
    }

    if ($dateTo) {
        $query->whereDate('rental_date', '<=', $dateTo);
    }

    $rentals = $query->latest('rental_date')->paginate(25)->withQueryString();

    // Summary — base query sama tapi tanpa paginate
    $baseQuery = Rental::query();
    if ($branchId !== null) {
        $baseQuery->where('branch_id', $branchId);
    }
    if ($dateFrom) {
        $baseQuery->whereDate('rental_date', '>=', $dateFrom);
    }
    if ($dateTo) {
        $baseQuery->whereDate('rental_date', '<=', $dateTo);
    }

    $summary = [
        'total'      => (clone $baseQuery)->count(),
        'pending'    => (clone $baseQuery)->where('rental_status', 'waiting')->count(),
        'active'     => (clone $baseQuery)->whereIn('rental_status', ['active', 'overdue'])->count(),
        'completed'  => (clone $baseQuery)->whereIn('rental_status', ['returned', 'completed'])->count(),
        'cancelled'  => (clone $baseQuery)->where('rental_status', 'cancelled')->count(),
        'total_nilai' => (clone $baseQuery)->whereIn('rental_status', ['returned', 'completed'])->sum('total_amount'),
    ];

    $statuses = Rental::distinct()->pluck('rental_status')->sort()->values();

    return view('reports.transactions', array_merge($shared, [
        'rentals'  => $rentals,
        'summary'  => $summary,
        'statuses' => $statuses,
        'search'   => $search,
        'status'   => $status,
        'dateFrom' => $dateFrom,
        'dateTo'   => $dateTo,
    ]));
}

    // =========================================================================
    // LAPORAN STOK PRODUK
    // =========================================================================

 public function stock(Request $request)
{
    $branchId = $this->resolvedBranchId($request);
    $shared   = $this->sharedViewData($request);

    $search   = $request->input('search');
    $category = $request->input('category');

    $query = Product::with(['branch', 'category']);

    if ($branchId !== null) {
        $query->where('branch_id', $branchId);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    if ($category) {
        $query->where('category_id', $category);
    }

    // Base query tanpa filter untuk summary
    $baseQuery = Product::query();
    if ($branchId !== null) {
        $baseQuery->where('branch_id', $branchId);
    }

    $totalProducts = (clone $baseQuery)->count();
    $totalAvail    = (clone $baseQuery)->where('status', 'available')->count();
    $totalRented   = (clone $baseQuery)->where('status', 'rented')->count();
    $totalStock    = $totalAvail + $totalRented;
    $activeRentals = $totalRented;
    $outOfStock    = (clone $baseQuery)->where('stock_available', 0)->count();
    $lowStock      = (clone $baseQuery)->where('stock_available', '>', 0)
                                       ->where('stock_available', '<=', 2)->count();

    $products   = $query->orderBy('name')->paginate(25)->withQueryString();
    $categories = \App\Models\Category::orderBy('name')->get();

    return view('reports.stock', array_merge($shared, [
        'products'      => $products,
        'totalProducts' => $totalProducts,
        'totalStock'    => $totalStock,
        'totalAvail'    => $totalAvail,
        'totalRented'   => $totalRented,
        'activeRentals' => $activeRentals,
        'outOfStock'    => $outOfStock,
        'lowStock'      => $lowStock,
        'categories'    => $categories,
        'search'        => $search,
        'category'      => $category,
    ]));
}

    // =========================================================================
    // LAPORAN PENGEMBALIAN
    // =========================================================================

   public function returns(Request $request)
{
    $branchId = $this->resolvedBranchId($request);
    $shared   = $this->sharedViewData($request);

    $dateFrom = $request->input('date_from');
    $dateTo   = $request->input('date_to');

    // Base query per branch
    $base = Rental::with(['customer', 'branch', 'createdBy', 'items']);
    if ($branchId !== null) {
        $base->where('branch_id', $branchId);
    }

    // Summary
    $summary = [
        'returned'  => (clone $base)->whereIn('rental_status', ['returned', 'completed'])->count(),
        'due_today' => (clone $base)->whereIn('rental_status', ['active', 'overdue'])
                                    ->whereDate('return_due_date', today())->count(),
        'overdue'   => (clone $base)->where('rental_status', 'overdue')->count(),
    ];

    // Terlambat — overdue
    $overdue = (clone $base)->where('rental_status', 'overdue')
                            ->orderBy('return_due_date')
                            ->get();

    // Jatuh tempo hari ini
    $dueToday = (clone $base)->whereIn('rental_status', ['active', 'overdue'])
                             ->whereDate('return_due_date', today())
                             ->orderBy('return_due_date')
                             ->get();

    // Riwayat pengembalian (dengan filter tanggal)
    $returnedQuery = (clone $base)->whereIn('rental_status', ['returned', 'completed']);

    if ($dateFrom) {
        $returnedQuery->whereDate('actual_return_date', '>=', $dateFrom);
    }

    if ($dateTo) {
        $returnedQuery->whereDate('actual_return_date', '<=', $dateTo);
    }

    $returned = $returnedQuery->latest('actual_return_date')->paginate(25)->withQueryString();

    return view('reports.returns', array_merge($shared, [
        'summary'  => $summary,
        'overdue'  => $overdue,
        'dueToday' => $dueToday,
        'returned' => $returned,
        'dateFrom' => $dateFrom,
        'dateTo'   => $dateTo,
    ]));
}
    // =========================================================================
    // EXPORT CSV (tanpa package, langsung bisa dibuka di Excel)
    // =========================================================================

    public function exportExcel(Request $request)
    {
        $user     = Auth::user();
        $branchId = $this->resolvedBranchId($request);

        if (! $user->isSuperAdmin()) {
            $branchId = (int) $user->branch_id;
        }

        $rentals = $this->baseRentalQuery($request, $branchId)
            ->latest('rental_date')
            ->get();

        $branchLabel = $branchId
            ? (Branch::find($branchId)?->name ?? "cabang-{$branchId}")
            : 'semua-cabang';

        $filename = 'laporan-rental-'
            . str($branchLabel)->slug()
            . '-' . now()->format('Ymd')
            . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($rentals) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 agar Excel baca karakter Indonesia dengan benar
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom — separator semicolon untuk Excel versi Indonesia
            fputcsv($handle, [
                'No. Invoice',
                'Tanggal Rental',
                'Jatuh Tempo',
                'Tanggal Kembali',
                'Cabang',
                'Pelanggan',
                'Status Rental',
                'Status Bayar',
                'Subtotal',
                'Diskon',
                'Denda',
                'Total',
                'Sudah Dibayar',
                'Sisa Tagihan',
                'Dibuat Oleh',
            ], ';');

            foreach ($rentals as $rental) {
                fputcsv($handle, [
                    $rental->invoice_number,
                    $rental->rental_date?->format('d/m/Y'),
                    $rental->return_due_date?->format('d/m/Y'),
                    $rental->actual_return_date?->format('d/m/Y') ?? '-',
                    $rental->branch?->name,
                    $rental->customer?->name,
                    $rental->status_label,
                    $rental->payment_status_label,
                    $rental->subtotal,
                    $rental->discount,
                    $rental->late_fee,
                    $rental->total_amount,
                    $rental->paid_amount,
                    $rental->remaining_amount,
                    $rental->createdBy?->name,
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // EXPORT PDF
    // =========================================================================

    public function exportPdf(Request $request)
    {
        $user     = Auth::user();
        $branchId = $this->resolvedBranchId($request);

        if (! $user->isSuperAdmin()) {
            $branchId = (int) $user->branch_id;
        }

        $rentals      = $this->baseRentalQuery($request, $branchId)->latest('rental_date')->get();
        $totalRevenue = $rentals->where('payment_status', Rental::PAYMENT_PAID)->sum('total_amount');
        $totalLateFee = $rentals->sum('late_fee');

        $branchName = $branchId
            ? (Branch::find($branchId)?->name ?? "Cabang #{$branchId}")
            : 'Semua Cabang';

        $pdf = Pdf::loadView('reports.pdf.transactions', [
            'rentals'      => $rentals,
            'totalRevenue' => $totalRevenue,
            'totalLateFee' => $totalLateFee,
            'branchName'   => $branchName,
            'startDate'    => $request->input('start_date'),
            'endDate'      => $request->input('end_date'),
            'generatedAt'  => now()->format('d/m/Y H:i'),
            'generatedBy'  => $user->name,
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan-'
            . str($branchName)->slug()
            . '-' . now()->format('Ymd')
            . '.pdf';

        return $pdf->download($filename);
    }
}