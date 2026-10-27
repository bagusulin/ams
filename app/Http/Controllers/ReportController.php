<?php

namespace App\Http\Controllers;

use App\Exports\AssignmentsExport;
use App\Exports\AssetsExport;
use App\Exports\MaintenancesExport;
use App\Exports\RentalExpiryExport;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\AssetMaintenance;
use App\Models\Location;
use App\Models\User;
use App\Support\Reports\RentalExpiryQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 6.1 ASSET REPORT
    |--------------------------------------------------------------------------
    */

    public function assets(Request $request)
    {
        Gate::authorize('reports.assets.view');
        $query = Asset::query()->with([
            'category',
            'location',
            'vendor',
        ]);

        $this->applyAssetFilters($query, $request);
        $user = Auth::user();
        abort_unless($user, 403);
        AssetAccess::assetsQuery($user, $query);

        $assets = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $categories = AssetAccess::categoriesQueryFor(Auth::user())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('reports.assets', [
            'assets' => $assets,
            'categories' => $categories,
            'locations' => $locations,
            'filters' => $request->query(),
        ]);
    }

    public function exportAssetsExcel(Request $request)
    {
        Gate::authorize('reports.assets.export');
        $filename = 'asset-report-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new AssetsExport($request->all()),
            $filename
        );
    }

    public function exportAssetsPdf(Request $request)
    {
        Gate::authorize('reports.assets.export');
        $query = Asset::query()->with([
            'category',
            'location',
            'vendor',
        ]);

        $this->applyAssetFilters($query, $request);

        $assets = $query
            ->orderBy('asset_code')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.assets', [
            'assets' => $assets,
            'filters' => $request->query(),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'asset-report-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    private function applyAssetFilters(
        Builder $query,
        Request $request
    ): void {
        if ($request->filled('search')) {
            $search = '%' . trim($request->string('search')->toString()) . '%';

            $query->where(function (Builder $q) use ($search) {
                $q->where('asset_code', 'like', $search)
                    ->orWhere('brand', 'like', $search)
                    ->orWhere('model', 'like', $search)
                    ->orWhere('serial_number', 'like', $search);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 6.2 ASSIGNMENT & RETURN REPORT
    |--------------------------------------------------------------------------
    */

    public function assignments(Request $request)
    {
        Gate::authorize('reports.assignments.view');
        $type = $request->input('type', 'active');

        if (!in_array($type, ['active', 'returned'], true)) {
            $type = 'active';
        }

        $query = $this->assignmentQuery($request, $type);

        if ($type === 'active') {
            $query->latest('assigned_at');
        } else {
            $query->latest('returned_at');
        }

        $assignments = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $users = User::query()
            ->orderBy('name')
            ->get();

        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $user = Auth::user();
        abort_unless($user, 403);

        $summaryQuery = AssetAssignment::query()
            ->whereHas('asset', function (Builder $assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            });

        if ($type === 'active') {
            $summaryQuery->whereNull('returned_at');
        } else {
            $summaryQuery->whereNotNull('returned_at');
        }

        $totalAssignments = $summaryQuery->count();

        return view('reports.assignments', [
            'assignments' => $assignments,
            'users' => $users,
            'locations' => $locations,
            'type' => $type,
            'totalAssignments' => $totalAssignments,
        ]);
    }

    public function exportAssignmentsExcel(Request $request)
    {
        Gate::authorize('reports.assignments.export');
        $type = $request->input('type', 'active');

        if (!in_array($type, ['active', 'returned'], true)) {
            $type = 'active';
        }

        $filename = (
            $type === 'active'
                ? 'assignment-active-report-'
                : 'return-history-report-'
        ) . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new AssignmentsExport($request->all()),
            $filename
        );
    }

    public function exportAssignmentsPdf(Request $request)
    {
        Gate::authorize('reports.assignments.export');
        $type = $request->input('type', 'active');

        if (!in_array($type, ['active', 'returned'], true)) {
            $type = 'active';
        }

        $assignments = $this->assignmentQuery($request, $type)
            ->when(
                $type === 'active',
                fn (Builder $query) => $query->latest('assigned_at'),
                fn (Builder $query) => $query->latest('returned_at')
            )
            ->latest('id')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.assignments', [
            'assignments' => $assignments,
            'type' => $type,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = (
            $type === 'active'
                ? 'assignment-active-report-'
                : 'return-history-report-'
        ) . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }

    private function assignmentQuery(
        Request $request,
        string $type
    ): Builder {
        $user = Auth::user();
        abort_unless($user, 403);
        $query = AssetAssignment::query()->with([
            'asset.category',
            'asset.location',
            'user',
            'location',
        ]);
        $query->whereHas('asset', function (Builder $assetQuery) use ($user) {
            AssetAccess::assetsQuery($user, $assetQuery);
        });

        if ($type === 'active') {
            $query->whereNull('returned_at');
        } else {
            $query->whereNotNull('returned_at');
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->string('search')->toString()) . '%';

            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('asset', function (Builder $assetQuery) use ($search) {
                    $assetQuery->where('asset_code', 'like', $search)
                        ->orWhere('brand', 'like', $search)
                        ->orWhere('model', 'like', $search)
                        ->orWhere('serial_number', 'like', $search);
                })->orWhereHas('user', function (Builder $userQuery) use ($search) {
                    $userQuery->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        $dateColumn = $type === 'active'
            ? 'assigned_at'
            : 'returned_at';

        if ($request->filled('date_from')) {
            $query->whereDate(
                $dateColumn,
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                $dateColumn,
                '<=',
                $request->input('date_to')
            );
        }

        return $query;
    }


    /*
    |--------------------------------------------------------------------------
    | 6.3 MAINTENANCE REPORT
    |--------------------------------------------------------------------------
    */

    public function maintenances(Request $request)
    {
        Gate::authorize('reports.maintenances.view');
        $query = $this->maintenanceQuery($request);

        $maintenances = (clone $query)
            ->latest('maintenance_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $totalMaintenance = (clone $query)->count();

        $inProgressMaintenance = (clone $query)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $completedMaintenance = (clone $query)
            ->where('status', 'completed')
            ->count();

        $totalCost = (clone $query)->sum('cost');

        $categories = AssetAccess::categoriesQueryFor(Auth::user())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('reports.maintenances', [
            'maintenances' => $maintenances,
            'categories' => $categories,
            'locations' => $locations,
            'totalMaintenance' => $totalMaintenance,
            'inProgressMaintenance' => $inProgressMaintenance,
            'completedMaintenance' => $completedMaintenance,
            'totalCost' => $totalCost,
        ]);
    }

    public function exportMaintenancesExcel(Request $request)
    {
        Gate::authorize('reports.maintenances.export');
        return Excel::download(
            new MaintenancesExport($request->all()),
            'maintenance-report-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function exportMaintenancesPdf(Request $request)
    {
        Gate::authorize('reports.maintenances.export');
        $maintenances = $this->maintenanceQuery($request)
            ->latest('maintenance_date')
            ->latest('id')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.maintenances', [
            'maintenances' => $maintenances,
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'maintenance-report-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    private function maintenanceQuery(Request $request): Builder
    {
        $user = Auth::user();
        abort_unless($user, 403);
        Gate::authorize('reports.maintenances.view');
        $query = AssetMaintenance::query()->with([
            'asset.category',
            'asset.location',
            'asset.vendor',
        ]);
        
        $query->whereHas('asset', function (Builder $assetQuery) use ($user) {
            AssetAccess::assetsQuery($user, $assetQuery);
        });

        if ($request->filled('search')) {
            $search = '%' . trim($request->string('search')->toString()) . '%';

            $query->where(function (Builder $q) use ($search) {
                $q->where('type', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('action_taken', 'like', $search)
                    ->orWhereHas('asset', function (Builder $assetQuery) use ($search) {
                        $assetQuery->where('asset_code', 'like', $search)
                            ->orWhere('brand', 'like', $search)
                            ->orWhere('model', 'like', $search)
                            ->orWhere('serial_number', 'like', $search);
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('asset', function (Builder $assetQuery) use ($request) {
                $assetQuery->where(
                    'category_id',
                    $request->input('category_id')
                );
            });
        }

        if ($request->filled('location_id')) {
            $query->whereHas('asset', function (Builder $assetQuery) use ($request) {
                $assetQuery->where(
                    'location_id',
                    $request->input('location_id')
                );
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'maintenance_date',
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'maintenance_date',
                '<=',
                $request->input('date_to')
            );
        }

        return $query;
    }


    /*
    |--------------------------------------------------------------------------
    | 6.4 RENTAL EXPIRY REPORT
    |--------------------------------------------------------------------------
    */

    public function rentalExpiry(Request $request)
    {
        Gate::authorize('reports.rental-expiry.view');
        $filters = $this->validateRentalExpiryFilters($request);

        $bucket = $filters['bucket'] ?? 'all';

        /*
         * Tabel menerapkan seluruh filter, termasuk bucket.
         */
        $assets = RentalExpiryQuery::build($filters)
            ->orderBy('rental_end_date')
            ->orderBy('asset_code')
            ->paginate(15)
            ->withQueryString();

        /*
         * Summary mengabaikan bucket yang sedang dipilih,
         * tetapi tetap mengikuti filter lain.
         */
        $summaryQuery = RentalExpiryQuery::build($filters, false);

        $today = today();
        $day7 = $today->copy()->addDays(7);
        $day60 = $today->copy()->addDays(60);

        $totalRentalAssets = (clone $summaryQuery)->count();

        $expiredCount = (clone $summaryQuery)
            ->whereDate('rental_end_date', '<', $today)
            ->count();

        $due7Count = (clone $summaryQuery)
            ->whereDate('rental_end_date', '>=', $today)
            ->whereDate('rental_end_date', '<=', $day7)
            ->count();

        $due8To60Count = (clone $summaryQuery)
            ->whereDate('rental_end_date', '>', $day7)
            ->whereDate('rental_end_date', '<=', $day60)
            ->count();

        $over60Count = (clone $summaryQuery)
            ->whereDate('rental_end_date', '>', $day60)
            ->count();

        $categories = AssetAccess::categoriesQueryFor(Auth::user())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('reports.rental-expiry', [
            'assets' => $assets,
            'categories' => $categories,
            'locations' => $locations,
            'bucket' => $bucket,
            'totalRentalAssets' => $totalRentalAssets,
            'expiredCount' => $expiredCount,
            'due7Count' => $due7Count,
            'due8To60Count' => $due8To60Count,
            'over60Count' => $over60Count,
        ]);
    }

    public function exportRentalExpiryExcel(Request $request)
    {
        Gate::authorize('reports.rental-expiry.export');
        $filters = $this->validateRentalExpiryFilters($request);

        return Excel::download(
            new RentalExpiryExport($filters),
            'rental-expiry-report-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function exportRentalExpiryPdf(Request $request)
    {
        Gate::authorize('reports.rental-expiry.export');
        $filters = $this->validateRentalExpiryFilters($request);

        $assets = RentalExpiryQuery::build($filters)
            ->orderBy('rental_end_date')
            ->orderBy('asset_code')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.rental-expiry', [
            'assets' => $assets,
            'bucket' => $filters['bucket'] ?? 'all',
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'rental-expiry-report-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    private function validateRentalExpiryFilters(Request $request): array
    {
        return $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:asset_categories,id',
            ],

            'location_id' => [
                'nullable',
                'integer',
                'exists:locations,id',
            ],

            'asset_status' => [
                'nullable',
                'in:available,assigned,maintenance,lost',
            ],

            'bucket' => [
                'nullable',
                'in:all,expired,due_7,due_8_60,over_60',
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ]);
    }
}