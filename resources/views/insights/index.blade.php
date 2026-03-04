<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Insights — {{ config('app.name', 'RBeverything') }}</title>
    <meta name="description" content="News, Articles & Blog from RBeverything — insights on technology, development, and business.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ── Glassmorphism cards ── */
        .glass-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: border-color .3s, box-shadow .3s;
        }

        .glass-card:hover {
            border-color: rgba(99, 102, 241, 0.45);
            box-shadow: 0 0 32px rgba(99, 102, 241, 0.18);
        }

        /* ── Bento grid ── */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-auto-rows: 260px;
            gap: 1rem;
        }

        .bento-col-4 {
            grid-column: span 4;
        }

        .bento-col-6 {
            grid-column: span 6;
        }

        .bento-col-8 {
            grid-column: span 8;
        }

        .bento-col-12 {
            grid-column: span 12;
        }

        .bento-row-2 {
            grid-row: span 2;
        }

        @media (max-width: 1024px) {

            .bento-col-4,
            .bento-col-6,
            .bento-col-8 {
                grid-column: span 12;
            }

            .bento-row-2 {
                grid-row: span 1;
            }
        }

        /* ── Card image zoom ── */
        .card-img-wrap {
            overflow: hidden;
        }

        .card-img-wrap img {
            transition: transform .5s ease;
        }

        .glass-card:hover .card-img-wrap img {
            transform: scale(1.06);
        }

        /* ── Vignette on featured hero ── */
        .hero-vignette {
            background: radial-gradient(ellipse at center, transparent 30%, rgba(0, 0, 0, .8) 100%),
                linear-gradient(to top, rgba(0, 0, 0, .95) 0%, transparent 60%);
        }

        /* ── Filter pill active state ── */
        .filter-pill.active {
            background: rgba(99, 102, 241, .25);
            border-color: rgba(99, 102, 241, .6);
            color: #a5b4fc;
        }
    </style>
</head>

