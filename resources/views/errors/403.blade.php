<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>403 Forbidden - RBeverything</title>

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

    <style>
        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .clip-text-gradient-red {
            background: linear-gradient(to right, #f87171, #ef4444);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="antialiased font-sans selection:bg-red-500 selection:text-white bg-slate-950 text-slate-100 flex flex-col min-h-screen">

    <x-header />

    <main class="flex-1 flex items-center justify-center relative overflow-hidden pt-24 pb-12 px-4 sm:px-6 lg:px-8">
        {{-- Background Glowing Orbs --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-red-600/10 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-72 h-72 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-2xl text-center">
            <div class="glass-card rounded-3xl p-8 sm:p-12 shadow-2xl overflow-hidden relative">
                {{-- Decorative inner orb --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-red-500/10 rounded-full blur-2xl"></div>

                <div class="flex justify-center mb-8 relative">
                    <div class="w-32 h-32 rounded-full bg-red-500/10 border border-red-500/20 flex flex-col items-center justify-center relative shadow-[0_0_30px_rgba(239,68,68,0.15)]">
                        <i data-feather="shield-off" class="w-12 h-12 text-red-400 opacity-80 absolute animate-pulse"></i>
                        <i data-feather="alert-octagon" class="w-16 h-16 text-red-500/30"></i>
                    </div>
                </div>

                <h1 class="text-7xl sm:text-9xl font-black tracking-tighter mb-4 select-none">
                    <span class="clip-text-gradient-red opacity-90">403</span>
                </h1>

                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4">Access Forbidden</h2>

                <p class="text-slate-400 text-sm sm:text-base max-w-md mx-auto mb-10 leading-relaxed">
                    You don't have permission to access this resource or page. If you believe this is a mistake, please contact the administrator.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button onclick="window.history.back()" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800/50 hover:bg-slate-800 border border-white/10 text-slate-200 font-medium transition-all group flex items-center justify-center gap-2">
                        <i data-feather="arrow-left" class="w-4 h-4 text-slate-400 group-hover:-translate-x-1 transition-transform"></i>
                        <span class="group-hover:text-white transition-colors">Go Back</span>
                    </button>
                    <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-medium transition-colors shadow-lg shadow-red-500/25 flex items-center justify-center gap-2">
                        <i data-feather="home" class="w-4 h-4"></i>
                        Return Home
                    </a>
                </div>
            </div>
        </div>
    </main>

    <x-footer />

    <script>
        feather.replace();
    </script>
</body>

</html>