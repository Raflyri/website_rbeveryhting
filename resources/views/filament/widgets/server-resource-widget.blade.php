<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-cpu-chip class="h-5 w-5 text-indigo-500" />
                <span>Server Resources</span>
            </div>
        </x-slot>
        <x-slot name="headerEnd">
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                Auto-refreshes every 15s
                &nbsp;·&nbsp;
                PHP {{ $php }}
                &nbsp;·&nbsp;
                {{ $os }}
                @if ($uptime)
                &nbsp;·&nbsp; Up {{ $uptime }}
                @endif
            </span>
        </x-slot>

        @php
        /**
        * Returns colour classes based on a percentage value.
        * green < 60%, yellow < 85%, red>= 85%
            */
            $barColor = function (?float $pct): string {
            if ($pct === null) return 'bg-gray-300 dark:bg-gray-600';
            if ($pct >= 85) return 'bg-red-500';
            if ($pct >= 60) return 'bg-yellow-400';
            return 'bg-green-500';
            };

            $textColor = function (?float $pct): string {
            if ($pct === null) return 'text-gray-400';
            if ($pct >= 85) return 'text-red-500 dark:text-red-400';
            if ($pct >= 60) return 'text-yellow-500 dark:text-yellow-400';
            return 'text-green-600 dark:text-green-400';
            };
            @endphp

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">

                {{-- ── CPU ── --}}
                <div class="rounded-xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-gray-800/60 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-1.5">
                            <x-heroicon-o-cpu-chip class="h-4 w-4 text-indigo-400" />
                            CPU Load
                        </p>
                        @if ($cpu['percent'] !== null)
                        <span class="text-lg font-bold {{ $textColor($cpu['percent']) }}">{{ $cpu['percent'] }}%</span>
                        @else
                        <span class="text-sm text-gray-400">N/A</span>
                        @endif
                    </div>

                    @if ($cpu['percent'] !== null)
                    <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden mb-3">
                        <div class="h-2.5 rounded-full transition-all duration-700 {{ $barColor($cpu['percent']) }}"
                            style="width: {{ $cpu['percent'] }}%"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-center">
                        @foreach (['1m' => $cpu['load1'], '5m' => $cpu['load5'], '15m' => $cpu['load15']] as $label => $val)
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $label }}</p>
                            <p class="text-sm font-mono font-semibold text-gray-700 dark:text-gray-200">{{ $val }}</p>
                        </div>
                        @endforeach
                    </div>
                    @if ($cpu['cores'])
                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">{{ $cpu['cores'] }} logical core(s)</p>
                    @endif
                    @else
                    <p class="text-xs text-gray-400 mt-1">CPU info not available on this platform.</p>
                    @endif
                </div>

                {{-- ── RAM ── --}}
                <div class="rounded-xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-gray-800/60 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-1.5">
                            <x-heroicon-o-circle-stack class="h-4 w-4 text-purple-400" />
                            Memory
                        </p>
                        @if ($ram['percent'] !== null)
                        <span class="text-lg font-bold {{ $textColor($ram['percent']) }}">{{ $ram['percent'] }}%</span>
                        @else
                        <span class="text-sm text-gray-400">N/A</span>
                        @endif
                    </div>

                    <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden mb-3">
                        <div class="h-2.5 rounded-full transition-all duration-700 {{ $barColor($ram['percent']) }}"
                            style="width: {{ $ram['percent'] ?? 0 }}%"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-1 text-center">
                        @foreach (['Total' => $ram['total'], 'Used' => $ram['used'], 'Free' => $ram['free']] as $label => $val)
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $label }}</p>
                            <p class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-200">{{ $val }}</p>
                        </div>
                        @endforeach
                    </div>

                    @if (!empty($ram['note']))
                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500 italic">{{ $ram['note'] }}</p>
                    @endif
                </div>

                {{-- ── Disk ── --}}
                <div class="rounded-xl border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-gray-800/60 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-1.5">
                            <x-heroicon-o-server-stack class="h-4 w-4 text-teal-400" />
                            Disk
                        </p>
                        <span class="text-lg font-bold {{ $textColor($disk['percent']) }}">{{ $disk['percent'] }}%</span>
                    </div>

                    <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden mb-3">
                        <div class="h-2.5 rounded-full transition-all duration-700 {{ $barColor($disk['percent']) }}"
                            style="width: {{ $disk['percent'] }}%"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-1 text-center">
                        @foreach (['Total' => $disk['total'], 'Used' => $disk['used'], 'Free' => $disk['free']] as $label => $val)
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $label }}</p>
                            <p class="text-xs font-mono font-semibold text-gray-700 dark:text-gray-200">{{ $val }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
    </x-filament::section>
</x-filament-widgets::widget>