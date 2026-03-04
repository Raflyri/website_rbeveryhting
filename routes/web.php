<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Models\LandingSetting;
use App\Models\Service;
use App\Http\Controllers\System\DeploymentController;
use App\Http\Controllers\Base64ConverterController;
use App\Http\Controllers\Base64ToolController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;

// --- SYSTEM DEPLOYMENT ROUTE ---
Route::get('/system/deploy/trigger', [DeploymentController::class, 'handle']);

// --- TOOLS ROUTES ---
Route::get('/tools/base64', [Base64ConverterController::class, 'index'])->name('tools.base64');

Route::prefix('tools/base64')->name('tools.base64.')->group(function () {
    // SPA: fetch panel HTML for a tool (no full reload)
    Route::get('ui/{slug}',  [Base64ToolController::class, 'panel'])->name('panel');
    // SPA: JSON form submission endpoint
    Route::post('api/{slug}', [Base64ToolController::class, 'apiHandle'])->name('api');
    // Classic (fallback / no-JS)
    Route::get('{slug}', [Base64ToolController::class, 'show'])->name('show');
    Route::post('{slug}', [Base64ToolController::class, 'handle'])->name('handle');
});

/**
 * Route::get('/', function () {
 * return view('welcome');
 * });
 */

Route::get('/', function () {
    // Ambil settingan pertama
    $setting = LandingSetting::first();

    // Cek status (Default true jika data belum ada sama sekali)
    $isMaintenance = $setting ? $setting->is_maintenance_mode : true;

    if ($isMaintenance) {
        // Tampilkan halaman 'Coming Soon' (yang sekarang welcome.blade.php)
        return view('welcome', ['setting' => $setting]);
    } else {
        // Tampilkan halaman 'Live/Utama' (Nanti kita buat home.blade.php)
        return view('home', ['setting' => $setting]);
    }
});

Route::get('/lang/{locale}', function ($locale) {
    $availableLocales = ['en_US', 'en_GB', 'id', 'ms', 'ja'];

    if (in_array($locale, $availableLocales)) {
        Session::put('locale', $locale);
    }

    return redirect()->back();
})->name('switch.language');

// --- CMS PAGES ROUTE ---
Route::get('/p/{slug}', [PageController::class, 'show'])->name('page.show');

// --- INSIGHTS (News / Articles / Blog) ---
Route::get('/insights', [PostController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [PostController::class, 'show'])->name('insights.show');

// Impersonation
Route::get('/impersonate/leave', [\App\Http\Controllers\ImpersonationController::class, 'leave'])->name('impersonate.leave');
Route::get('/impersonate/{user}', [\App\Http\Controllers\ImpersonationController::class, 'enter'])->name('impersonate.enter');
