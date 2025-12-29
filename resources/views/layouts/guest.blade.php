<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mr. Wayouji POS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    
    <div class="min-h-screen flex">
        
        {{-- ==================================================================== --}}
        {{-- BAGIAN KIRI: LOGO BESAR DENGAN LATAR BELAKANG ESTETIK                --}}
        {{-- ==================================================================== --}}
        <div class="hidden lg:flex w-1/2 bg-orange-900 items-center justify-center relative overflow-hidden">
            
            {{-- 1. Gambar Latar Belakang (Suasana Kafe/Kopi Gelap) --}}
            {{-- Saya ganti ke gambar yang lebih gelap agar logo Anda lebih menonjol --}}
            <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=2070&auto=format&fit=crop" 
                 class="absolute inset-0 w-full h-full object-cover opacity-50 mix-blend-overlay blur-[2px]" 
                 alt="Cafe Ambience Background">
            
            {{-- Overlay Gradient agar teks/logo lebih terbaca --}}
            <div class="absolute inset-0 bg-gradient-to-t from-orange-900/80 to-black/40"></div>

            {{-- 2. Konten Utama di Tengah Kiri --}}
            <div class="relative z-10 flex flex-col items-center p-12 text-center">
                
                {{-- LOGO UTAMA YANG BESAR --}}
                <div class="mb-8 p-2 bg-white/10 rounded-full backdrop-blur-sm border-4 border-white/20 shadow-2xl">
                    <img src="{{ asset('images/LogoToko.jpeg') }}" 
                         alt="Mr. Wayouji Logo" 
                         class="w-64 h-64 object-cover rounded-full">
                </div>

                {{-- Slogan --}}
                <h2 class="text-3xl font-bold text-white mb-2 tracking-wide">Mr. Wayouji</h2>
                <div class="h-1 w-20 bg-orange-500 mb-4 rounded-full"></div>
                <p class="text-lg text-orange-100 font-medium tracking-wider uppercase opacity-90">
                    Premium Taste, Unforgettable Moments.
                </p>
            </div>
        </div>

        {{-- ==================================================================== --}}
        {{-- BAGIAN KANAN: FORM LOGIN                                            --}}
        {{-- ==================================================================== --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-[#FFFBF5] p-8">
            <div class="w-full sm:max-w-md bg-white px-8 py-10 shadow-[0_20px_50px_rgba(0,0,0,0.07)] rounded-3xl border border-orange-100/50">
                
                <div class="mb-8 text-center">
                    <h3 class="text-2xl font-bold text-gray-800">Selamat Datang</h3>
                    <p class="text-gray-500 text-sm mt-1">Silakan masuk ke akun Anda</p>
                </div>

                {{-- Slot Form Login --}}
                {{ $slot }}
                
            </div>

            <div class="mt-8 text-center text-sm text-gray-400 font-medium">
                &copy; {{ date('Y') }} Mr. Wayouji Point of Sale System.
            </div>
        </div>

    </div>
</body>
</html>