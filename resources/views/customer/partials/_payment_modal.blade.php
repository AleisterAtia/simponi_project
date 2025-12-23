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

<div x-show="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
    <div @click="paymentModal = false" class="fixed inset-0 bg-black bg-opacity-60"></div>

    <div
        class="fixed inset-0 z-50 m-auto h-fit max-h-[90vh] w-full max-w-md overflow-y-auto rounded-lg bg-gray-50 p-0 shadow-xl">

        <div class="p-4 border-b bg-white flex items-center">
            <button @click="paymentModal = false; confirmationModal = true" class="text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="text-center w-full">
                <h2 class="text-lg font-semibold">Metode Pembayaran</h2>
                <p class="text-sm text-gray-500">Pilih cara pembayaran yang Anda inginkan</p>
            </div>
        </div>

        <div class="p-6 space-y-4" style="background-color: #FFFBF5;">

            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <h3 class="font-semibold mb-3">Ringkasan Pesanan</h3>
                <div class="space-y-1 text-sm">

                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal Awal:</span>
                        <span class="font-medium text-gray-800"
                            x-text="formatCurrency(createdOrder?.subtotal || 0)"></span>
                    </div>

                    <template x-if="createdOrder?.discount_amount > 0">
                        <div class="flex justify-between text-red-600 font-semibold border-b pb-2">
                            <span>Diskon Member (<span
                                    x-text="createdOrder?.discount_percentage || 0"></span>%):</span>
                            <span>— <span x-text="formatCurrency(createdOrder?.discount_amount || 0)"></span></span>
                        </div>
                    </template>
                </div>

                <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t">
                    <span>TOTAL BAYAR:</span>
                    <span class="text-orange-600" x-text="formatCurrency(createdOrder?.total_price || 0)"></span>
                </div>
            </div>

            <div>
                <h3 class="font-semibold mb-2 text-gray-700">Pilih Metode Pembayaran</h3>
                <div class="space-y-3">

                    <button @click="selectedPayment = 'cash'"
                        :class="selectedPayment === 'cash' ? 'ring-2 ring-orange-500 border-orange-500' : 'border-gray-300'"
                        class="w-full flex items-center p-4 bg-white rounded-lg border transition duration-150">
                        <div class="p-2 bg-orange-100 rounded-full mr-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold">Tunai (Cash)</p>
                            <p class="text-sm text-gray-500">Bayar langsung dengan uang tunai</p>
                        </div>
                        <span class="ml-auto text-xs font-semibold text-gray-500">Recommended</span>
                    </button>

                    <button @click="selectedPayment = 'qris'"
                        :class="selectedPayment === 'qris' ? 'ring-2 ring-orange-500 border-orange-500' : 'border-gray-300'"
                        class="w-full flex items-center p-4 bg-white rounded-lg border transition duration-150">

                        <div class="mr-4 flex-shrink-0">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Logo_QRIS.svg/1200px-Logo_QRIS.svg.png"
                                alt="QRIS" class="h-8 w-auto object-contain">
                        </div>

                        <div class="text-left">
                            <p class="font-semibold">QRIS</p>
                            <p class="text-sm text-gray-500">Scan QR menggunakan Mobile Banking</p>
                        </div>

                        <span class="ml-auto text-xs font-semibold text-gray-500">Digital</span>
                    </button>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border min-h-[150px] flex items-center justify-center">

                <template x-if="!selectedPayment">
                    <p class="text-gray-500 text-sm">Silakan pilih metode pembayaran di atas.</p>
                </template>

                <template x-if="selectedPayment === 'cash'">
                    <div class="text-center text-gray-700">
                        <div class="bg-orange-100 p-3 rounded-full inline-block mb-2">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="font-semibold">Bayar di Kasir</h4>
                        <p class="text-sm mt-1 max-w-xs mx-auto">Silakan menuju kasir dan sebutkan nama Anda untuk
                            melakukan pembayaran tunai.</p>
                    </div>
                </template>

                <template x-if="selectedPayment === 'qris'">
                    <div class="w-full">
                        <p class="text-center font-semibold text-gray-700 mb-2 text-sm">Scan QRIS di bawah ini:</p>

                        <div class="mx-auto bg-white border-2 border-gray-200 rounded-xl shadow-sm overflow-hidden"
                            style="max-width: 280px;">

                            <div class="relative bg-white pt-3 pb-2 px-3 text-center">
                                <div
                                    class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-600 via-red-500 to-red-600">
                                </div>

                                <div class="flex justify-between items-center mb-1">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Logo_QRIS.svg/1200px-Logo_QRIS.svg.png"
                                        alt="QRIS" class="h-6">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Logo_GPN.png"
                                        alt="GPN" class="h-4 opacity-80">
                                </div>

                                <h3 class="font-bold text-sm text-gray-900 leading-tight uppercase mt-1">Mr. Wayojiai
                                    Buah Premium</h3>
                                <p class="text-[10px] text-gray-400">NMID: ID2025000012345</p>
                            </div>

                            <div class="flex justify-center py-2 relative bg-white">
                                <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=WAYOJIAI-ORDER-${createdOrder?.id}`"
                                    alt="QRIS Code" class="border p-1 rounded">
                            </div>

                            <div class="bg-gray-50 border-t border-gray-100 p-1 text-center">
                                <p class="text-[9px] text-gray-400">Dicetak oleh: Mr. Wayojiai System</p>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

        </div>

        <div class="p-4 bg-white border-t">
            <button type="button" @click="confirmPayment()" :disabled="!selectedPayment"
                :class="!selectedPayment ? 'bg-gray-400 cursor-not-allowed' : 'bg-orange-500 hover:bg-orange-600'"
                class="w-full text-center px-4 py-3 rounded-lg text-white font-semibold transition">
                Bayar
            </button>
        </div>
    </div>
</div>
