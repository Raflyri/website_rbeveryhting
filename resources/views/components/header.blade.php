<header class="fixed top-0 w-full z-50 transition-all duration-300 bg-black/50 backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <div class="text-2xl font-bold text-white tracking-widest">
            RBeverything
        </div>

        <nav class="hidden md:flex gap-8 text-white/80 text-sm font-medium">
            <a href="{{ route('tools.base64') }}" class="hover:text-white transition">
                Tools
            </a>
            <a href="#" class="hover:text-white transition">
                {{ __('text.menu_about') }}
            </a>
            <a href="#" class="hover:text-white transition">
                {{ __('text.menu_contact') }}
            </a>
        </nav>
    </div>
</header>