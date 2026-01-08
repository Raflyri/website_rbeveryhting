@php
$setting = \App\Models\LandingSetting::latest()->first();
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - RBeverything</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #000;
        }

        /* Container Video yang Fixed */
        #hero-media-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -1;
            /* Di belakang konten */
            overflow: hidden;
        }

        /* Video/Gambar itu sendiri */
        #hero-media {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Transisi smooth biar gak kaget */
            transition: transform 0.1s linear;
            will-change: transform;
            /* Optimasi performa render */
        }

        /* Overlay Hitam Transparan */
        #hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            /* Gelap 30% */
            z-index: 1;
        }
    </style>
</head>

<body class="antialiased">

    <x-header />

    <div id="hero-media-container">
        @if($setting && $setting->hero_video)
        <video id="hero-media" autoplay muted loop playsinline>
            <source src="{{ asset('storage/' . $setting->hero_video) }}" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
        @elseif($setting && $setting->hero_image)
        <img id="hero-media" src="{{ asset('storage/' . $setting->hero_image) }}" alt="Background">
        @else
        <div class="w-full h-full bg-gray-900 flex items-center justify-center">
            <span class="text-gray-500">No Media</span>
        </div>
        @endif

        <div id="hero-overlay"></div>
    </div>

    <main class="relative z-10 w-full min-h-[150vh] flex flex-col items-center justify-start pt-[30vh]">

        <div class="text-center px-4 max-w-4xl mx-auto" data-aos="fade-up">
            <h1 class="text-6xl md:text-8xl font-bold text-white mb-6 tracking-tighter drop-shadow-2xl">
                {{ $setting->hero_title ?? 'COMING SOON' }}
            </h1>
            <p class="text-xl md:text-3xl text-gray-200 font-light max-w-2xl mx-auto leading-relaxed drop-shadow-md">
                Building the future of IT Solutions. <br>
                <!--<span class="font-semibold text-white">RBeverything.com</span>-->
            </p>

            <!--<div class="mt-12">
                <a href="#" class="group relative px-8 py-4 bg-white text-black font-bold rounded-full overflow-hidden inline-block transition-transform hover:scale-105">
                    <span class="relative z-10">{{ __('text.notify_me') }}</span>
                    <div class="absolute inset-0 h-full w-full scale-0 rounded-full transition-all duration-300 group-hover:scale-100 group-hover:bg-gray-200/50"></div>
                </a>
            </div>-->
        </div>

        <div class="mt-[40vh] w-full bg-gradient-to-t from-black via-black/80 to-transparent pt-32 pb-20 px-6">
            <div class="max-w-3xl mx-auto text-center text-gray-400">
                <h2 class="text-3xl font-bold text-white mb-6">{{ __('text.vision_title') }}</h2>
                <p class="text-lg leading-relaxed mb-8">
                    {{ $setting->vision_desc ?? __('text.vision_desc') }}
                </p>
            </div>

            <div class="mt-10 border-t border-white/10 pt-10">
                <x-footer />
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            const heroMedia = document.getElementById('hero-media');
            const heroOverlay = document.getElementById('hero-overlay');

            // Rumus Zoom:
            // Scale awal 1. Setiap 1000px scroll, nambah zoom 0.5x
            // Semakin besar angka pembagi (1500), semakin pelan zoom-nya.
            const scaleValue = 1 + (scrollPosition / 1500);

            // Rumus Gelap (Opacity):
            // Semakin ke bawah, overlay makin gelap (maksimal 0.8)
            const opacityValue = 0.3 + (scrollPosition / 1000);

            // Terapkan ke element
            if (heroMedia) {
                heroMedia.style.transform = `scale(${scaleValue})`;
            }

            if (heroOverlay) {
                // Batasi opacity max 0.9 biar gak gelap total
                heroOverlay.style.backgroundColor = `rgba(0,0,0,${Math.min(opacityValue, 0.9)})`;
            }
        });
    </script>
</body>

</html>