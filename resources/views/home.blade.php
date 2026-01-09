<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RBeverything') }} - Everything You Need</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-white">

    <x-header />

    <section class="relative h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            @if($setting && $setting->hero_video)
            <video autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="{{ asset('storage/' . $setting->hero_video) }}" type="video/mp4">
            </video>
            @elseif($setting && $setting->hero_image)
            <img src="{{ asset('storage/' . $setting->hero_image) }}" class="w-full h-full object-cover" alt="Hero">
            @else
            <div class="w-full h-full bg-gray-900"></div>
            @endif
            <div class="absolute inset-0 bg-black/60"></div>
        </div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto text-white">
            <h1 class="text-4xl md:text-7xl font-bold mb-6 tracking-tight">
                {{ $setting->hero_title ?? 'RBeverything' }}
            </h1>
            <p class="text-xl md:text-2xl font-light text-gray-200 max-w-3xl mx-auto">
                {{ $setting->vision_desc ?? 'Your IT Consultant Partner. Everything you need!' }}
            </p>
            <div class="mt-10">
                <a href="#services" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-full transition duration-300">
                    Explore Services
                </a>
            </div>
        </div>
    </section>

    <section id="services" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl">Our Services</h2>
                <p class="mt-4 text-lg text-gray-600">Solusi teknologi komprehensif untuk kebutuhan bisnis Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($services as $service)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100 p-8">
                    <div class="h-16 w-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6 text-blue-600">
                        @if($service->icon)
                        <img src="{{ asset('storage/' . $service->icon) }}" class="h-10 w-10 object-contain" alt="{{ $service->title }}">
                        @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $service->title }}</h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $service->short_description }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-footer />

</body>

</html>