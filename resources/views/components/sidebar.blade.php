<div class="flex flex-col w-64 bg-gray-800 h-full fixed top-0 left-0 z-20">
    {{-- Logo dan Nama Brand --}}
    <div class="flex-shrink-0 flex items-center p-4 border-b border-gray-700">
        {{-- Ganti path gambar/icon sesuai asset Anda --}}
        <span class="text-3xl text-yellow-400 mr-2">☕</span> 
        <div class="leading-tight">
            <h1 class="text-xl font-bold text-white">Wayouji</h1>
            <p class="text-xs text-gray-400">Premium Beverages</p>
        </div>
    </div>

    {{-- Menu Navigasi --}}
    <div class="flex-grow p-4 space-y-4 overflow-y-auto">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
            Menu Utama
        </h3>
        
        <div class="space-y-1">
            {{-- Dashboard --}}
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{-- Icon Home --}}
                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2 2m-2-2m-2-2h3m-7 2H5a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1v-10a1 1 0 00-1-1h-3" />
                </svg>
                <span>Dashboard</span>
            </x-sidebar-link>

            {{-- Manajemen Menu --}}
            <x-sidebar-link href="#" :active="request()->is('admin/menu*')">
                {{-- Icon Menu --}}
                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
                <span>Manajemen Menu</span>
            </x-sidebar-link>

            {{-- Rewards --}}
            <x-sidebar-link :href="route('admin.rewards.index')" :active="request()->is('admin/rewards*')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 12v8a2 2 0 01-2 2h-4v-10h6zm-8 0v10H6a2 2 0 01-2-2v-8h6zm8-2h-6V4h4a2 2 0 012 2v4zm-8 0H4V6a2 2 0 012-2h4v6zm0 0V4m0 6h6" />
                </svg>
                <span>Rewards</span>
            </x-sidebar-link>
            
            {{-- Pesanan --}}
            <x-sidebar-link href="#" :active="request()->is('admin/pesanan*')">
                {{-- Icon Pesanan (Clipboard) --}}
                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span>Pesanan</span>
            </x-sidebar-link>

            {{-- Laporan --}}
            <x-sidebar-link href="#" :active="request()->is('admin/laporan*')">
                {{-- Icon Laporan (Chart) --}}
                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6" />
                </svg>
                <span>Laporan</span>
            </x-sidebar-link>
            
            {{-- Pengaturan --}}
            <x-sidebar-link href="#" :active="request()->is('admin/pengaturan*')">
                {{-- Icon Pengaturan (Cog) --}}
                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.82 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.82 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.82-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.82-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Pengaturan</span>
            </x-sidebar-link>
        </div>
    </div>
</div>

{{-- Div untuk memberi padding pada konten utama --}}
<div class="ml-64">
    {{ $slot }}
</div>
