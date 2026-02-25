<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Str;

class ApiCallLogs extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'API Call Logs';
    protected static ?string $navigationGroup = 'Tools & Utilities';
    protected static ?int    $navigationSort  = 99;
    protected static ?string $title           = 'API Call Logs';
    protected static string  $view            = 'filament.pages.api-call-logs';

    /** Number of lines to read from the end of the log */
    public int $lines = 200;

    /** Filter: 'all' | 'info' | 'warning' | 'error' */
    public string $level = 'all';

    /** Free-text search filter */
    public string $searchTerm = '';

    protected function getViewData(): array
    {
        return [
            'entries' => $this->parsedEntries(),
        ];
    }

    private function logPath(): string
    {
        return storage_path('logs/laravel.log');
    }

    private function parsedEntries(): array
    {
        $path = $this->logPath();
        if (! file_exists($path)) {
            return [];
        }

        // Read the last N lines efficiently
        $all   = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $tail  = array_slice($all, -$this->lines);

        $entries = [];
        $buffer  = null;

        foreach ($tail as $line) {
            // New log entry starts with a timestamp: [YYYY-MM-DD HH:MM:SS]
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $m)) {
                if ($buffer) {
                    $entries[] = $buffer;
                }
                $buffer = $this->parseLine($line, $m[1]);
            } elseif ($buffer) {
                // Continuation of previous entry (stack trace etc.)
                $buffer['extra'] = ($buffer['extra'] ?? '') . "\n" . $line;
            }
        }
        if ($buffer) {
            $entries[] = $buffer;
        }

        // Keep only Base64 SPA entries
        $entries = array_filter($entries, fn($e) => Str::contains($e['message'], '[Base64 SPA]'));

        // Level filter
        if ($this->level !== 'all') {
            $entries = array_filter($entries, fn($e) => strtolower($e['level']) === $this->level);
        }

        // Search filter
        if ($this->searchTerm !== '') {
            $entries = array_filter(
                $entries,
                fn($e) =>
                Str::contains(strtolower($e['message'] . ($e['context'] ?? '')), strtolower($this->searchTerm))
            );
        }

        return array_reverse(array_values($entries));
    }

    private function parseLine(string $line, string $timestamp): array
    {
        // Format: [datetime] channel.LEVEL: message {"context":"..."} []
        preg_match('/\]\s+\S+\.(\w+):\s+(.+?)(\s+\{.*)?$/', $line, $m);

        $level   = strtolower($m[1] ?? 'info');
        $message = trim($m[2] ?? $line);
        $context = isset($m[3]) ? trim($m[3]) : '';

        return [
            'timestamp' => $timestamp,
            'level'     => $level,
            'message'   => $message,
            'context'   => $context,
            'extra'     => '',
        ];
    }
}
