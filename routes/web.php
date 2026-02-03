<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Models\LandingSetting;
use App\Models\Service;
use App\Http\Controllers\System\DeploymentController;

// --- SYSTEM DEPLOYMENT ROUTE ---
Route::get('/system/deploy/trigger', [DeploymentController::class, 'handle']);

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
    $availabeLocales = ['en_US', 'en_GB', 'id', 'ms', 'ja'];

    if (in_array($locale, $availabeLocales)) {
        session::put('locale', $locale);
    }

    return redirect()->back();
})->name('switch.language');
