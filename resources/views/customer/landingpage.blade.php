@extends('layouts.customer_layout')

@section('content')
    {{-- PROMO BANNER (File dari Langkah 1) --}}
    @include('customer.partials.promo_section')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ================================================= --}}
        {{-- BAGIAN BARU: MENU POPULER (Horizontal Scroll)     --}}
        {{-- ================================================= --}}
        <h2 class="text-xl font-bold mb-4 text-gray-800">Menu Populer</h2>
        <div class="flex space-x-4 overflow-x-auto pb-4 mb-8">
            {{-- Loop menu populer Anda di sini --}}
            @foreach ($popularMenus as $menu)
                {{-- Asumsi Anda punya variabel $popularMenus --}}
                <div
                    class="flex-shrink-0 w-64 bg-white rounded-lg shadow-md hover:shadow-lg transition flex items-center p-3">
                    <img src="{{ $menu->image_url ?? 'https://placehold.co/80x80' }}" alt="{{ $menu->name }}"
                        class="w-20 h-20 object-cover rounded-md">
                    <div class="ml-3 flex-1">
                        <h3 class="font-semibold text-gray-800">{{ $menu->name }}</h3>
                        <p class="text-orange-600 font-bold text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                    </div>
                    <button {{-- Panggil fungsi 'addItemToCart' dari layout Anda --}} @click="addItemToCart({{ $menu->id }})"
                        class="bg-orange-100 text-orange-600 p-2 rounded-full hover:bg-orange-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>
            @endforeach
            {{-- Akhir Loop --}}
        </div>

        {{-- ================================================= --}}
        {{-- BAGIAN BARU: KATEGORI MENU (Dengan Tabs)          --}}
        {{-- ================================================= --}}
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Pesan Menu</h2>

        <div class="flex space-x-2 border-b border-gray-200 mb-6">
            {{--
              Tombol-tombol ini dikontrol oleh 'tab' dari wayoujiApp()
              di layout utama Anda.
            --}}
            <button @click="tab = 'semua'"
                :class="tab === 'semua' ? 'border-orange-500 text-orange-600' :
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 px-5 font-semibold border-b-2 transition">
                Semua Menu
            </button>
            <button @click="tab = 'Jus'"
                :class="tab === 'Jus' ? 'border-orange-500 text-orange-600' :
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 px-5 font-semibold border-b-2 transition">
                Jus
            </button>
            <button @click="tab = 'Es Kulkul'"
                :class="tab === 'Es Kulkul' ? 'border-orange-500 text-orange-600' :
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="py-3 px-5 font-semibold border-b-2 transition">
                Es Kulkul
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Loop semua menu Anda di sini --}}
            @foreach ($menus as $menu)
                {{--
                  Kartu ini akan Tampil/Sembunyi berdasarkan tab yang dipilih.
                  x-show="tab === 'semua' || tab === '{{ $menu->category_name }}'"

                  !! CATATAN: Jika Anda tidak punya $menu->category_name,
                  hapus saja atribut x-show di bawah ini.
                --}}
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-4 flex flex-col"
                    x-show="tab === 'semua' || tab === '{{ $menu->category_name ?? 'semua' }}'" x-transition>

                    {{-- Gambar --}}
                    <div class="w-full h-48 bg-gray-100 rounded-lg overflow-hidden">
                        <img src="{{ $menu->image_url ?? 'https://placehold.co/300x200' }}" alt="{{ $menu->name }}"
                            class="w-full h-full object-cover">
                    </div>

                    {{-- Detail Teks --}}
                    <h3 class="mt-4 font-semibold text-lg text-gray-800">{{ $menu->name }}</h3>
                    <p class="text-gray-600 text-sm flex-1 my-1">{{ $menu->description }}</p>

                    {{-- Harga & Tombol Tambah --}}
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-orange-600 font-bold text-lg">Rp
                            {{ number_format($menu->price, 0, ',', '.') }}</span>

                        {{-- Ganti 'onclick' dengan '@click' dari Alpine --}}
                        <button class="btn-add" @click="openToppingModal({{ json_encode($menu) }})">
                            Tambah
                        </button>
                    </div>
                </div>
            @endforeach
            {{-- Akhir Loop --}}

        </div>
    </div>
@endsection
