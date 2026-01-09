<?php
// AMANKAN AKSES: Hanya izinkan jika ada kunci rahasia yang cocok
if (($_GET['key'] ?? '') !== 'R4flyB14nca**12#') {
    http_response_code(403);
    die('⛔ Akses Ditolak.');
}

// DEFINISI PATH
$rootDir = __DIR__ . '/../'; // Naik satu level ke root project
$zipFile = $rootDir . 'artifact.zip';

// PROSES UNZIP
if (file_exists($zipFile)) {
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        // Ekstrak file, menimpa yang lama
        $zip->extractTo($rootDir);
        $zip->close();
        
        // Hapus file zip setelah selesai agar hemat space
        unlink($zipFile);
        
        echo "✅ SUCCESS: Website berhasil di-update & diekstrak!";
    } else {
        http_response_code(500);
        echo "❌ ERROR: Gagal membuka file zip.";
    }
} else {
    http_response_code(404);
    echo "❌ ERROR: File artifact.zip tidak ditemukan di server.";
}