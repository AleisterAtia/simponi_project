<!-- Modal Pembayaran 2 Langkah (KASIR) -->
<div x-show="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-200" x-cloak>

    <!-- Overlay -->
    <div @click="paymentModal = false" class="fixed inset-0 bg-black bg-opacity-60"></div>

    <!-- Panel -->
    <div class="relative z-50 bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">

        <!-- Header (Berubah sesuai Step) -->
        <div class="bg-orange-500 p-4 text-white flex justify-between items-center">
            <h3 class="text-lg font-bold" x-text="paymentStep === 1 ? 'Pilih Metode Pembayaran' : 'Informasi Pelanggan'">
            </h3>
            <button @click="paymentModal = false" class="text-white hover:text-orange-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- KONTEN STEP 1: PILIH METODE PEMBAYARAN -->
        <div x-show="paymentStep === 1" x-transition:enter="transition ease-out duration-300" class="p-6 space-y-6">
            <div class="text-center">
                <p class="text-gray-500 text-sm mb-1">Total Tagihan</p>
                <h2 class="text-4xl font-extrabold text-gray-900" x-text="formatCurrency(summaryTotal)"></h2>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Opsi Tunai -->
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method_opt" value="cash" class="peer sr-only"
                        x-model="selectedPayment">
                    <div
                        class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50 h-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 mb-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-semibold text-gray-800">Tunai</span>
                    </div>
                </label>
                <!-- Opsi QRIS -->
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method_opt" value="qris" class="peer sr-only"
                        x-model="selectedPayment">
                    <div
                        class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 h-full">
                        <svg class="w-10 h-10 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold text-gray-800">QRIS</span>
                    </div>
                </label>
            </div>

            <button @click="paymentStep = 2" :disabled="!selectedPayment"
                :class="!selectedPayment ? 'bg-gray-300 cursor-not-allowed' : 'bg-orange-500 hover:bg-orange-600 text-white'"
                class="w-full py-3 rounded-xl font-bold text-lg transition-all">
                Lanjut
            </button>
        </div>

        <!-- KONTEN STEP 2: INFO PELANGGAN -->
        <div x-show="paymentStep === 2" x-transition:enter="transition ease-out duration-300" class="p-6 space-y-4"
            x-cloak>
            <p class="text-sm text-gray-600 text-center mb-4">Masukkan data pelanggan (opsional)</p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                <input type="text" x-model="finalCustomerName" placeholder="Contoh: Budi (atau kosongkan)"
                    class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="tel" x-model="finalCustomerPhone" placeholder="Contoh: 08123..."
                    class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500">
            </div>

            <div class="pt-4 flex gap-3">
                <button @click="paymentStep = 1"
                    class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                    Kembali
                </button>
                <button @click="confirmFinalPayment()" :disabled="submitting"
                    class="flex-1 py-3 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 transition flex items-center justify-center gap-2">
                    <span x-show="!submitting">Konfirmasi & Bayar</span>
                    <span x-show="submitting">Memproses...</span>
                </button>
            </div>
        </div>

    </div>
</div>
