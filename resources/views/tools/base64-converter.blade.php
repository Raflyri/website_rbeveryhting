<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Base64 Tools - RBeverything</title>
    <meta name="description" content="Free online Base64 tools: Encode, Decode, URL Safe, Image, File. Fast, secure, developer-friendly.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800|jetbrains-mono:400" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#10b981',
                        dark: '#0f172a',
                        darker: '#020617',
                    }
                }
            }
        }
    </script>

    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="{{ asset('css/tools/base64.css') }}">
</head>

<body class="antialiased font-sans selection:bg-blue-500 selection:text-white bg-slate-950 text-slate-100">

    <x-header />

    {{-- ─── MOBILE: Sidebar overlay backdrop removed ─────────────────────────── --}}

    <main class="min-h-[calc(100vh-160px)] pt-24 relative overflow-x-hidden flex flex-col">

        {{-- Background decorations --}}
        <div class="absolute top-0 inset-x-0 h-[500px] bg-gradient-to-b from-blue-900/20 to-transparent pointer-events-none"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-60 -left-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-screen-xl w-full mx-auto px-4 sm:px-6 lg:px-8 pb-12 relative z-10 flex-1">

            {{-- ─── Page title row ─────────────────────────────────────────── --}}
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-bold tracking-widest uppercase mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        {{ __('text.dev_utilities') }}
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">
                        {{ __('text.b64_title_base64') }} <span class="clip-text-gradient">{{ __('text.b64_title_tools') }}</span>
                    </h1>
                </div>

                {{-- Mobile hamburger button removed in favor of horizontal tabs --}}
            </div>

            {{-- ─── MOBILE: Horizontal scrollable tabs ───────────────────────── --}}
            <div class="lg:hidden flex overflow-x-auto whitespace-nowrap space-x-2 pb-4 mb-4 scrollbar-hide">
                @foreach($endpoints->whereNotNull('api_url') as $endpoint)
                <button
                    data-spa-item="{{ $endpoint->slug }}"
                    data-spa-category="{{ $endpoint->category }}"
                    class="spa-mobile-tab bg-slate-800/60 border border-white/5 text-slate-300 px-4 py-2 rounded-xl text-sm font-medium transition-colors">
                    {{ $endpoint->name }}
                </button>
                @endforeach
            </div>

            {{-- ─── SPA App Layout ──────────────────────────────────────────── --}}
            <div class="flex gap-6 items-start relative">

                {{-- ─── SIDEBAR (desktop only now) ───────────────────────────── --}}
                <aside id="spa-sidebar-drawer"
                    class="sticky top-24 left-0 z-auto h-auto w-64 xl:w-72 hidden lg:flex flex-col bg-transparent">

                    <div class="flex-1 overflow-y-auto px-3 pb-6 space-y-5 scrollbar-hide">

                        {{-- Category filter pills --}}
                        <div class="flex flex-wrap gap-1.5 pb-1">
                            <button data-spa-filter="all" class="spa-cat-active text-xs px-3 py-1 rounded-full border font-medium transition-all duration-150">
                                {{ __('text.category_all') }}
                            </button>
                            @foreach($endpoints->pluck('category')->unique()->filter()->sort()->values() as $cat)
                            <button data-spa-filter="{{ $cat }}" class="spa-cat-inactive text-xs px-3 py-1 rounded-full border font-medium transition-all duration-150">
                                {{ ucfirst($cat) }}
                            </button>
                            @endforeach
                        </div>

                        {{-- Tool list --}}
                        @if($endpoints->isEmpty())
                        <p class="text-sm text-slate-500 px-2">{{ __('text.no_tools') }}</p>
                        @else
                        @foreach($endpoints->groupBy('category') as $category => $group)
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-600 px-2 mb-1">{{ ucfirst($category) }}</p>
                            <ul class="space-y-0.5">
                                @foreach($group as $endpoint)
                                <li>
                                    <a href="{{ route('tools.base64.show', $endpoint->slug) }}"
                                        data-spa-item="{{ $endpoint->slug }}"
                                        data-spa-category="{{ $endpoint->category }}"
                                        class="spa-sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 cursor-pointer group
                                              {{ !empty($endpoint->api_url) ? '' : 'opacity-40 pointer-events-none' }}">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 text-slate-400 shrink-0 group-hover:bg-blue-500/20 group-hover:text-blue-400 transition-colors">
                                            <i data-feather="{{ $endpoint->icon ?: 'box' }}" class="w-3.5 h-3.5"></i>
                                        </span>
                                        <span class="truncate">{{ $endpoint->name }}</span>
                                        @if(empty($endpoint->api_url))
                                        <span class="ml-auto text-[9px] uppercase tracking-wide bg-slate-700 text-slate-400 px-1.5 py-0.5 rounded">{{ __('text.soon') }}</span>
                                        @endif
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </aside>

                {{-- ─── CONTENT PANEL ───────────────────────────────────────── --}}
                <section class="flex-1 min-w-0">

                    {{-- Welcome splash (shown until a tool is selected) --}}
                    <div id="spa-welcome" class="text-center py-10 md:py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-blue-500/10 border border-blue-500/20 text-blue-400 mb-6">
                            <i data-feather="zap" class="w-10 h-10"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-2">{{ __('text.select_tool') }}</h2>
                        <p class="text-slate-400 max-w-sm mx-auto text-sm leading-relaxed">
                            {{ __('text.select_tool_desc') }}
                        </p>
                        <div class="mt-8 inline-flex items-center gap-2 text-xs text-slate-600">
                            <i data-feather="arrow-left" class="w-3 h-3"></i>
                            {{ $endpoints->where('api_url', '!=', null)->count() }} {{ __('text.tools_available') }}
                        </div>
                    </div>

                    {{-- Dynamic tool panel — populated by SPA JS --}}
                    <div id="spa-main-panel" class="min-h-0"></div>

                </section>
            </div>
        </div>
    </main>

    <x-footer />

    <script>
        feather.replace();

        // Pass translations to SPA JS
        window.spaTranslations = {
            validationError: "{{ __('text.validation_error') }}",
            apiError: "{{ __('text.api_error') }}",
            failedToLoad: "{{ __('text.failed_to_load') }}",
            error: "{{ __('text.error') }}",
            working: "{{ __('text.working') }}",
            submit: "{{ __('text.submit') }}",
            downloading: "{{ __('text.downloading') }}",
            downloadReady: "{{ __('text.download_ready') }}",
            downloadSaved: "{{ __('text.download_saved') }}"
        };
    </script>
    <script src="{{ asset('js/tools/base64-spa.js') }}"></script>
</body>

</html>