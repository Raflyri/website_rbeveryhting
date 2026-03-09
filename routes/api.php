<?php

use App\Http\Controllers\Base64ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes serve the base64tools.rbeverything.com static subdomain.
| They are stateless (no session, no CSRF) and protected only by the
| CORS middleware which restricts access to the allowed origin.
|
*/

// ── Base64 Tools JSON API ──────────────────────────────────────────────────
Route::prefix('base64')
    ->middleware(['cors.base64tools'])
    ->group(function () {
        // Returns all active tools + their field configs as JSON
        Route::get('tools', [Base64ApiController::class, 'index']);
        Route::options('tools', fn() => response('', 204));

        // Forwards a tool call to the external API, logs it, returns JSON
        Route::post('proxy/{slug}', [Base64ApiController::class, 'proxy']);
        Route::options('proxy/{slug}', fn() => response('', 204));
    });