<body class="bg-[#080810] text-white min-h-screen antialiased font-sans">

    <x-header />

    <main class="pt-20">

        {{-- ══════════════════════════════════════════════
             FEATURED HERO POST
        ══════════════════════════════════════════════ --}}
        @if ($featured)
        <section class="relative h-[85vh] min-h-[520px] w-full overflow-hidden">
            {{-- Background --}}
            @if ($featured->thumbnailUrl())
            <img src="{{ $featured->thumbnailUrl() }}" alt="{{ $featured->title }}"
                class="absolute inset-0 w-full h-full object-cover scale-105 animate-[slowZoom_20s_ease_infinite]" />
            @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 to-purple-950"></div>
            @endif
            <div class="hero-vignette absolute inset-0"></div>

            {{-- Content --}}
            <div class="relative z-10 h-full flex flex-col justify-end px-6 pb-16 max-w-7xl mx-auto">
                {{-- HOT badge --}}
                <div class="mb-4 inline-flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest
                                 bg-orange-500/20 border border-orange-500/40 text-orange-300 backdrop-blur-sm">
                        🔥 Hot Article
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border
                                 {{ $featured->typeColor() }}">
                        {{ ucfirst($featured->type) }}
                    </span>
                    <span class="text-white/40 text-xs">{{ $featured->readingTimeLabel() }}</span>
                </div>

                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black leading-tight max-w-4xl
                            tracking-tight text-white drop-shadow-2xl">
                    {{ $featured->title }}
                </h1>

                @if ($featured->resolvedExcerpt())
                <p class="mt-4 text-lg text-white/60 max-w-2xl leading-relaxed">
                    {{ $featured->resolvedExcerpt(180) }}
                </p>
                @endif

                <div class="mt-8 flex items-center gap-6">
                    <a href="{{ route('insights.show', $featured->slug) }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold text-sm
                              bg-indigo-600 hover:bg-indigo-500 transition-all duration-300
                              shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/50 hover:scale-105">
                        Read Article
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <div class="text-white/40 text-sm">
                        @if ($featured->author_name)
                        <span>by <span class="text-white/70">{{ $featured->author_name }}</span></span>
                        @endif
                        @if ($featured->published_at)
                        <span class="ml-2">· {{ $featured->published_at->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- ══════════════════════════════════════════════
             FILTER PILLS
        ══════════════════════════════════════════════ --}}
        <section class="max-w-7xl mx-auto px-6 mt-12 mb-8">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-white/30 text-xs uppercase tracking-widest mr-2">Filter</span>
                @foreach (['all' => 'All', 'news' => 'News', 'article' => 'Articles', 'blog' => 'Blog'] as $key => $label)
                <button data-filter="{{ $key }}"
                    class="filter-pill px-5 py-2 rounded-full text-sm font-medium border border-white/10
                               bg-white/5 text-white/60 hover:text-white hover:border-white/30
                               transition-all duration-200 {{ $key === 'all' ? 'active' : '' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </section>

        {{-- ══════════════════════════════════════════════
             BENTO GRID
        ══════════════════════════════════════════════ --}}
        <section class="max-w-7xl mx-auto px-6 pb-24">

            @if ($allPosts->isEmpty())
            <div class="text-center py-24 text-white/30">
                <p class="text-5xl mb-4">✍️</p>
                <p class="text-lg">No posts published yet. Check back soon!</p>
            </div>
            @else
            <div class="bento-grid" id="bento-grid">
                @foreach ($allPosts as $i => $post)
                @php
                /* Bento pattern: 0→wide+tall, 1→narrow, 2→narrow, 3→mid, 4→mid, repeating */
                $pattern = $i % 5;
                $colClass = match($pattern) {
                0 => 'bento-col-8 bento-row-2',
                1 => 'bento-col-4',
                2 => 'bento-col-4',
                3 => 'bento-col-6',
                4 => 'bento-col-6',
                default => 'bento-col-4',
                };
                @endphp

                <a href="{{ route('insights.show', $post->slug) }}"
                    data-type="{{ $post->type }}"
                    class="post-card glass-card rounded-2xl overflow-hidden flex flex-col group relative {{ $colClass }}">

                    {{-- Thumbnail --}}
                    <div class="card-img-wrap absolute inset-0">
                        @if ($post->thumbnailUrl())
                        <img src="{{ $post->thumbnailUrl() }}" alt="{{ $post->title }}"
                            class="w-full h-full object-cover opacity-60" />
                        @else
                        <div class="w-full h-full bg-gradient-to-br
                                        {{ $post->type === 'news' ? 'from-emerald-950 to-teal-900' :
                                           ($post->type === 'blog' ? 'from-pink-950 to-rose-900' : 'from-indigo-950 to-violet-900') }}">
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    </div>

                    {{-- Content --}}
                    <div class="relative z-10 flex flex-col justify-end h-full p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $post->typeColor() }}">
                                {{ ucfirst($post->type) }}
                            </span>
                            <span class="text-white/40 text-xs">{{ $post->readingTimeLabel() }}</span>
                        </div>
                        <h2 class="font-bold text-white leading-snug
                                           {{ in_array($pattern, [0]) ? 'text-2xl md:text-3xl' : 'text-base md:text-lg' }}
                                           group-hover:text-indigo-200 transition-colors line-clamp-3">
                            {{ $post->title }}
                        </h2>
                        @if (in_array($pattern, [0, 3, 4]))
                        <p class="mt-2 text-white/50 text-sm line-clamp-2">
                            {{ $post->resolvedExcerpt(120) }}
                        </p>
                        @endif
                        <div class="mt-3 flex items-center gap-2 text-xs text-white/30">
                            @if ($post->author_name)
                            <span>{{ $post->author_name }}</span>
                            <span>·</span>
                            @endif
                            @if ($post->published_at)
                            <span>{{ $post->published_at->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </section>

    </main>

    <x-footer />

    <script>
        // ── Filter pills ──
        const pills = document.querySelectorAll('.filter-pill');
        const cards = document.querySelectorAll('.post-card');
        const grid = document.getElementById('bento-grid');

        pills.forEach(pill => {
            pill.addEventListener('click', () => {
                const filter = pill.dataset.filter;

                // Update active state
                pills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');

                // Show/hide cards with smooth fade
                cards.forEach(card => {
                    const type = card.dataset.type;
                    const show = filter === 'all' || type === filter;
                    card.style.transition = 'opacity .25s, transform .25s';
                    if (show) {
                        card.style.opacity = '1';
                        card.style.display = '';
                        card.style.transform = 'scale(1)';
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.96)';
                        setTimeout(() => {
                            if (pill.classList.contains('active') && filter !== 'all' && card.dataset.type !== filter) card.style.display = 'none';
                        }, 250);
                    }
                });
            });
        });

        // ── Animate cards in on load ──
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(24px)';
            card.style.transition = 'opacity .5s ease, transform .5s ease';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 80 + i * 60);
        });
    </script>

    <style>
        @keyframes slowZoom {

            0%,
            100% {
                transform: scale(1.05);
            }

            50% {
                transform: scale(1.12);
            }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</body>

</html>