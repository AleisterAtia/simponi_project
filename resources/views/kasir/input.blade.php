@extends('kasir.kasir')

@section('content')
    {{-- Kirim data menus DAN toppings ke Alpine --}}
    <div class="p-6 md:p-10" x-data="manualOrderApp({{ $menus }}, {{ $toppings }})">

        {{-- ... Header Judul (Sama seperti sebelumnya) ... --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Input Pesanan Manual</h1>
                <p class="text-gray-500">Klik item untuk memilih topping & varian</p>
            </div>
        </div>

        {{-- Pesan Error/Success (Sama seperti sebelumnya) --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- BAGIAN KIRI: DAFTAR MENU --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">

                    {{-- Tab Kategori (Sama) --}}
                    <div class="flex space-x-2 border-b border-gray-200 mb-5 overflow-x-auto pb-2">
                        <button @click="tab = 'semua'"
                            :class="tab === 'semua' ? 'border-orange-500 text-orange-600' : 'text-gray-500'"
                            class="py-2 px-4 font-semibold border-b-2 transition">Semua</button>
                        @foreach ($categories as $category)
                            <button @click="tab = {{ $category->id }}"
                                :class="tab === {{ $category->id }} ? 'border-orange-500 text-orange-600' : 'text-gray-500'"
                                class="py-2 px-4 font-semibold border-b-2 transition">{{ $category->name }}</button>
                        @endforeach
                    </div>

                    {{-- Grid Menu --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 h-[60vh] overflow-y-auto pr-2">
                        @forelse($menus as $menu)
                            <div x-show="tab === 'semua' || tab === {{ $menu->category_id ?? 'semua' }}">
                                {{-- PERUBAHAN: Saat diklik, panggil openModal bukan langsung addItem --}}
                                <button @click="openModal({{ json_encode($menu) }})"
                                    class="w-full h-full text-left p-3 bg-white rounded-lg shadow hover:border-orange-500 hover:shadow-md transition border">
                                    <img src="{{ $menu->image_url ?? 'https://placehold.co/150' }}"
                                        alt="{{ $menu->name }}" class="w-full h-24 object-cover rounded-md">
                                    <h3 class="font-semibold text-gray-800 text-sm mt-2 line-clamp-2">{{ $menu->name }}
                                    </h3>
                                    <p class="text-orange-600 font-bold text-sm">Rp
                                        {{ number_format($menu->price, 0, ',', '.') }}</p>
                                </button>
                            </div>
                        @empty
                            <p>Tidak ada menu.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- BAGIAN KANAN: RINGKASAN PESANAN --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 h-fit sticky top-10">
                <h2 class="text-xl font-semibold mb-4 border-b pb-3">Ringkasan Pesanan</h2>

                <form action="{{ route('kasir.pembayaran.show') }}" method="POST"> @csrf
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        <template x-for="(item, index) in summaryItems" :key="index">
                            <div class="border-b pb-4 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-800" x-text="item.name"></p>
                                        {{-- Tampilkan Topping yang dipilih di sini --}}
                                        <template x-if="item.selectedToppings && item.selectedToppings.length > 0">
                                            <p class="text-xs text-gray-500 italic">
                                                + <span x-text="item.selectedToppings.map(t => t.name).join(', ')"></span>
                                            </p>
                                        </template>
                                        <p class="text-xs text-gray-500" x-text="formatCurrency(item.totalPricePerItem)">
                                        </p>
                                    </div>

                                    {{-- Kontrol Qty --}}
                                    <div class="flex items-center border rounded-lg ml-2">
                                        <button type="button" @click="updateQty(index, -1)"
                                            class="px-2 text-gray-600">-</button>
                                        <span class="px-2 text-sm font-medium" x-text="item.quantity"></span>
                                        <button type="button" @click="updateQty(index, 1)"
                                            class="px-2 text-gray-600">+</button>
                                    </div>
                                </div>
                                <div class="text-right font-bold text-sm mt-1">
                                    <span x-text="formatCurrency(item.totalPricePerItem * item.quantity)"></span>
                                </div>

                                {{-- HIDDEN INPUTS UNTUK BACKEND --}}
                                <input type="hidden" :name="`items[${index}][menu_id]`" :value="item.id">
                                <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                                <input type="hidden" :name="`items[${index}][price]`" :value="item.totalPricePerItem">

                                {{-- Loop input topping agar terbaca array --}}
                                <template x-for="(top, tIndex) in item.selectedToppings" :key="tIndex">
                                    <input type="hidden" :name="`items[${index}][toppings][]`" :value="top.id">
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Total & Tombol Submit --}}
                    <div class="border-t mt-4 pt-4">
                        <div class="flex justify-between font-bold text-xl mb-4">
                            <span>Total:</span>
                            <span x-text="formatCurrency(summaryTotal)"></span>
                        </div>
                        <input type="hidden" name="total_price" :value="summaryTotal">
                        <button type="submit" :disabled="summaryItems.length === 0"
                            class="w-full bg-orange-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-orange-600 transition disabled:bg-gray-300">
                            Lanjut Ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL PILIH TOPPING (Baru) --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">

                {{-- Modal Header --}}
                <div class="bg-orange-500 p-4 flex justify-between items-center text-white">
                    <h3 class="font-bold text-lg" x-text="tempItem ? tempItem.name : 'Pilih Menu'"></h3>
                    <button @click="closeModal()" class="text-white hover:text-gray-200">&times;</button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <p class="text-gray-600 mb-4 font-semibold">Pilih Topping (Opsional):</p>

                    {{-- Di dalam Modal Body --}}
<div class="space-y-3">
    <template x-if="!allToppings || allToppings.length === 0">
        <p class="text-gray-400 text-sm">Tidak ada topping tersedia.</p>
    </template>

    <template x-for="topping in allToppings" :key="topping.id">
        <label class="flex items-center justify-between p-3 border rounded cursor-pointer hover:bg-gray-50">
            <div class="flex items-center">
                {{-- PERBAIKAN DISINI: Gunakan topping.id --}}
                <input type="checkbox"
                       :value="topping.id"
                       x-model="tempSelectedToppings"
                       class="form-checkbox h-5 w-5 text-orange-600 rounded">

                <span class="ml-3 text-gray-700" x-text="topping.name"></span>
            </div>
            <span class="text-sm font-semibold text-gray-500">+ <span x-text="formatCurrency(topping.price)"></span></span>
        </label>
    </template>
</div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-100 p-4 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500">Harga per item:</p>
                        <p class="font-bold text-lg text-orange-600" x-text="formatCurrency(calculateTempTotal())"></p>
                    </div>
                    <button @click="confirmAddItem()"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                        Simpan Pesanan
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- SCRIPT ALPINE.JS --}}
    <script></script>
@endsection
