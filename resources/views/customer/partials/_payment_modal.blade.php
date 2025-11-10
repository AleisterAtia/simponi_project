<!-- Modal 3: Pembayaran -->
<div x-show="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
    <!-- Overlay -->
    <div @click="paymentModal = false" class="fixed inset-0 bg-black bg-opacity-60"></div>

    <!-- Panel -->
    <div
        class="fixed inset-0 z-50 m-auto h-fit max-h-[90vh] w-full max-w-md overflow-y-auto rounded-lg bg-gray-50 p-0 shadow-xl">

        <!-- Header -->
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

        <!-- Konten -->
        <div class="p-6 space-y-4" style="background-color: #FFFBF5;">

            <!-- Ringkasan Pesanan -->
            <div class="bg-white p-4 rounded-lg shadow-sm border" x-data="{ subtotal: (createdOrder?.total_price || 0) / 1.1, tax: (createdOrder?.total_price || 0) * 0.1 / 1.1 }">
                <h3 class="font-semibold mb-3">Ringkasan Pesanan</h3>
                <div class="border-t pt-3 mt-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-800" x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pajak (10%):</span>
                        <span class="font-medium text-gray-800" x-text="formatCurrency(tax)"></span>
                    </div>
                </div>
                <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t">
                    <span>Total Pembayaran:</span>
                    <span x-text="formatCurrency(createdOrder?.total_price || 0)"></span>
                </div>
            </div>

            <!-- Pilihan Pembayaran -->
            <div>
                <h3 class="font-semibold mb-2 text-gray-700">Pilih Metode Pembayaran</h3>
                <div class="space-y-3">
                    <!-- Opsi 1: Tunai (Cash) -->
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

                    <!-- Opsi 2: QRIS -->
                    <button @click="selectedPayment = 'qris'"
                        :class="selectedPayment === 'qris' ? 'ring-2 ring-orange-500 border-orange-500' : 'border-gray-300'"
                        class="w-full flex items-center p-4 bg-white rounded-lg border transition duration-150">
                        <div class="p-2 bg-blue-100 rounded-full mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                                <path d="M3 10h4M3 14h4M17 10h4M17 14h4M5 3v4M15 3v4M5 17v4M15 17v4"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="font-semibold">QRIS</p>
                            <p class="text-sm text-gray-500">Scan QR menggunakan Mobile Banking</p>
                        </div>
                        <span class="ml-auto text-xs font-semibold text-gray-500">Digital</span>
                    </button>
                </div>
            </div>

            <!-- Tampilan Info Tambahan (QRIS / Cash) -->
            <div class="bg-white p-4 rounded-lg shadow-sm border min-h-[150px] flex items-center justify-center">
                <!-- Tampilan Default -->
                <template x-if="!selectedPayment">
                    <p class="text-gray-500 text-sm">Silakan pilih metode pembayaran di atas.</p>
                </template>
                <!-- Tampilan jika pilih CASH -->
                <template x-if="selectedPayment === 'cash'">
                    <div class="text-center text-gray-700">
                        <h4 class="font-semibold">Bayar di Kasir</h4>
                        <p class="text-sm mt-1">Anda akan membayar secara tunai langsung di kasir.</p>
                    </div>
                </template>
                <!-- Tampilan jika pilih QRIS -->
                <template x-if="selectedPayment === 'qris'">
                    <div class="text-center text-gray-700">
                        <h4 class="font-semibold mb-2">Scan untuk Membayar</h4>
                        <!-- Ganti dengan URL QR Code Anda -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=ExampleQRCode"
                            alt="QRIS Code" class="mx-auto border rounded-lg">
                    </div>
                </template>
            </div>

        </div>

        <!-- Footer Tombol -->
        <div class="p-4 bg-white border-t">
            <button type="button" @click="confirmPayment()" :disabled="!selectedPayment"
                :class="!selectedPayment ? 'bg-gray-400 cursor-not-allowed' : 'bg-orange-500 hover:bg-orange-600'"
                class="w-full text-center px-4 py-3 rounded-lg text-white font-semibold transition">
                Konfirmasi Pembayaran
            </button>
        </div>
    </div>
</div>
