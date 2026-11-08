<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetQrPrintController extends Controller
{
    /**
     * Print QR code sticker label for a single asset.
     */
    public function show(Request $request, Asset $asset): View
    {
        if (! ($request->user()?->isAdmin() || $request->user()?->isAssetStaff())) {
            abort(403, 'Akses ditolak. Fitur cetak label hanya diperuntukkan bagi Administrator Sistem dan Petugas Aset.');
        }

        return view('assets.print-qr', [
            'assets' => collect([$asset->load(['category', 'department', 'location'])]),
            'title' => 'Cetak Stiker Single - '.$asset->asset_code,
            'size' => $request->query('size', 'single'),
            'layout' => $request->query('layout', '3x5'),
        ]);
    }

    /**
     * Print batch QR code sticker labels for multiple selected assets.
     */
    public function bulk(Request $request): View
    {
        if (! ($request->user()?->isAdmin() || $request->user()?->isAssetStaff())) {
            abort(403, 'Akses ditolak. Fitur cetak label hanya diperuntukkan bagi Administrator Sistem dan Petugas Aset.');
        }

        $rawIds = $request->query('ids', '');
        $ids = array_filter(explode(',', (string) $rawIds), fn ($id) => is_numeric($id));

        $query = Asset::query()->with(['category', 'department', 'location']);
        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        } else {
            $query->orderBy('asset_code')->take(30);
        }

        $assets = $query->get();

        return view('assets.print-qr', [
            'assets' => $assets,
            'title' => 'Cetak Batch QR Code Barang ('.$assets->count().' Aset)',
            'size' => $request->query('size', '65mm'),
            'layout' => $request->query('layout', '3x5'),
        ]);
    }
}
