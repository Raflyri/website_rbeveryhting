<?php

namespace App\Jobs;

use App\Models\ApiCallLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResolveIpCountry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 10;

    public function __construct(public readonly int $logId) {}

    public function handle(): void
    {
        $log = ApiCallLog::find($this->logId);
        if (! $log) {
            return;
        }

        $ip = $log->ip;

        // Local / private IPs — skip API call
        if (! $ip || $ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            $log->update(['country' => 'Local', 'country_code' => '--']);
            return;
        }

        try {
            $response = Http::timeout(8)->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode");

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'success') {
                    $log->update([
                        'country'      => $data['country'] ?? null,
                        'country_code' => $data['countryCode'] ?? null,
                    ]);
                    return;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('[ResolveIpCountry] Failed to resolve IP ' . $ip . ': ' . $e->getMessage());
        }

        $log->update(['country' => 'Unknown', 'country_code' => '??']);
    }
}
