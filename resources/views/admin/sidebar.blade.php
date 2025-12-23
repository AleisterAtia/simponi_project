@php
    // --- Penyesuaian Logika Pengecekan Aktif (Logika Paling Ketat) ---

    
    // 1. Tentukan status aktif per sub-menu (Hanya cocokkan Resource Route yang benar)
    $isDaftarMenuActive = request()->is('admin/menu') || request()->is('admin/menu/*'); 
    $isCategoriesActive = request()->is('admin/categories') || request()->is('admin/categories/*');
    $isToppingsActive = request()->is('admin/toppings') || request()->is('admin/toppings/*');
    
    // 2. Tentukan status aktif untuk item menu diskon
    $isDiscountsActive = request()->is('admin/discounts') || request()->is('admin/discounts/*'); // ⬅️ TAMBAHAN BARU
        


    // 1. Tentukan status aktif per sub-menu (Hanya cocokkan Resource Route yang benar)
    $isDaftarMenuActive = request()->is('admin/menu') || request()->is('admin/menu/*');
    $isCategoriesActive = request()->is('admin/categories') || request()->is('admin/categories/*');
    $isToppingsActive = request()->is('admin/toppings') || request()->is('admin/toppings/*');

    // 2. Tentukan status aktif untuk item menu diskon
    $isDiscountsActive = request()->is('admin/discounts') || request()->is('admin/discounts/*'); // ⬅️ TAMBAHAN BARU


    // 3. Tentukan apakah grup 'Manajemen Menu' secara keseluruhan harus dianggap aktif
    $isMenuSectionActive = $isDaftarMenuActive || $isCategoriesActive || $isToppingsActive;

    // 4. Tentukan status aktif untuk item menu lainnya
    $isDashboardActive = request()->is('admin');
    $isRewardsActive = request()->is('admin/rewards') || request()->is('admin/rewards/*');
    $isCustomersActive = request()->is('admin/customers') || request()->is('admin/customers/*');
    $isOrdersActive = request()->is('admin/orders') || request()->is('admin/orders/*');
    $isReportsActive = request()->is('admin/reports') || request()->is('admin/reports/*');
    $isSettingsActive = request()->is('admin/settings');
@endphp

