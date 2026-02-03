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
        $commands = [
            'migrate' => ['command' => 'migrate', 'params' => ['--force' => true]],
            'optimize' => ['command' => 'optimize:clear', 'params' => []],
            'storage' => ['command' => 'storage:link', 'params' => []],
            'view' => ['command' => 'view:cache', 'params' => []],
            'config' => ['command' => 'config:cache', 'params' => []],
            'filament' => ['command' => 'filament:upgrade', 'params' => []],
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

            return response()->json([
                'status' => 'success',
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
