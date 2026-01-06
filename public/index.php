<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
} 

// --- LOGIKA PENENTU JALUR (Production vs Sandbox/Local) ---

// 1. Cek apakah folder vendor ada di "belakang" folder public ini?
// Kalau ada, berarti kita sedang di SANDBOX (atau Localhost).
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
    $app = require __DIR__.'/../bootstrap/app.php';
} 
// 2. Kalau tidak ada, berarti kita di PRODUCTION (public_html).
// Kita harus ambil file dari folder 'projects/production'.
else {
    require __DIR__.'/../projects/production/vendor/autoload.php';
    $app = require __DIR__.'/../projects/production/bootstrap/app.php';
}

// Register the Composer autoloader...
//require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
//$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
