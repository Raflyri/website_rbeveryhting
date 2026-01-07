<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Helper untuk Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// --- LOGIKA DETEKSI JALUR (REVISI FINAL) ---

// 1. Cek Jalur STAGING (Prioritas)
// Karena staging ada di subfolder, kita harus mundur 2 langkah (../../)
// Mencari: /home/user/projects/staging/vendor/autoload.php
if (file_exists(__DIR__.'/../../projects/sandbox/vendor/autoload.php')) {
    require __DIR__.'/../../projects/sandbox/vendor/autoload.php';
    $app = require __DIR__.'/../../projects/sandbox/bootstrap/app.php';
}

// 2. Cek Jalur PRODUCTION
// Production ada di root, cukup mundur 1 langkah (../)
// Mencari: /home/user/projects/production/vendor/autoload.php
elseif (file_exists(__DIR__.'/../projects/production/vendor/autoload.php')) {
    require __DIR__.'/../projects/production/vendor/autoload.php';
    $app = require __DIR__.'/../projects/production/bootstrap/app.php';
}

// 3. Cek Jalur LOCAL / DEFAULT
// Fallback kalau dijalankan di laptop
else {
    require __DIR__.'/../vendor/autoload.php';
    $app = require __DIR__.'/../bootstrap/app.php';
}

// --- EKSEKUSI ---
$app->handleRequest(Request::capture());