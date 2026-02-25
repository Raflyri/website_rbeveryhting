<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $endpoint->name }} - Base64 Tools - RBeverything</title>

    <meta name="description" content="{{ $endpoint->description ?? 'Base64 developer tool on RBeverything.' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800|jetbrains-mono:400" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/tools/base64.css') }}">
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
</head>

<body class="antialiased font-sans selection:bg-blue-500 selection:text-white bg-slate-950 text-slate-100">
    <x-header />

    <main class="min-h-screen pt-24 pb-20 relative overflow-hidden">
        <div class="absolute top-0 inset-x-0 h-[400px] bg-gradient-to-b from-blue-900/20 to-transparent pointer-events-none"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-40 -left-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <a href="{{ route('tools.base64') }}" class="inline-flex items-center text-sm text-slate-400 hover:text-slate-200 mb-3">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-1"></i>
                        Back to all Base64 tools
                    </a>
                    <div class="inline-flex items-center gap-3 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold tracking-wide uppercase mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        {{ ucfirst($endpoint->category) }} Tool
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                        {{ $endpoint->name }}
                    </h1>
                    @if(!empty($endpoint->description))
                    <p class="text-slate-400 max-w-2xl">
                        {{ $endpoint->description }}
                    </p>
                    @endif
                </div>
                <div class="hidden md:flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-400">
                    <i data-feather="{{ $endpoint->icon ?: 'code' }}" class="w-7 h-7"></i>
                </div>
            </div>

            @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                <div class="font-semibold mb-1">Validation error</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $errorMessage)
                    <li>{{ $errorMessage }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (!empty($error))
            <div class="mb-6 rounded-xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                <div class="font-semibold mb-1">API error</div>
                <p>{{ $error }}</p>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <x-footer />

    <script>
        feather.replace();
    </script>
</body>

</html>