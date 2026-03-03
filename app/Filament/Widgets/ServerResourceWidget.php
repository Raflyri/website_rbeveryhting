<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ServerResourceWidget extends Widget
{
    protected static string  $view            = 'filament.widgets.server-resource-widget';
    protected static ?string $pollingInterval = '15s';
    protected int|string|array $columnSpan   = 'full';
    protected static ?int    $sort            = 2;

    protected function getViewData(): array
    {
        return [
            'cpu'    => $this->cpuLoad(),
            'ram'    => $this->ramUsage(),
            'disk'   => $this->diskUsage(),
            'php'    => PHP_VERSION,
            'os'     => php_uname('s') . ' ' . php_uname('r'),
            'uptime' => $this->serverUptime(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CPU
    |--------------------------------------------------------------------------
    */
    private function cpuLoad(): array
    {
        if (function_exists('sys_getloadavg')) {
            $loads = sys_getloadavg();
            $cores = $this->cpuCores();
            $percent = $cores > 0 ? round(($loads[0] / $cores) * 100, 1) : round($loads[0] * 100, 1);
            $percent = min($percent, 100);
            return [
                'load1'   => round($loads[0], 2),
                'load5'   => round($loads[1], 2),
                'load15'  => round($loads[2], 2),
                'percent' => $percent,
                'cores'   => $cores,
            ];
        }

        // Windows fallback — not easily available without exec
        return ['load1' => null, 'load5' => null, 'load15' => null, 'percent' => null, 'cores' => null];
    }

    private function cpuCores(): int
    {
        if (is_file('/proc/cpuinfo')) {
            $cores = substr_count(file_get_contents('/proc/cpuinfo'), 'processor');
            return $cores ?: 1;
        }
        return 1;
    }

    /*
    |--------------------------------------------------------------------------
    | RAM
    |--------------------------------------------------------------------------
    */
    private function ramUsage(): array
    {
        // Linux
        if (is_file('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)/i', $meminfo, $total);
            preg_match('/MemAvailable:\s+(\d+)/i', $meminfo, $available);

            if ($total && $available) {
                $totalKb     = (int) $total[1];
                $availableKb = (int) $available[1];
                $usedKb      = $totalKb - $availableKb;
                $percent     = round(($usedKb / $totalKb) * 100, 1);

                return [
                    'total'   => $this->formatBytes($totalKb * 1024),
                    'used'    => $this->formatBytes($usedKb * 1024),
                    'free'    => $this->formatBytes($availableKb * 1024),
                    'percent' => $percent,
                ];
            }
        }

        // PHP process fallback (Windows / shared hosting)
        $used    = memory_get_usage(true);
        $limit   = $this->phpMemoryLimit();
        $percent = $limit > 0 ? round(($used / $limit) * 100, 1) : null;

        return [
            'total'   => $limit > 0 ? $this->formatBytes($limit) : 'Unknown',
            'used'    => $this->formatBytes($used),
            'free'    => $limit > 0 ? $this->formatBytes($limit - $used) : 'Unknown',
            'percent' => $percent,
            'note'    => 'PHP process memory (server total not available)',
        ];
    }

    private function phpMemoryLimit(): int
    {
        $raw = ini_get('memory_limit');
        if ($raw === '-1') return 0;

        $unit  = strtolower(substr($raw, -1));
        $value = (int) $raw;

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Disk
    |--------------------------------------------------------------------------
    */
    private function diskUsage(): array
    {
        $path  = base_path();
        $total = disk_total_space($path);
        $free  = disk_free_space($path);
        $used  = $total - $free;

        return [
            'total'   => $this->formatBytes($total),
            'used'    => $this->formatBytes($used),
            'free'    => $this->formatBytes($free),
            'percent' => $total > 0 ? round(($used / $total) * 100, 1) : 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Uptime
    |--------------------------------------------------------------------------
    */
    private function serverUptime(): ?string
    {
        if (is_file('/proc/uptime')) {
            $seconds = (int) explode(' ', file_get_contents('/proc/uptime'))[0];
            $days    = intdiv($seconds, 86400);
            $hours   = intdiv($seconds % 86400, 3600);
            $minutes = intdiv($seconds % 3600, 60);

            return "{$days}d {$hours}h {$minutes}m";
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    private function formatBytes(int|float $bytes): string
    {
        if ($bytes >= 1024 ** 3) return round($bytes / (1024 ** 3), 2) . ' GB';
        if ($bytes >= 1024 ** 2) return round($bytes / (1024 ** 2), 2) . ' MB';
        if ($bytes >= 1024)      return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