<aside x-data="{ open: false }" class="text-white w-64 flex-shrink-0 min-h-screen bg-orange-500 flex flex-col">

    {{-- Bagian Header/Logo --}}
    <div class="flex items-center space-x-3 p-4">
        {{-- Ikon Logo --}}
        <img src="{{ asset('images/logop.png') }}" alt="Wayouji Logo" class="h-10 w-10 border rounded-lg bg-white">
        <div class="leading-tight">
            <h1 class="text-xl font-bold text-white">Mr.Wayojiai</h1>
            {{-- Mengganti subtitle agar sesuai gambar --}}
            <p class="text-xs text-orange-100">Point of Sale</p>
        </div>
    </div>

    {{-- Menu Navigasi --}}
    <nav :class="{ 'block': open, 'hidden': !open }" class="px-3 py-4 sm:block space-y-2 flex-1">

        {{-- Mengganti heading menu --}}
        <h3 class="text-sm font-semibold text-orange-100 uppercase tracking-wider mb-2 px-1">
            Menu Admin
        </h3>

        {{-- Daftar Menu --}}

        {{-- Item Dashboard --}}
        <a href="/admin"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition duration-150 ease-in-out

            {{ $isDashboardActive
                ? 'bg-white text-orange-600 font-semibold shadow-sm'
                : 'text-white hover:bg-orange-600' }}">

            {{ $isDashboardActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">
 
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- Item Manajemen Menu (Dropdown) --}}

        <div x-data="{ 
            // Ambil status awal: Jika rute aktif, paksa 'true'. Jika Dashboard, paksa 'false'. Jika rute lain, ambil dari LocalStorage.
            openMenu: {{ $isMenuSectionActive ? 'true' : ($isDashboardActive ? 'false' : '(localStorage.getItem("adminMenuOpen") === "true")') }},
            

        <div x-data="{
            // Ambil status awal: Jika rute aktif, paksa 'true'. Jika Dashboard, paksa 'false'. Jika rute lain, ambil dari LocalStorage.
            openMenu: {{ $isMenuSectionActive ? 'true' : ($isDashboardActive ? 'false' : '(localStorage.getItem("adminMenuOpen") === "true")') }},

 4c2a5a2a1c7d0eb480e7c5f93af77bd9ec5d7b98
            // Fungsi untuk toggle dan simpan status ke local storage
            toggleMenu() {
                this.openMenu = !this.openMenu;
                localStorage.setItem('adminMenuOpen', this.openMenu);
            }

        }" 
        x-init="
            // FIX KONSISTENSI DI DASHBOARD: 
            // Ketika berada di Dashboard, selalu pastikan status tersimpan adalah 'false' 
            // agar saat navigasi berikutnya, statusnya benar-benar reset.
            if ({{ $isDashboardActive ? 'true' : 'false' }}) {
                localStorage.setItem('adminMenuOpen', 'false');
            }
        "
        class="space-y-2">
            
            {{-- Tombol Dropdown Utama --}}
            <button @click="toggleMenu()" 
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition duration-150 ease-in-out
                {{ $isMenuSectionActive
                    ? 'bg-white text-orange-600 font-semibold shadow-sm'
                    : 'text-white hover:bg-orange-600' }}">

        }" x-init="// FIX KONSISTENSI DI DASHBOARD:
        // Ketika berada di Dashboard, selalu pastikan status tersimpan adalah 'false'
        // agar saat navigasi berikutnya, statusnya benar-benar reset.
        if ({{ $isDashboardActive ? 'true' : 'false' }}) {
            localStorage.setItem('adminMenuOpen', 'false');
        }" class="space-y-2">

            {{-- Tombol Dropdown Utama --}}
            <button @click="toggleMenu()"
                class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition duration-150 ease-in-out
                {{ $isMenuSectionActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">
                <span class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1z" />
                        <path fill-rule="evenodd"
                            d="M4 5a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 10a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-1zM4 15a1 1 0 011-1h10a1 1 0 011 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1v-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Manajemen Menu</span>
                </span>
                {{-- Ikon panah (rotasi) --}}

                <svg x-bind:class="{ 'rotate-90': openMenu, 'rotate-0': !openMenu }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">

                <svg x-bind:class="{ 'rotate-90': openMenu, 'rotate-0': !openMenu }"
                    class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            {{-- Sub-menu Dropdown --}}

            <div x-show="openMenu" x-transition:enter="transition ease-out duration-150" 
                 x-transition:enter-start="opacity-0 transform -translate-y-2" 
                 x-transition:enter-end="opacity-100 transform translate-y-0" 
                 x-transition:leave="transition ease-in duration-100" 
                 x-transition:leave-start="opacity-100 transform translate-y-0" 
                 x-transition:leave-end="opacity-0 transform -translate-y-2" 
                 class="space-y-1 ml-4 border-l border-orange-400">
                
                {{-- Sub-Item Daftar Menu --}}
                <a href="{{ route('admin.menu.index') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg transition duration-150 ease-in-out text-sm
                    {{ $isDaftarMenuActive
                        ? 'bg-white text-orange-600 font-semibold shadow-sm'
                        : 'text-white hover:bg-orange-600' }}">
                    <span>Daftar Menu</span>
                </a>
                
                {{-- Sub-Item Kategori --}}
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg transition duration-150 ease-in-out text-sm
                    {{ $isCategoriesActive
                        ? 'bg-white text-orange-600 font-semibold shadow-sm'
                        : 'text-white hover:bg-orange-600' }}">

            <div x-show="openMenu" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="space-y-1 ml-4 border-l border-orange-400">

                {{-- Sub-Item Daftar Menu --}}
                <a href="{{ route('admin.menu.index') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg transition duration-150 ease-in-out text-sm
                    {{ $isDaftarMenuActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">
                    <span>Daftar Menu</span>
                </a>

                {{-- Sub-Item Kategori --}}
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg transition duration-150 ease-in-out text-sm
                    {{ $isCategoriesActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">

                    <span>Kategori</span>
                </a>

                {{-- Sub-Item Topping --}}
                <a href="{{ route('admin.toppings.index') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg transition duration-150 ease-in-out text-sm

                    {{ $isToppingsActive
                        ? 'bg-white text-orange-600 font-semibold shadow-sm'
                        : 'text-white hover:bg-orange-600' }}">

                    {{ $isToppingsActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">
 
                    <span>Topping</span>
                </a>
            </div>
        </div>
        {{-- END Item Manajemen Menu (Dropdown) --}}

        


        {{-- ⬇️ ITEM BARU: DISKON ⬇️ --}}
        <a href="{{ route('admin.discounts.index') }}"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition duration-150 ease-in-out
            {{ $isDiscountsActive // Menggunakan variabel baru
                ? 'bg-white text-orange-600 font-semibold shadow-sm'
                : 'text-white hover:bg-orange-600' }}">

            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13H5M12 8l4 4-4 4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 13H5M12 8l4 4-4 4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

            </svg>
            <span>Diskon</span>
        </a>
        {{-- ⬆️ END ITEM DISKON ⬆️ --}}


        {{-- Item Reward --}}
        <a href="{{ route('admin.rewards.index') }}"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition duration-150 ease-in-out

            {{ $isRewardsActive
                ? 'bg-white text-orange-600 font-semibold shadow-sm'
                : 'text-white hover:bg-orange-600' }}">
            
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3 3h2l1 5H4l-1-5zM6 3h8l1 5H7L6 3zm10 0h2l-1 5h-2l1-5zM4 10h12v2H4v-2zm1 4h10v2H5v-2z"/>

            {{ $isRewardsActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3 3h2l1 5H4l-1-5zM6 3h8l1 5H7L6 3zm10 0h2l-1 5h-2l1-5zM4 10h12v2H4v-2zm1 4h10v2H5v-2z" />

            </svg>

            <span>Reward</span>
        </a>

        <a href="{{ route('admin.customers.index') }}"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition duration-150 ease-in-out

            {{ $isCustomersActive
                ? 'bg-white text-orange-600 font-semibold shadow-sm'
                : 'text-white hover:bg-orange-600' }}">
            
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>


            {{ $isCustomersActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
            </svg>

 
            <span>Manajemen Member</span>
        </a>
        {{-- Item Pesanan --}}
        <a href="/admin/orders"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition duration-150 ease-in-out

            {{ $isOrdersActive
                ? 'bg-white text-orange-600 font-semibold shadow-sm'
                : 'text-white hover:bg-orange-600' }}">

            {{ $isOrdersActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">
 
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd"
                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h6a1 1 0 100-2H7z"
                    clip-rule="evenodd" />
            </svg>
            <span>Pesanan</span>
        </a>

        {{-- Item Laporan --}}
        <a href="{{ route('admin.reports') }}"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition duration-150 ease-in-out

            {{ $isReportsActive
                ? 'bg-white text-orange-600 font-semibold shadow-sm'
                : 'text-white hover:bg-orange-600' }}">

            {{ $isReportsActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 1a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5z"
                    clip-rule="evenodd" />
                <path d="M10 8a2 2 0 100 4 2 2 0 000-4z" />
            </svg>
            <span>Laporan</span>
        </a>

        {{-- Item Pengaturan --}}
        <a href="{{ route('admin.settings') }}"
            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition duration-150 ease-in-out

            {{ $isSettingsActive
                ? 'bg-white text-orange-600 font-semibold shadow-sm'
                : 'text-white hover:bg-orange-600' }}">

            {{ $isSettingsActive ? 'bg-white text-orange-600 font-semibold shadow-sm' : 'text-white hover:bg-orange-600' }}">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 00-2.3 1.054A1.532 1.532 0 016 7.5v2a1.532 1.532 0 01-1.054 2.3c-1.56.38-1.56 2.6 0 2.98a1.532 1.532 0 001.054 2.3A1.532 1.532 0 018.5 16v-2a1.532 1.532 0 012.3-1.054c1.56-.38 1.56-2.6 0-2.98a1.532 1.532 0 00-1.054-2.3A1.532 1.532 0 0110 5.5v2a1.532 1.532 0 01-2.3 1.054c-1.56.38-1.56 2.6 0 2.98a1.532 1.532 0 001.054 2.3c1.56-.38 1.56-2.6 0-2.98a1.532 1.532 0 011.054-2.3c.38-1.56 2.6-1.56 2.98 0a1.532 1.532 0 001.054 2.3c-1.56.38-1.56 2.6 0 2.98a1.532 1.532 0 001.054 2.3c.38-1.56 2.6-1.56 2.98 0a1.532 1.532 0 001.054 2.3A1.532 1.532 0 0114 7.5v-2a1.532 1.532 0 01-1.054-2.3c-1.56.38-1.56 2.6 0-2.98a1.532 1.532 0 00-1.054-2.3z"
                    clip-rule="evenodd" />
            </svg>
            <span>Pengaturan</span>
        </a>

    </nav>

    {{-- Bagian Footer (Pemisah dan Logout) --}}
    <div class="px-3 py-4">
        {{-- Garis Pemisah --}}
        <hr class="border-t border-orange-400 opacity-50 mb-4">

        {{-- Judul "Admin" --}}
        <h3 class="text-sm font-semibold text-orange-100 uppercase tracking-wider mb-2 px-1">
            Admin
        </h3>

        {{-- Tombol Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center space-x-3 w-full text-left px-3 py-2.5 rounded-lg
                        bg-white text-orange-600 font-semibold shadow-sm
                        hover:bg-gray-100 transition duration-150 ease-in-out">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>

</aside>