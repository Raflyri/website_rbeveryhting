<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Models\LandingSetting;
use App\Models\Service;

// --- ROUTE DARURAT UNTUK SHARED HOSTING (Tanpa SSH) ---
// Akses URL ini nanti: domain.com/setup-server-darurat?key=rahasia123
Route::get('/option-2-rute', function () {

    // 1. Keamanan Sederhana (Biar gak sembarang orang akses)
    if (request()->get('key') !== 'R4flyB14nca**12#') {
        abort(403, 'Unauthorized action.');
    }

    $output = "<h1>Server Setup Result:</h1>";

    try {
        // 2. Update Database (Migrate)
        // Setara: php artisan migrate --force
        Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output .= "<strong>Migration:</strong><br><pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre><hr>";

        // 3. Bersihkan Cache Config (Wajib setelah update env/file config)
        // Setara: php artisan optimize:clear
        Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $output .= "<strong>Cache Clear:</strong><br><pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre><hr>";

        // 4. Install Symlink Storage (Biar gambar muncul)
        // Setara: php artisan storage:link
        // Note: Di shared hosting kadang perlu trik khusus path, tapi coba standar dulu.
        try {
            Illuminate\Support\Facades\Artisan::call('storage:link');
            $output .= "<strong>Storage Link:</strong><br><pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
        } catch (\Exception $e) {
            $output .= "<strong>Storage Link Error:</strong> " . $e->getMessage();
        }
    } catch (\Exception $e) {
        $output .= "<h2 style='color:red'>ERROR: " . $e->getMessage() . "</h2>";
    }

    return $output;
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
    $availabeLocales = ['en_US', 'en_GB', 'id', 'ms', 'ja'];

    if (in_array($locale, $availabeLocales)) {
        session::put('locale', $locale);
    }

    return redirect()->back();
})->name('switch.language');
