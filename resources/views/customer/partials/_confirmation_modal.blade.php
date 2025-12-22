<div x-show="confirmationModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 bg-black bg-opacity-60" aria-hidden="true" x-cloak></div>

<div x-show="confirmationModal" x-transition @click.outside="confirmationModal = false"
    class="fixed inset-0 z-50 m-auto h-fit max-h-[90vh] w-full max-w-md overflow-y-auto rounded-lg bg-gray-50 p-0 shadow-xl"
    x-cloak>

    <div class="flex flex-col items-center p-6 bg-white border-b">
        <div class="p-3 bg-orange-100 rounded-full">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mt-3">Konfirmasi Pesanan</h2>
        <p class="text-sm text-gray-500">Periksa kembali pesanan dan data diri Anda</p>
    </div>

    <div class="overflow-y-auto p-6 space-y-5" style="background-color: #FFFBF5;">

        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <h3 class="font-semibold mb-3">Detail Pesanan (<span x-text="cartCount"></span> Item)</h3>

            <template x-if="cartItems.length === 0">
                <p class="text-gray-500">Keranjang kosong.</p>
            </template>

            <div class="space-y-2">
                <template x-for="item in cartItems" :key="item.id">
                    <div class="flex justify-between items-start text-sm">
                        <div>
                            <p class="font-semibold" x-text="item.name"></p>
                            <p class="text-gray-500">
                                <span x-text="formatCurrency(item.price)"></span>
                                <span x-text="' &times; ' + item.quantity + ' item'"></span>
                            </p>
                            <template x-if="item.toppings">
                                <p class="text-xs text-gray-400" x-text="'+ ' + item.toppings"></p>
                            </template>
                        </div>
                        <p class="font-semibold text-gray-800" x-text="formatCurrency(item.subtotal)"></p>
                    </div>
                </template>
            </div>

            <div class="border-t pt-3 mt-3 space-y-1 text-sm">
                {{-- TIDAK ADA DISKON DI SINI, HANYA SUBTOTAL MURNI --}}
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal Awal:</span>
                    <span class="font-medium text-gray-800" x-text="formatCurrency(cartTotal)"></span>
                </div>
            </div>
            
            {{-- 🚨 PERBAIKAN LABEL DI SINI --}}
            <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t">
                <span>Total Pesanan (Pre-Diskon):</span>
                <span class="text-orange-600" x-text="formatCurrency(cartTotal)"></span>
            </div>
            <p x-show="isMember" class="text-xs text-green-600 font-semibold text-right -mt-2">
                Diskon Member akan dihitung di halaman berikutnya.
            </p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <h3 class="font-semibold mb-3">Informasi Pemesan</h3>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nama Pemesan:</span>
                    <span class="font-medium text-gray-800" x-text="customerInfo.name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Nomor Telepon:</span>
                    <span class="font-medium text-gray-800" x-text="customerInfo.phone"></span>
                </div>
                <template x-if="customerInfo.table_number">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomor Meja:</span>
                        <span class="font-medium text-gray-800" x-text="customerInfo.table_number"></span>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border text-center">
            <h3 class="font-semibold">Estimasi Waktu Persiapan</h3>
            <p class="text-2xl font-bold text-orange-600 my-1">15 Menit</p>
            <p class="text-xs text-gray-500">Waktu dapat berbeda tergantung antrian pesanan</p>
        </div>
    </div>

    <div class="p-4 bg-white border-t grid grid-cols-2 gap-3">
        <button type="button" @click="confirmationModal = false; checkoutModal = true"
            class="w-full text-center px-4 py-3 rounded-lg bg-white border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
            Kembali (Edit)
        </button>

        {{-- Tombol Lanjut dengan state submitting --}}
        <button type="button" @click="submitting = true; submitCheckout()" :disabled="submitting"
            :class="{ 'bg-gray-400 cursor-not-allowed': submitting, 'bg-orange-500 hover:bg-orange-600': !submitting }"
            class="w-full text-center px-4 py-3 rounded-lg text-white font-semibold transition">
            <span x-show="!submitting">Lanjut Ke Pembayaran</span>
            <span x-show="submitting">Memproses...</span>
        </button>
    </div>
</div>