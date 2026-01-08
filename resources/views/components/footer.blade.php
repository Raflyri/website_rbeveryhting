<footer class="bg-black text-white/40 py-10 text-center text-sm border-t border-white/10 z-50 relative">

    <div class="flex justify-center gap-6 mb-6 font-medium text-xs tracking-widest uppercase">
        <a href="{{ route('switch.language', 'en_US') }}" class="{{ app()->getLocale() == 'en_US' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇺🇸 English (US)
        </a>
        <a href="{{ route('switch.language', 'en_GB') }}" class="{{ app()->getLocale() == 'en_GB' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇬🇧 English (UK)
        </a>
        <a href="{{ route('switch.language', 'id') }}" class="{{ app()->getLocale() == 'id' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇮🇩 Indonesia
        </a>
        <a href="{{ route('switch.language', 'ms') }}" class="{{ app()->getLocale() == 'ms' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇲🇾 Melayu
        </a>
        <a href="{{ route('switch.language', 'ja') }}" class="{{ app()->getLocale() == 'ja' ? 'text-white' : 'text-gray-600 hover:text-white' }} transition">
            🇯🇵 日本語
        </a>
    </div>

    <p>
        &copy; {{ date('Y') }} RBeverything.com. {{ __('text.footer_copy') }}
    </p>
</footer>