<x-filament-panels::page>
    {{-- ═══════════════════════════════════════════════
         STATS BAR
    ════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">
        @php
        $statCards = [
        ['label' => 'Total Calls', 'value' => $stats['total'], 'icon' => 'heroicon-o-signal', 'color' => 'text-indigo-400'],
        ['label' => 'Success', 'value' => $stats['success'], 'icon' => 'heroicon-o-check-circle', 'color' => 'text-green-400'],
        ['label' => 'Warnings', 'value' => $stats['warning'], 'icon' => 'heroicon-o-exclamation-triangle', 'color' => 'text-yellow-400'],
        ['label' => 'Errors', 'value' => $stats['error'], 'icon' => 'heroicon-o-x-circle', 'color' => 'text-red-400'],
        ];
        @endphp
        @foreach ($statCards as $card)
        <div class="fi-section rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <x-dynamic-component :component="$card['icon']" class="h-7 w-7 {{ $card['color'] }}" />
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($card['value']) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════
         FILTERS
    ════════════════════════════════════════════════ --}}
    <div class="fi-section rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900 mb-4">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Level --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Level</label>
                <select wire:model.live="level"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="all">All Levels</option>
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="error">Error</option>
                </select>
            </div>

            {{-- Endpoint --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Endpoint</label>
                <select wire:model.live="endpoint"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Endpoints</option>
                    @foreach ($endpoints as $ep)
                    <option value="{{ $ep }}">{{ $ep }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Country --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Country</label>
                <select wire:model.live="country"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Countries</option>
                    @foreach ($countries as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            {{-- IP Filter --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">IP Address</label>
                <input wire:model.live.debounce.400ms="ipFilter" type="text" placeholder="e.g. 192.168"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Date From</label>
                <input wire:model.live="dateFrom" type="date"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Date To</label>
                <input wire:model.live="dateTo" type="date"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            {{-- Search --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search message, URL, IP, snippets…"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm dark:border-white/10 dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>
        </div>

        <div class="mt-3 flex justify-end">
            <button wire:click="resetFilters"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-600 shadow-sm hover:bg-gray-50 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                Reset Filters
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         TABLE
    ════════════════════════════════════════════════ --}}
    <div class="fi-section rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 overflow-hidden">
        @if ($logs->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
            <x-heroicon-o-inbox class="h-14 w-14 mb-3 opacity-40" />
            <p class="text-sm">No API call logs found.</p>
            <p class="text-xs mt-1 opacity-70">Make a call via the Base64 SPA tool to see logs appear here.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-white/10">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Time</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Endpoint</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IP</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Country</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Method</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Duration</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Level</th>
                        <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach ($logs as $log)
                    @php
                    $levelColors = [
                    'info' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                    'warning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                    'error' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    ];
                    $statusColor = match(true) {
                    ($log->http_status >= 500) => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    ($log->http_status >= 400) => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                    ($log->http_status >= 200) => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                    default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                    };
                    $methodColor = match($log->method) {
                    'POST' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
                    'GET' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                    default => 'bg-gray-100 text-gray-600',
                    };
                    $drawerKey = 'drawer-' . $log->id;
                    @endphp

                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-indigo-600 dark:text-indigo-400">{{ $log->api_endpoint ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $log->ip ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">
                            @if ($log->country)
                            @if ($log->country_code && strlen($log->country_code) === 2 && $log->country_code !== '--' && $log->country_code !== '??')
                            <img src="https://flagcdn.com/16x12/{{ strtolower($log->country_code) }}.png"
                                alt="{{ $log->country_code }}"
                                class="inline-block mr-1 rounded-sm"
                                onerror="this.style.display='none'" />
                            @endif
                            {{ $log->country }}
                            @else
                            <span class="text-gray-400 animate-pulse">Resolving…</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $methodColor }}">{{ $log->method }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($log->http_status)
                            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $statusColor }}">{{ $log->http_status }}</span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">
                            @if ($log->duration_ms !== null)
                            {{ $log->duration_ms }}ms
                            @else
                            —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $levelColors[$log->level] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($log->level) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button
                                onclick="document.getElementById('{{ $drawerKey }}').classList.toggle('hidden')"
                                class="rounded border border-gray-200 dark:border-white/10 px-2 py-1 text-xs text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition">
                                Expand
                            </button>
                        </td>
                    </tr>

                    {{-- Expandable detail row --}}
                    <tr id="{{ $drawerKey }}" class="hidden bg-gray-50 dark:bg-gray-800/50">
                        <td colspan="9" class="px-6 py-4">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                {{-- URL --}}
                                <div class="col-span-full">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">URL</p>
                                    <p class="font-mono text-xs text-gray-700 dark:text-gray-200 break-all bg-white dark:bg-gray-900 rounded p-2 border border-gray-200 dark:border-white/10">
                                        {{ $log->url ?? '—' }}
                                    </p>
                                </div>

                                {{-- Message --}}
                                <div class="col-span-full">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Message</p>
                                    <p class="text-xs text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 rounded p-2 border border-gray-200 dark:border-white/10">
                                        {{ $log->message ?? '—' }}
                                    </p>
                                </div>

                                {{-- Request Snippet --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Request Snippet</p>
                                    <pre class="font-mono text-xs text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 rounded p-2 border border-gray-200 dark:border-white/10 overflow-x-auto whitespace-pre-wrap break-all max-h-40">{{ $log->request_snippet ?? '(empty)' }}</pre>
                                </div>

                                {{-- Response Snippet --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Response Snippet</p>
                                    <pre class="font-mono text-xs text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 rounded p-2 border border-gray-200 dark:border-white/10 overflow-x-auto whitespace-pre-wrap break-all max-h-40">{{ $log->response_snippet ?? '(empty)' }}</pre>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-100 dark:border-white/10">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</x-filament-panels::page>