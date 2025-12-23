    @extends('layouts.customer_layout')

    @section('content')

        {{-- 1. PERSIAPAN DATA --}}
        @php
            $currentCustomer = Auth::check() ? Auth::user()->customer : null;
            // Cek apakah user adalah member aktif
            $isMemberLogged = $currentCustomer && $isMember;
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">

            {{-- ================================================================== --}}
            {{-- LOGIKA TAMPILAN: JIKA MEMBER vs JIKA TAMU (NON-MEMBER)             --}}
            {{-- ================================================================== --}}

            @if ($isMemberLogged)
                {{-- ================================================= --}}
                {{-- TAMPILAN A: DASHBOARD KHUSUS MEMBER (Login)       --}}
                {{-- ================================================= --}}

                <div x-data="{ memberTab: 'dashboard' }" class="mb-10">

                    {{-- HEADER DASHBOARD MEMBER --}}
                    <div
                        class="bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden mb-6">
                        <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 rounded-full bg-white opacity-10"></div>

                        <div class="flex flex-col md:flex-row justify-between items-center relative z-10">
                            <div>
                                <h2 class="text-3xl font-bold mb-1">Selamat Datang, {{ Auth::user()->name }}!</h2>
                                <p class="text-orange-100 opacity-90">Member sejak
                                    {{ \Carbon\Carbon::parse($currentCustomer->created_at)->format('d/m/Y') }}</p>
                            </div>

                            <div
                                class="mt-4 md:mt-0 bg-white/20 backdrop-blur-sm rounded-xl p-4 min-w-[140px] text-center border border-white/30">
                                <span class="block text-4xl font-bold" x-text="currentPoints.toLocaleString('id-ID')"></span>
                                <span class="text-sm font-medium text-orange-50">Total Poin</span>
                            </div>
                        </div>
                    </div>

                    {{-- TABS NAVIGASI --}}
                    <div class="flex space-x-3 mb-6 overflow-x-auto">
                        <button @click="memberTab = 'dashboard'"
                            :class="memberTab === 'dashboard' ? 'bg-orange-500 text-white shadow-md' :
                                'bg-white text-gray-600 hover:bg-gray-50'"
                            class="px-6 py-2 rounded-full font-semibold transition-all duration-200 whitespace-nowrap">
                            Dashboard
                        </button>
                        <button @click="memberTab = 'redeem'"
                            :class="memberTab === 'redeem' ? 'bg-orange-500 text-white shadow-md' :
                                'bg-white text-gray-600 hover:bg-gray-50'"
                            class="px-6 py-2 rounded-full font-semibold transition-all duration-200 whitespace-nowrap">
                            Tukar Hadiah
                        </button>
                        <button @click="memberTab = 'history'"
                            :class="memberTab === 'history' ? 'bg-orange-500 text-white shadow-md' :
                                'bg-white text-gray-600 hover:bg-gray-50'"
                            class="px-6 py-2 rounded-full font-semibold transition-all duration-200 whitespace-nowrap">
                            Riwayat
                        </button>
                    </div>

                    {{-- KONTEN TAB --}}

                    {{-- 1. TAB DASHBOARD --}}
                    {{-- 1. TAB DASHBOARD --}}
                    <div x-show="memberTab === 'dashboard'" x-transition:enter="transition ease-out duration-300"
                        {{-- UBAH: Grid disesuaikan agar muat 4 kartu (md:grid-cols-2 lg:grid-cols-4) --}} class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        {{-- KARTU 1: STATUS MEMBER --}}
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-semibold text-gray-800">Status Member</h3>
                                <span class="text-orange-500"><i class="bi bi-crown"></i></span>
                            </div>
                            <p class="text-2xl font-bold {{ $currentCustomer->status_color }}">
                                {{ $currentCustomer->status_label }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $currentCustomer->next_level_message }}
                            </p>
                        </div>

                        {{-- KARTU 2: BENEFIT MEMBER (BARU) --}}
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-semibold text-gray-800">Benefit Member</h3>
                                <span class="text-orange-500">
                                    {{-- Ikon Diskon/Tag --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-tag-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 6.586 1H2zm4 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                    </svg>
                                </span>
                            </div>
                            {{-- Menampilkan info Diskon 10% sesuai database --}}
                            <p class="text-2xl font-bold text-orange-600">Diskon 10%</p>
                            <p class="text-xs text-gray-500 mt-1">Otomatis di setiap transaksi</p>
                        </div>

                        {{-- KARTU 3: POIN TERSEDIA --}}
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-semibold text-gray-800">Poin Tersedia</h3>
                                <span class="text-orange-500"><i class="bi bi-gift"></i></span>
                            </div>
                            <p class="text-2xl font-bold text-orange-600" x-text="currentPoints.toLocaleString('id-ID')">
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Siap ditukar dengan hadiah</p>
                        </div>

                        {{-- KARTU 4: TOTAL TRANSAKSI --}}
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-sm font-semibold text-gray-800">Total Transaksi</h3>
                                <span class="text-orange-500"><i class="bi bi-bag"></i></span>
                            </div>
                            <p class="text-2xl font-bold text-orange-600">12</p>
                            <p class="text-xs text-gray-500 mt-1">Transaksi bulan ini</p>
                        </div>
                    </div>

                    {{-- 2. TAB TUKAR HADIAH --}}
                    <div x-show="memberTab === 'redeem'" x-transition:enter="transition ease-out duration-300">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Hadiah yang Tersedia</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @forelse ($rewards as $reward)
                                <div
                                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
                                    <div class="bg-gray-50 rounded-lg p-4 mb-4 flex items-center justify-center h-40">
                                        <img src="https://placehold.co/150x150/orange/white?text={{ substr($reward->name, 0, 3) }}"
                                            alt="{{ $reward->name }}" class="h-full object-contain mix-blend-multiply">
                                    </div>
                                    <h4 class="font-bold text-gray-800 mb-1">{{ $reward->name }}</h4>
                                    <div class="flex items-center justify-between mt-4">
                                        <span
                                            class="text-orange-600 font-bold">{{ number_format($reward->points_required) }}
                                            poin</span>
                                        <button @click="redeemReward({{ $reward->id }})"
                                            :disabled="currentPoints < {{ $reward->points_required }}"
                                            :class="currentPoints >= {{ $reward->points_required }} ?
                                                'bg-orange-100 text-orange-600 hover:bg-orange-200' :
                                                'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                            class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                                            <span
                                                x-text="currentPoints >= {{ $reward->points_required }} ? 'Tukar' : 'Kurang'"></span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="col-span-4 text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                                    <p class="text-gray-500">Belum ada reward tersedia.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 3. TAB RIWAYAT --}}
                    <div x-show="memberTab === 'history'" x-transition:enter="transition ease-out duration-300">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Transaksi</h3>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Poin
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15/1/2024</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Orange Juice +
                                            Smoothie</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">Rp 45.000</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-orange-600 font-bold text-right">
                                            +4</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @else
                {{-- ================================================= --}}
                {{-- TAMPILAN B: BANNER PROMO (Belum Login / Bukan Member) --}}
                {{-- ================================================= --}}

                {{-- Pastikan file ini berisi kode banner 'Gabung Member & Dapatkan Poin' --}}
                @include('customer.partials.promo_section')
            @endif


            {{-- ================================================= --}}
            {{-- BAGIAN MENU (SELALU TAMPIL UNTUK SEMUA)           --}}
            {{-- ================================================= --}}
            <h2 class="text-xl font-bold mb-4 text-gray-800 mt-8">Menu Populer</h2>
            <div class="flex space-x-4 overflow-x-auto pb-4 mb-8">
                @foreach ($popularMenus as $menu)
                    <div
                        class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md hover:shadow-lg transition flex items-center p-3">
                        <img src="{{ $menu->image_url ?? 'https://placehold.co/80x80' }}" alt="{{ $menu->name }}"
                            class="w-20 h-20 object-cover rounded-md">
                        <div class="ml-3 flex-1">
                            <h3 class="font-semibold text-gray-800 text-sm line-clamp-2">{{ $menu->name }}</h3>
                            <p class="text-orange-600 font-bold text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <button @click="addItemToCart({{ $menu->id }})"
                            class="bg-orange-100 text-orange-600 p-2 rounded-full hover:bg-orange-500 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>

            <h2 class="text-2xl font-bold mb-6 text-gray-800">Pesan Menu</h2>

            <div class="flex space-x-2 border-b border-gray-200 mb-6 overflow-x-auto">
                <button @click="tab = 'semua'"
                    :class="tab === 'semua' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500'"
                    class="py-3 px-5 font-semibold border-b-2 transition whitespace-nowrap">Semua Menu</button>
                @foreach ($categories as $category)
                    <button @click="tab = '{{ $category->name }}'"
                        :class="tab === '{{ $category->name }}' ? 'border-orange-500 text-orange-600' :
                            'border-transparent text-gray-500'"
                        class="py-3 px-5 font-semibold border-b-2 transition whitespace-nowrap">{{ $category->name }}</button>
                @endforeach
            </div>

            <div class="mb-6">
                <input type="text" placeholder="Cari menu berdasarkan nama..." x-model="search"
                    class="w-full p-3 border rounded-lg focus:ring-orange-500 focus:border-orange-500" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 min-h-[300px]">
                @foreach ($menus as $menu)
                    {{-- LOGIKA STOK HABIS --}}
                    @php
                        $isOutOfStock = $menu->stock <= 0;
                    @endphp

                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 flex flex-col relative overflow-hidden"
                        x-show="(tab === 'semua' || tab === '{{ $menu->category->name ?? '' }}') && ('{{ strtolower($menu->name) }}'.includes(search.toLowerCase()))"
                        x-transition>

                        {{-- GAMBAR PRODUK --}}
                        <div class="w-full h-48 bg-gray-100 rounded-lg overflow-hidden relative">

                            {{-- 1. OVERLAY JIKA HABIS --}}
                            @if ($isOutOfStock)
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10">
                                    <span
                                        class="bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide shadow-md transform -rotate-12">
                                        Stok Habis
                                    </span>
                                </div>
                            @endif

                            {{-- Gambar (Diberi efek grayscale jika habis) --}}
                            <img src="{{ $menu->image_url ?? 'https://placehold.co/300x200' }}"
                                alt="{{ $menu->name }}"
                                class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-75' : '' }}">
                        </div>

                        {{-- NAMA & DESKRIPSI --}}
                        <h3 class="mt-4 font-semibold text-lg {{ $isOutOfStock ? 'text-gray-400' : 'text-gray-800' }}">
                            {{ $menu->name }}
                        </h3>
                        <p class="text-gray-600 text-sm flex-1 my-1 line-clamp-2">{{ $menu->description }}</p>

                        {{-- HARGA & TOMBOL --}}
                        <div class="mt-4 flex justify-between items-center">
                            <span class="{{ $isOutOfStock ? 'text-gray-400' : 'text-orange-600' }} font-bold text-lg">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </span>

                            {{-- 2. TOMBOL DISABLED JIKA HABIS --}}
                            @if ($isOutOfStock)
                                <button disabled
                                    class="bg-gray-300 text-gray-500 cursor-not-allowed px-4 py-2 rounded-lg font-bold text-sm">
                                    Habis
                                </button>
                            @else
                                <button class="btn-add" @click="openToppingModal({{ json_encode($menu) }})">
                                    Tambah
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endsection

    @push('scripts')
        <script>
            // NOTE: Fungsi redeemReward didefinisikan di customer_layout.blade.php
        </script>
    @endpush
