<?php

declare(strict_types=1);

use App\Http\Controllers\AssetQrPrintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMAS-RS (RS UMMI Bogor)
|--------------------------------------------------------------------------
|
| The entire hospital asset management application is powered natively by
| the Filament Admin Panel at '/admin'.
|
*/

// Redirect root and legacy endpoints to Filament Admin Panel
Route::redirect('/', '/admin')->name('home');
Route::redirect('/dashboard', '/admin')->name('dashboard');
Route::redirect('/login', '/admin/login')->name('login');

// Square QR Code Print Routes (Authenticated)
Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/admin/assets/{asset}/print-qr', [AssetQrPrintController::class, 'show'])->name('assets.print-qr');
    Route::get('/admin/assets-print-qr-bulk', [AssetQrPrintController::class, 'bulk'])->name('assets.print-qr-bulk');
});
