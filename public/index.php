<?php

// Nyalakan Error Reporting (Hanya untuk debugging, matikan nanti saat live)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// --- LOGIKA DETEKSI JALUR (FIXED) ---

// 1. Cek Jalur STAGING (Prioritas Utama)
// Posisi: /home/user/public_html/staging.web.rbeverything.com/index.php
// Target: /home/user/projects/staging/vendor/autoload.php
// Logika: Mundur 2 langkah (../../) lalu masuk ke projects/staging
if (file_exists(__DIR__.'/../../projects/sandbox/vendor/autoload.php')) {
    
    // Cek Maintenance Mode Staging
    if (file_exists($maintenance = __DIR__.'/../../projects/sandbox/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require __DIR__.'/../../projects/sandbox/vendor/autoload.php';
    $app = require __DIR__.'/../../projects/sandbox/bootstrap/app.php';
}

// 2. Cek Jalur PRODUCTION
// Posisi: /home/user/public_html/index.php
// Target: /home/user/projects/production/vendor/autoload.php
// Logika: Mundur 1 langkah (../) lalu masuk ke projects/production
elseif (file_exists(__DIR__.'/../projects/production/vendor/autoload.php')) {

    // Cek Maintenance Mode Production
    if (file_exists($maintenance = __DIR__.'/../projects/production/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require __DIR__.'/../projects/production/vendor/autoload.php';
    $app = require __DIR__.'/../projects/production/bootstrap/app.php';
}

// 3. Cek Jalur LOCAL (Laptop)
else {
    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require __DIR__.'/../vendor/autoload.php';
    $app = require __DIR__.'/../bootstrap/app.php';
}

// --- EKSEKUSI ---
$app->handleRequest(Request::capture());