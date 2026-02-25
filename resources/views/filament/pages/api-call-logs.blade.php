<x-filament-panels::page>

    {{-- Toolbar / Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">

        {{-- Lines selector --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Last</label>
            <select
                wire:model.live="lines"
                class="fi-input rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm px-3 py-1.5 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500">
                <option value="100">100 lines</option>
                <option value="200">200 lines</option>
                <option value="500">500 lines</option>
                <option value="1000">1000 lines</option>
            </select>
        </div>

        {{-- Level filter --}}
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Level</label>
            <select
                wire:model.live="level"
                class="fi-input rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm px-3 py-1.5 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500">
                <option value="all">All</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="error">Error</option>
                <option value="debug">Debug</option>
            </select>
        </div>

        {{-- Search --}}
        <div class="flex-1 min-w-[200px]">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchTerm"
                placeholder="Search log messages…"
                class="w-full fi-input rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm px-3 py-1.5 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
        </div>

        {{-- Entry count badge --}}
        <div class="ml-auto">
            <span class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-900 px-3 py-1 text-xs font-semibold text-primary-700 dark:text-primary-300">
                {{ count($entries) }} {{ Str::plural('entry', count($entries)) }}
            </span>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
        @if (count($entries) === 0)
        <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
            <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mb-3 opacity-40" />
            <p class="text-sm font-medium">No log entries found.</p>
            <p class="text-xs mt-1">Try adjusting your filters or check that the application has generated some API logs.</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap w-40">Timestamp</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 w-24">Level</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Message</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 w-64">Context</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($entries as $entry)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors duration-100">
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap font-mono">
                        {{ $entry['timestamp'] }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                        $badgeClass = match(strtolower($entry['level'])) {
                        'error' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                        'warning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400',
                        'info' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                        'debug' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                        };
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                            {{ strtoupper($entry['level']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200 break-all">
                        {{ $entry['message'] }}
                        @if (!empty($entry['extra']))
                        <details class="mt-1">
                            <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300">Stack trace</summary>
                            <pre class="mt-1 text-xs text-gray-500 dark:text-gray-400 overflow-x-auto whitespace-pre-wrap bg-gray-50 dark:bg-gray-800 rounded p-2">{{ $entry['extra'] }}</pre>
                        </details>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs font-mono text-gray-500 dark:text-gray-400 break-all">
                        {{ $entry['context'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</x-filament-panels::page>