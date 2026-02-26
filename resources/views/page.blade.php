<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} - RBeverything</title>

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
        /* Prose styles for Rich Text Output from Filament */
        .prose-custom {
            color: #cbd5e1;
            /* slate-300 */
        }

        .prose-custom h1,
        .prose-custom h2,
        .prose-custom h3,
        .prose-custom h4 {
            color: white;
            font-weight: 700;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }

        .prose-custom p {
            margin-bottom: 1.2em;
            line-height: 1.75;
        }

        .prose-custom a {
            color: #60a5fa;
            /* blue-400 */
            text-decoration: underline;
        }

        .prose-custom ul {
            list-style-type: disc;
            padding-left: 1.5em;
            margin-bottom: 1em;
        }

        .prose-custom ol {
            list-style-type: decimal;
            padding-left: 1.5em;
            margin-bottom: 1em;
        }

        .prose-custom blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1em;
            font-style: italic;
            color: #94a3b8;
        }
    </style>
</head>

<body class="antialiased font-sans selection:bg-blue-500 selection:text-white bg-slate-950 text-slate-100 flex flex-col min-h-screen">

    <x-header />

    <main class="flex-1 max-w-4xl w-full mx-auto px-6 py-32 xl:py-40">
        <article>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-8">{{ $page->title }}</h1>

            <div class="prose-custom max-w-none">
                {!! $page->content !!}
            </div>
        </article>
    </main>

    <x-footer />

    <script>
        feather.replace();
    </script>
</body>

</html>