<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DeploymentController extends Controller
{
    /**
     * Handle post-deployment tasks.
     * This route should be protected by a secret key.
     */
    public function handle(Request $request)
    {
        // 1. Validate Secret Key
        $inputKey = $request->input('key');
        $validKey = config('app.deploy_secret'); // We will add this to config

        if (!$validKey || $inputKey !== $validKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid or missing deployment key.'
            ], 403);
        }

        // 2. Define Commands to Run
        // NOTE: 'storage:link' and 'filament:upgrade' are intentionally excluded.
        // storage:link calls exec()/symlink() which are disabled on cPanel hosting.
        // filament:upgrade calls Composer which is unavailable in a web process.
        // The public/storage symlink must be created once manually via cPanel File Manager.
        $commands = [
            'migrate'  => ['command' => 'migrate',        'params' => ['--force' => true]],
            'optimize' => ['command' => 'optimize:clear',  'params' => []],
            'view'     => ['command' => 'view:cache',      'params' => []],
            'config'   => ['command' => 'config:cache',    'params' => []],
        ];

        $results = [];

        try {
            // 3. Execute Commands
            foreach ($commands as $key => $cmd) {
                try {
                    Artisan::call($cmd['command'], $cmd['params']);
                    $results[$key] = [
                        'status' => 'success',
                        'output' => Artisan::output(),
                    ];
                } catch (\Exception $e) {
                    $results[$key] = [
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ];
                    Log::error("Deployment command failed: {$cmd['command']}", ['error' => $e]);
                }
            }

            // 4. Report symlink status (informational — cannot create via web process on cPanel)
            $storageLinkPath = public_path('storage');
            $results['symlink_check'] = [
                'status'  => is_link($storageLinkPath) ? 'exists' : 'missing',
                'message' => is_link($storageLinkPath)
                    ? '✅ public/storage symlink exists.'
                    : '⚠️  public/storage symlink is MISSING. Create it manually via cPanel File Manager.',
            ];

            return response()->json([
                'status'  => 'success',
                'message' => 'Deployment tasks executed.',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Critical deployment error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
