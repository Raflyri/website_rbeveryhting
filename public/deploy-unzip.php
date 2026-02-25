<?php

/**
 * deploy-unzip.php – Server-side deployment unzipper
 *
 * Receives a GET request from the CI/CD pipeline after zip archives
 * have been uploaded. Extracts core.zip (app code) and/or public.zip
 * (public assets) then cleans up the zip files.
 *
 * Security: key is read from the DEPLOY_UNZIP_KEY environment variable
 * with a hardcoded fallback for backward compatibility.
 */

// ── AUTHENTICATION ──────────────────────────────────────────────────
$validKey = getenv('DEPLOY_UNZIP_KEY') ?: 'R4flyB14nca**12#';
$inputKey = $_GET['key'] ?? '';

if (!$inputKey || $inputKey !== $validKey) {
    http_response_code(403);
    header('Content-Type: application/json');
    die(json_encode([
        'status'  => 'error',
        'message' => '⛔ Unauthorized: Invalid or missing deployment key.',
    ]));
}

// ── PATH DEFINITIONS ────────────────────────────────────────────────
$publicDir = __DIR__;                  // public/ or public_html/
$rootDir   = dirname(__DIR__);         // project root (one level up)

$archives = [
    'core'   => [
        'zip'     => $rootDir . '/core.zip',
        'dest'    => $rootDir,
        'label'   => 'Core (app + vendor)',
    ],
    'public' => [
        'zip'     => $publicDir . '/public.zip',
        'dest'    => $publicDir,
        'label'   => 'Public assets',
    ],
];

// ── EXTRACT ─────────────────────────────────────────────────────────
$results  = [];
$hasError = false;

foreach ($archives as $key => $archive) {
    if (!file_exists($archive['zip'])) {
        $results[$key] = [
            'status'  => 'skipped',
            'message' => "No {$archive['label']} zip found — skipped.",
        ];
        continue;
    }

    $zip = new ZipArchive();
    if ($zip->open($archive['zip']) === true) {
        $zip->extractTo($archive['dest']);
        $zip->close();

        // Clean up zip after extraction
        unlink($archive['zip']);

        $results[$key] = [
            'status'  => 'success',
            'message' => "✅ {$archive['label']} extracted successfully.",
        ];
    } else {
        $hasError = true;
        $results[$key] = [
            'status'  => 'error',
            'message' => "❌ Failed to open {$archive['label']} zip.",
        ];
    }
}

// Also handle legacy single artifact.zip (backward compatibility)
$legacyZip = $rootDir . '/artifact.zip';
if (file_exists($legacyZip)) {
    $zip = new ZipArchive();
    if ($zip->open($legacyZip) === true) {
        $zip->extractTo($rootDir);
        $zip->close();
        unlink($legacyZip);
        $results['legacy'] = [
            'status'  => 'success',
            'message' => '✅ Legacy artifact.zip extracted.',
        ];
    }
}

// ── RESPONSE ────────────────────────────────────────────────────────
header('Content-Type: application/json');
http_response_code($hasError ? 500 : 200);
echo json_encode([
    'status'  => $hasError ? 'partial_error' : 'success',
    'message' => $hasError
        ? 'Some archives failed to extract.'
        : '✅ All archives extracted successfully.',
    'results' => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
