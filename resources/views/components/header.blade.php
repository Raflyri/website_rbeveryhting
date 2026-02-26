@php
$siteSetting = \App\Models\SiteSetting::first();
$headerMenu = $siteSetting ? $siteSetting->header_menu : [
['label' => 'Tools', 'url' => route('tools.base64')],
['label' => __('text.menu_about'), 'url' => '#'],
['label' => __('text.menu_contact'), 'url' => '#'],
];
@endphp

<header class="fixed top-0 w-full z-50 transition-all duration-300 bg-black/50 backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="text-2xl font-bold text-white tracking-widest leading-none">
            <a href="/" class="hover:text-blue-400 transition-colors">RBeverything</a>
        </div>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex gap-8 text-white/80 text-sm font-medium items-center">
            @if($headerMenu && is_array($headerMenu))
            @foreach($headerMenu as $menuItem)
            <a href="{{ $menuItem['url'] ?? '#' }}" class="hover:text-white transition">{{ $menuItem['label'] ?? '' }}</a>
            @endforeach
            @endif
        </nav>

        <!-- Mobile Menu Toggle Button -->
        <button id="mobile-menu-toggle" class="md:hidden text-white/80 hover:text-white p-2 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu-dropdown" class="hidden md:hidden bg-slate-900 border-b border-white/10 px-6 py-4 absolute w-full top-20 left-0 shadow-2xl">
        <nav class="flex flex-col gap-5 text-white/80 text-sm font-medium">
            @if($headerMenu && is_array($headerMenu))
            @foreach($headerMenu as $menuItem)
            <a href="{{ $menuItem['url'] ?? '#' }}" class="block hover:text-white transition">{{ $menuItem['label'] ?? '' }}</a>
            @endforeach
            @endif

            <div class="border-t border-white/10 pt-4 mt-2">
                <p class="text-xs text-slate-500 mb-3 tracking-widest uppercase">Language / Bahasa</p>
                <div class="flex flex-wrap gap-3 text-xs">
                    <a href="{{ route('switch.language', 'en_US') }}" class="{{ app()->getLocale() == 'en_US' ? 'text-blue-400' : 'text-slate-400 hover:text-white' }}">🇺🇸 EN</a>
                    <a href="{{ route('switch.language', 'id') }}" class="{{ app()->getLocale() == 'id' ? 'text-blue-400' : 'text-slate-400 hover:text-white' }}">🇮🇩 ID</a>
                    <a href="{{ route('switch.language', 'ms') }}" class="{{ app()->getLocale() == 'ms' ? 'text-blue-400' : 'text-slate-400 hover:text-white' }}">🇲🇾 MS</a>
                    <a href="{{ route('switch.language', 'ja') }}" class="{{ app()->getLocale() == 'ja' ? 'text-blue-400' : 'text-slate-400 hover:text-white' }}">🇯🇵 JA</a>
                </div>
            </div>
        </nav>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('mobile-menu-toggle');
        const menu = document.getElementById('mobile-menu-dropdown');
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });
        }
    });
</script>