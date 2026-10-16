<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use F9WebLtd\QrCode\Generator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AssetQrController extends Controller
{
    /**
     * Halaman daftar asset dengan QR Code.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user, 403);
        Gate::authorize('asset-qr.view');
        $query = AssetAccess::assetsQuery(
            $user,
            Asset::query()->with([
                'category',
                'location',
                'vendor',
            ])
        );

        if ($request->filled('search')) {
            $search = '%' . trim($request->input('search')) . '%';

            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', $search)
                    ->orWhere('brand', 'like', $search)
                    ->orWhere('model', 'like', $search)
                    ->orWhere('serial_number', 'like', $search);
            });
        }

        $assets = $query
            ->orderBy('asset_code')
            ->paginate(15)
            ->withQueryString();

        return view('asset-qr.index', compact('assets'));
    }

    /**
     * Halaman detail asset yang dibuka melalui QR.
     */
    public function detail(Asset $asset): View
    {
        $user = Auth::user();
        abort_unless($user, 403);
        Gate::authorize('asset-qr.view');
        abort_unless(
            AssetAccess::canView($user, $asset),
            404
        );
        $asset->load([
            'category',
            'location',
            'vendor',
            'currentAssignment.user',
            'currentAssignment.location',
        ]);

        $assignments = $asset->assignments()
            ->with(['user', 'location'])
            ->latest('assigned_at')
            ->take(10)
            ->get();

        $histories = $asset->histories()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        $maintenances = $asset->maintenances()
            ->latest('maintenance_date')
            ->take(10)
            ->get();

        return view('asset-qr.detail', compact(
            'asset',
            'assignments',
            'histories',
            'maintenances'
        ));
    }

    /**
     * Menghasilkan QR Code dalam format SVG.
     */
    public function image(Asset $asset): Response
    {
        $user = Auth::user();
        abort_unless($user, 403);
        Gate::authorize('asset-qr.view');
        abort_unless(
            AssetAccess::canView($user, $asset),
            404
        );
        $targetUrl = route('asset-qr.detail', [
            'asset' => $asset->id,
        ]);

        $generator = new Generator();

        $svg = $generator
            ->format('svg')
            ->size(280)
            ->generate($targetUrl);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Halaman cetak label asset.
     */
    public function printLabel(Asset $asset): View
    {
        $user = Auth::user();
        abort_unless($user, 403);
        Gate::authorize('asset-qr.view');
        abort_unless(
            AssetAccess::canView($user, $asset),
            404
        );
        $asset->load([
            'category',
            'location',
        ]);

        return view('asset-qr.label', compact('asset'));
    }
}
