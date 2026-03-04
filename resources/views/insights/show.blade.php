<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }} — {{ config('app.name', 'RBeverything') }}</title>
    <meta name="description" content="{{ $post->resolvedExcerpt(160) }}">
    @if ($post->thumbnailUrl())
    <meta property="og:image" content="{{ $post->thumbnailUrl() }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Prism.js syntax highlighting (CDN) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">

    <style>
        /* ── Layout ── */
        body {
            background: #080810;
            color: rgba(255, 255, 255, 0.88);
            font-family: 'Outfit', 'Figtree', system-ui, sans-serif;
        }

        /* ── Neon progress bar (top of page) ── */
        #neon-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #a78bfa);
            box-shadow: 0 0 10px rgba(139, 92, 246, .8), 0 0 24px rgba(99, 102, 241, .5);
            z-index: 9999;
            transition: width .1s linear;
            border-radius: 0 2px 2px 0;
        }

        /* ── Standard header (transparent, disappears on scroll) ── */
        #site-header {
            transition: opacity .4s, transform .4s;
        }

        #site-header.hidden-header {
            opacity: 0;
            transform: translateY(-100%);
            pointer-events: none;
        }

        /* ── Floating pill ── */
        #reading-pill {
            position: fixed;
            top: 16px;
            left: 50%;
            transform: translateX(-50%) translateY(-80px);
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 8px 18px;
            z-index: 9998;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform .4s cubic-bezier(.34, 1.56, .64, 1), opacity .35s;
            opacity: 0;
            max-width: calc(100vw - 40px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .4);
            white-space: nowrap;
        }

        #reading-pill.visible {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        /* ── Cover image vignette ── */
        .cover-vignette {
            background: linear-gradient(to bottom, rgba(8, 8, 16, .3) 0%, transparent 30%, transparent 50%, rgba(8, 8, 16, 1) 100%);
        }

        /* ── Prose typography ── */
        .prose-dark {
            color: rgba(255, 255, 255, .82);
            line-height: 1.85;
            font-size: 1.125rem;
            max-width: 740px;
            margin: 0 auto;
        }

        .prose-dark h2 {
            font-size: 1.7rem;
            font-weight: 800;
            color: #fff;
            margin: 2.5rem 0 1rem;
            letter-spacing: -.02em;
        }

        .prose-dark h3 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            margin: 2rem 0 .75rem;
        }

        .prose-dark h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            margin: 1.5rem 0 .5rem;
        }

        .prose-dark p {
            margin: 0 0 1.4rem;
        }

        .prose-dark a {
            color: #818cf8;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .prose-dark strong {
            color: #fff;
        }

        .prose-dark ul,
        .prose-dark ol {
            margin: 0 0 1.4rem 1.25rem;
        }

        .prose-dark li {
            margin-bottom: .4rem;
        }

        /* ── Block: Code ── */
        .block-code-wrap {
            background: #0f0f1a;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 12px;
            margin: 2rem 0;
            overflow: hidden;
        }

        .block-code-header {
            background: rgba(255, 255, 255, .04);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
        }

        .block-code-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .block-code-wrap pre {
            margin: 0 !important;
            padding: 20px !important;
            background: transparent !important;
        }

        .block-code-wrap code {
            font-size: .88rem !important;
        }

        /* ── Block: Quote ── */
        .block-quote {
            border-left: 3px solid #6366f1;
            background: rgba(99, 102, 241, .07);
            border-radius: 0 12px 12px 0;
            padding: 1.25rem 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            font-size: 1.15rem;
            color: rgba(255, 255, 255, .85);
        }

        .block-quote-attribution {
            font-style: normal;
            color: rgba(255, 255, 255, .4);
            font-size: .85rem;
            margin-top: .5rem;
        }

        /* ── Block: Image ── */
        .block-image {
            margin: 2rem 0;
            text-align: center;
        }

        .block-image img {
            border-radius: 14px;
            max-width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
        }

        .block-image figcaption {
            margin-top: .5rem;
            font-size: .8rem;
            color: rgba(255, 255, 255, .35);
            font-style: italic;
        }

        /* ── Author card ── */
        .author-card {
            background: rgba(255, 255, 255, .04);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 20px;
            padding: 28px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            max-width: 740px;
            margin: 0 auto;
        }

        /* ── Next article card ── */
        .next-article-card {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 20px;
            overflow: hidden;
            transition: border-color .3s, box-shadow .3s;
        }

        .next-article-card:hover {
            border-color: rgba(99, 102, 241, .45);
            box-shadow: 0 0 40px rgba(99, 102, 241, .15);
        }

        .next-article-card img {
            transition: transform .5s;
        }

        .next-article-card:hover img {
            transform: scale(1.04);
        }

        /* ── Grand CTA ── */
        .grand-cta {
            background: linear-gradient(135deg, rgba(30, 10, 60, .95), rgba(15, 10, 30, .98));
            border: 1px solid rgba(255, 255, 255, .06);
            border-radius: 24px;
        }

        .cta-btn:hover {
            box-shadow: 0 0 30px rgba(99, 102, 241, .6);
        }
    </style>
