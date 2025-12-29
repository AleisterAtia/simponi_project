<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

    {{-- Struktur Layout Sidebar dan Konten --}}
    <div class="min-h-screen flex">

        {{-- 1. Sidebar Kiri (Fixed) --}}
        @include('admin.sidebar')

        {{-- 2. Area Konten Utama --}}
        <div class="flex-1 ml-64"> {{-- ml-64 untuk mengimbangi lebar sidebar --}}

            {{-- Navigation Bar di Atas Konten (Search Bar & Profile/Role) --}}
            <nav class="bg-white border-b border-gray-200 shadow-md">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">

                        {{-- Search Bar --}}
                        <div class="flex items-center w-full max-w-lg">
                            <label for="search" class="sr-only">Cari</label>
                            <div class="relative w-full">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.197 5.197a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                </div>
                                <input type="search" id="search"
                                    class="block w-full rounded-lg border-gray-300 pl-10 pr-4 py-2 text-sm focus:border-yellow-500 focus:ring-yellow-500"
                                    placeholder="Cari pesanan, menu, atau laporan...">
                            </div>
                        </div>

                        {{-- Profile/Role dan Tanggal --}}
                        <div class="flex items-center space-x-4">
                            {{-- Tanggal --}}
                            <span class="text-sm text-gray-500 hidden sm:block">
                                Minggu, 12 Oktober 2025
                            </span>

                            {{-- Role Selectors --}}
                            <div class="flex space-x-2">
                                <button
                                    class="text-gray-600 font-medium py-1 px-3 rounded-lg border border-gray-300 hover:bg-gray-100 transition duration-150">Menu</button>
                                <button
                                    class="bg-gray-100 text-gray-800 font-medium py-1 px-3 rounded-lg border border-gray-300 transition duration-150">Admin</button>
                                <button
                                    class="bg-green-600 text-white font-medium py-1 px-3 rounded-lg hover:bg-green-700 transition duration-150">Kasir</button>
                            </div>

                            {{-- User Dropdown (Breeze default) --}}
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Content default Breeze untuk Profile dan Logout -->
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>

                    </div>
                </div>
            </nav>

            {{-- Header Konten (Dashboard Title) --}}
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- Konten Utama Halaman --}}
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
