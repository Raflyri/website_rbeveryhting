@php
$siteSetting = \App\Models\SiteSetting::first();
$footerMenu = $siteSetting ? $siteSetting->footer_menu : [];
@endphp

<footer class="bg-black text-white/40 py-10 text-center text-sm border-t border-white/10 z-50 relative">

    @if($footerMenu && is_array($footerMenu) && count($footerMenu) > 0)
    <!-- Dynamic Footer Link Menu -->
    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 px-4 mb-8 font-medium text-sm text-white/70">
        @foreach($footerMenu as $menuItem)
        <a href="{{ $menuItem['url'] ?? '#' }}" class="hover:text-white transition">
            {{ $menuItem['label'] ?? '' }}
        </a>
        @endforeach
    </div>
    @endif

    <!-- Language Selector -->
    <div class="flex flex-wrap justify-center gap-x-6 gap-y-4 px-4 mb-6 font-medium text-xs tracking-widest uppercase border-t border-white/5 pt-6 mx-auto max-w-2xl">
        <a href="{{ route('switch.language', 'en_US') }}" class="{{ app()->getLocale() == 'en_US' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇺🇸 EN (US)
        </a>
        <a href="{{ route('switch.language', 'en_GB') }}" class="{{ app()->getLocale() == 'en_GB' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇬🇧 EN (UK)
        </a>
        <a href="{{ route('switch.language', 'id') }}" class="{{ app()->getLocale() == 'id' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇮🇩 ID
        </a>
        <a href="{{ route('switch.language', 'ms') }}" class="{{ app()->getLocale() == 'ms' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇲🇾 MS
        </a>
        <a href="{{ route('switch.language', 'ja') }}" class="{{ app()->getLocale() == 'ja' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇯🇵 JA
        </a>
    </div>

    <!-- Copyright Info -->
    <p>
        &copy; {{ date('Y') }} RBeverything.com. {{ __('text.footer_copy') }}
    </p>
</footer>