</head>

<body class="antialiased">

    {{-- ── NEON PROGRESS BAR ── --}}
    <div id="neon-progress"></div>

    {{-- ── STANDARD HEADER (hides on scroll) ── --}}
    <div id="site-header">
        <x-header />
    </div>

    {{-- ── FLOATING READING PILL ── --}}
    <div id="reading-pill">
        <a href="{{ route('insights.index') }}"
            class="flex items-center gap-1.5 text-white/60 hover:text-white transition text-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="hidden sm:inline">Back</span>
        </a>
        <span class="text-white/20">|</span>
        <span class="text-white/70 text-xs font-medium truncate max-w-[200px] sm:max-w-xs">
            {{ $post->title }}
        </span>
        <span class="text-white/20">|</span>
        <button onclick="sharePost()" class="flex items-center gap-1.5 text-white/60 hover:text-white transition text-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
            <span class="hidden sm:inline">Share</span>
        </button>
    </div>

    <main>

        {{-- ══════════════════════════════════════════════
             COVER IMAGE / HERO
        ══════════════════════════════════════════════ --}}
        <section class="relative h-[70vh] min-h-[400px] w-full overflow-hidden">
            @if ($post->thumbnailUrl())
            <img src="{{ $post->thumbnailUrl() }}" alt="{{ $post->title }}"
                class="absolute inset-0 w-full h-full object-cover" />
            @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 to-purple-950"></div>
            @endif
            <div class="cover-vignette absolute inset-0"></div>

            <div class="relative z-10 h-full flex flex-col justify-end px-6 pb-12 max-w-4xl mx-auto">
                <div class="flex items-center gap-2 mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $post->typeColor() }}">
                        {{ ucfirst($post->type) }}
                    </span>
                    <span class="text-white/40 text-xs">{{ $post->readingTimeLabel() }}</span>
                    @if ($post->published_at)
                    <span class="text-white/30 text-xs">· {{ $post->published_at->format('d M Y') }}</span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-black leading-tight text-white tracking-tight drop-shadow-2xl">
                    {{ $post->title }}
                </h1>
                @if ($post->resolvedExcerpt())
                <p class="mt-4 text-lg text-white/55 max-w-2xl">{{ $post->resolvedExcerpt(200) }}</p>
                @endif
            </div>
        </section>

        {{-- ══════════════════════════════════════════════
             ARTICLE BODY
        ══════════════════════════════════════════════ --}}
        <article id="article-content" class="px-6 py-16 max-w-4xl mx-auto">
            <div class="prose-dark">

                @foreach ($post->blocks ?? [] as $block)
                @php $data = $block['data'] ?? []; @endphp

                {{-- ── Text Block ── --}}
                @if ($block['type'] === 'text')
                @if (!empty($data['heading']))
                @php $tag = $data['heading_level'] ?? 'h2'; @endphp
                <{{ $tag }}>{{ $data['heading'] }}</{{ $tag }}>
                @endif
                @if (!empty($data['paragraph']))
                <div>{!! $data['paragraph'] !!}</div>
                @endif

                {{-- ── Image Block ── --}}
                @elseif ($block['type'] === 'image')
                @if (!empty($data['src']))
                <figure class="block-image" style="text-align: {{ $data['alignment'] ?? 'center' }}">
                    <img src="{{ asset('storage/' . $data['src']) }}"
                        alt="{{ $data['caption'] ?? '' }}" />
                    @if (!empty($data['caption']))
                    <figcaption>{{ $data['caption'] }}</figcaption>
                    @endif
                </figure>
                @endif

                {{-- ── Code Block ── --}}
                @elseif ($block['type'] === 'code')
                <div class="block-code-wrap">
                    <div class="block-code-header">
                        <span class="block-code-dot bg-red-500"></span>
                        <span class="block-code-dot bg-yellow-400"></span>
                        <span class="block-code-dot bg-green-400"></span>
                        <span class="ml-2 text-xs text-white/30 font-mono">{{ $data['language'] ?? 'code' }}</span>
                    </div>
                    <pre><code class="language-{{ $data['language'] ?? 'plaintext' }}">{{ $data['code'] ?? '' }}</code></pre>
                </div>

                {{-- ── Quote Block ── --}}
                @elseif ($block['type'] === 'quote')
                <blockquote class="block-quote">
                    <p>{{ $data['text'] ?? '' }}</p>
                    @if (!empty($data['attribution']))
                    <p class="block-quote-attribution">{{ $data['attribution'] }}</p>
                    @endif
                </blockquote>
                @endif
                @endforeach

            </div>
        </article>

        {{-- ══════════════════════════════════════════════
             AUTHOR BENTO CARD
        ══════════════════════════════════════════════ --}}
        <section class="px-6 pb-16 max-w-4xl mx-auto">
            <div class="author-card">
                @if ($post->authorAvatarUrl())
                <img src="{{ $post->authorAvatarUrl() }}" alt="{{ $post->author_name }}"
                    class="w-16 h-16 rounded-full object-cover border-2 border-white/10 shrink-0" />
                @else
                <div class="w-16 h-16 rounded-full bg-indigo-600/30 border border-indigo-500/30
                                flex items-center justify-center text-2xl font-bold text-indigo-300 shrink-0">
                    {{ strtoupper(substr($post->author_name ?? 'R', 0, 1)) }}
                </div>
                @endif
                <div>
                    <p class="text-xs uppercase tracking-widest text-white/30 mb-1">Written by</p>
                    <p class="font-bold text-white text-lg">{{ $post->author_name ?? 'RBeverything Team' }}</p>
                    @if ($post->author_bio)
                    <p class="text-white/55 text-sm mt-1 leading-relaxed">{{ $post->author_bio }}</p>
                    @endif
                    <div class="flex items-center gap-3 mt-3">
                        <button onclick="sharePost()"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium
                                   bg-white/5 border border-white/10 text-white/60 hover:text-white hover:border-white/30 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            Share this article
                        </button>
                        <a href="{{ route('insights.index') }}"
                            class="text-indigo-400 hover:text-indigo-300 text-xs transition">
                            ← All posts
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════
             MAGNETIC NEXT ARTICLE
        ══════════════════════════════════════════════ --}}
        @if ($next)
        <section class="px-6 pb-16 max-w-4xl mx-auto" id="next-article-section">
            <p class="text-xs uppercase tracking-widest text-white/25 text-center mb-6">Continue Reading</p>
            <a href="{{ route('insights.show', $next->slug) }}" class="next-article-card block group">
                <div class="relative h-52 overflow-hidden">
                    @if ($next->thumbnailUrl())
                    <img src="{{ $next->thumbnailUrl() }}" alt="{{ $next->title }}"
                        class="w-full h-full object-cover opacity-60" />
                    @else
                    <div class="w-full h-full bg-gradient-to-r from-indigo-950 to-violet-900"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full border {{ $next->typeColor() }}">
                            {{ ucfirst($next->type) }}
                        </span>
                        <h3 class="mt-2 text-xl md:text-2xl font-bold text-white group-hover:text-indigo-200 transition-colors">
                            {{ $next->title }}
                        </h3>
                        <p class="mt-1 text-white/40 text-sm">{{ $next->readingTimeLabel() }}
                            @if ($next->published_at) · {{ $next->published_at->format('d M Y') }} @endif
                        </p>
                    </div>
                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="px-3 py-1.5 rounded-full bg-indigo-600 text-white text-xs font-medium">
                            Read →
                        </span>
                    </div>
                </div>
            </a>
        </section>
        @endif

        {{-- ══════════════════════════════════════════════
             GRAND CTA
        ══════════════════════════════════════════════ --}}
        <section class="px-6 pb-20 max-w-4xl mx-auto">
            <div class="grand-cta px-8 py-14 text-center">
                <p class="text-white/30 text-xs uppercase tracking-widest mb-4">Got inspired?</p>
                <h2 class="text-2xl md:text-4xl font-black text-white leading-tight max-w-2xl mx-auto">
                    Have a big idea after reading this?
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">
                        Let's build it together
                    </span>
                    with RBeverything.
                </h2>
                <p class="mt-4 text-white/45 text-sm max-w-lg mx-auto">
                    We turn tech ideas into real products. From MVP to enterprise — we've got you covered.
                </p>
                <a href="/#services"
                    class="cta-btn inline-flex items-center gap-2 mt-8 px-8 py-4 rounded-full font-bold text-sm
                          bg-gradient-to-r from-indigo-600 to-violet-600 text-white
                          hover:from-indigo-500 hover:to-violet-500 transition-all duration-300 hover:scale-105">
                    Start Consultation
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </section>

    </main>

    <x-footer />

    {{-- Prism.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js" defer></script>

    <script>
        // ── Scroll handler: neon bar, floating pill, header fade ──
        const header = document.getElementById('site-header');
        const pill = document.getElementById('reading-pill');
        const bar = document.getElementById('neon-progress');
        const article = document.getElementById('article-content');

        function onScroll() {
            const scrollY = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const pct = docHeight > 0 ? (scrollY / docHeight) * 100 : 0;

            // Progress bar
            bar.style.width = pct + '%';

            // Header ↔ pill swap at 100px
            if (scrollY > 100) {
                header.classList.add('hidden-header');
                pill.classList.add('visible');
            } else {
                header.classList.remove('hidden-header');
                pill.classList.remove('visible');
            }
        }

        window.addEventListener('scroll', onScroll, {
            passive: true
        });
        onScroll(); // run once on load

        // ── Share ──
        function sharePost() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: location.href
                });
            } else {
                navigator.clipboard.writeText(location.href)
                    .then(() => alert('Link copied to clipboard!'))
                    .catch(() => alert('Share: ' + location.href));
            }
        }

        // ── Magnetic next article: pre-fetch on hover ──
        @if($next)
        const nextSection = document.getElementById('next-article-section');
        if (nextSection) {
            const nextLink = nextSection.querySelector('a');
            if (nextLink) {
                nextLink.addEventListener('mouseenter', () => {
                    const link = document.createElement('link');
                    link.rel = 'prefetch';
                    link.href = nextLink.href;
                    document.head.appendChild(link);
                }, {
                    once: true
                });
            }
        }
        @endif
    </script>
</body>

</html>