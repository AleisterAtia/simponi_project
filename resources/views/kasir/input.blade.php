@extends('kasir.kasir') {{-- Pastikan ini @extends layout kasir oranye Anda --}}

@section('content')
    <div class="p-6 md:p-10" x-data="manualOrderApp({{ $menus }})">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Input Pesanan Manual</h1>
                <p class="text-gray-500">Klik pada item menu untuk menambahkannya ke pesanan</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><strong>Error:</strong> {{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">

                    <div class="flex space-x-2 border-b border-gray-200 mb-5 overflow-x-auto pb-2">
                        <button @click="tab = 'semua'"
                            :class="tab === 'semua' ? 'border-orange-500 text-orange-600' :
                                'border-transparent text-gray-500'"
                            class="py-2 px-4 font-semibold border-b-2 transition whitespace-nowrap">Semua</button>
                        @foreach ($categories as $category)
                            <button @click="tab = {{ $category->id }}"
                                :class="tab === {{ $category->id }} ? 'border-orange-500 text-orange-600' :
                                    'border-transparent text-gray-500'"
                                class="py-2 px-4 font-semibold border-b-2 transition whitespace-nowrap">{{ $category->name }}</button>
                        @endforeach
                    </div>

                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 h-[60vh] overflow-y-auto pr-2">
                        @forelse($menus as $menu)
                            <div x-show="tab === 'semua' || tab === {{ $menu->category_id ?? 'semua' }}"
                                x-transition.opacity>
                                <button @click="addItem({{ json_encode($menu) }})"
                                    class="w-full h-full text-left p-3 bg-white rounded-lg shadow border border-gray-200 hover:border-orange-500 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-orange-300">
                                    <img src="{{ $menu->image_url ?? 'https://placehold.co/150' }}"
                                        alt="{{ $menu->name }}" class="w-full h-24 object-cover rounded-md">
                                    <h3 class="font-semibold text-gray-800 text-sm mt-2 line-clamp-2">{{ $menu->name }}
                                    </h3>
                                    <p class="text-orange-600 font-bold text-sm">Rp
                                        {{ number_format($menu->price, 0, ',', '.') }}</p>
                                </button>
                            </div>
                        @empty
                            <p class="col-span-full text-center text-gray-500">Tidak ada menu
                                yang tersedia.</p>
                        @endforelse
                    </div>

                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 h-fit sticky top-10">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h2 class="text-xl font-semibold text-gray-800">Ringkasan Pesanan</h2>
                    <button @click="clearSummary()" x-show="summaryItems.length > 0"
                        class="text-sm text-red-500 hover:underline">Hapus Semua</button>
                </div>

                <form id="manual-order-form" action="{{ route('kasir.pembayaran.show') }}" method="POST">
                    @csrf
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        <template x-if="summaryItems.length === 0">
                            <p class="text-gray-500 text-center py-6">Belum ada item.</p>
                        </template>

                        <template x-for="(item, index) in summaryItems" :key="index">
                            <div class="flex items-center gap-3 border-b pb-4 last:border-b-0">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500" x-text="formatCurrency(item.price)"></p>
                                </div>
                                <div class="flex items-center border rounded-lg">
                                    <button type="button" @click="updateQty(index, -1)"
                                        class="px-2 py-1 hover:bg-gray-100 text-gray-600">-</button>
                                    <span class="px-3 text-sm font-medium" x-text="item.quantity"></span>
                                    <button type="button" @click="updateQty(index, 1)"
                                        class="px-2 py-1 hover:bg-gray-100 text-gray-600">+</button>
                                </div>
                                <div class="text-right min-w-[80px]">
                                    <p class="font-semibold text-gray-800"
                                        x-text="formatCurrency(item.price * item.quantity)"></p>
                                </div>
                                <input type="hidden" :name="`items[${index}][menu_id]`" :value="item.id">
                                <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                                <input type="hidden" :name="`items[${index}][price]`" :value="item.price">
                            </div>
                        </template>
                    </div>

                    <div class="border-t mt-4 pt-4">
                        <div class="flex justify-between font-bold text-xl text-gray-800 mb-4">
                            <span>Total:</span>
                            <span x-text="formatCurrency(summaryTotal)"></span>
                        </div>
                        <input type="hidden" name="total_price" :value="summaryTotal">

                        <button type="submit" :disabled="summaryItems.length === 0"
                            :class="summaryItems.length === 0 ? 'bg-gray-300' : 'bg-orange-500 hover:bg-orange-600'"
                            class="w-full text-white font-bold py-3 px-4 rounded-lg transition">
                            Lanjut Ke Pembayaran
                        </button>
                    </div>

                </form>
            </div>
        </div>


    </div>

    {{-- SCRIPT ALPINE.JS UNTUK LOGIKA HALAMAN --}}
@endsection
