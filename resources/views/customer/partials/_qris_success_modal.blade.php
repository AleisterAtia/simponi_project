<!-- Modal 4b: Sukses QRIS -->
<div x-show="qrisSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
    <!-- Overlay -->
    <div @click="window.location.reload()" class="fixed inset-0 bg-black bg-opacity-70"></div>

    <!-- Panel -->
    <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl p-8 text-center">
        <h2 class="text-2xl font-bold">Scan QRIS untuk Membayar</h2>
        <p class="text-gray-600 mt-2">Pesanan Anda akan diproses setelah pembayaran berhasil.</p>

        <div class="my-6 p-4 bg-gray-50 border rounded-lg">
            <!-- Ganti dengan URL QR Code Anda -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ExampleQRCode" alt="QRIS Code"
                class="mx-auto border rounded-lg shadow-lg">

            <div class="text-left mt-4">
                <p class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-semibold" x-text="createdOrder?.order_code"></span>
                </p>
                <div class="border-t my-3"></div>
                <p class="flex justify-between text-xl font-bold">
                    <span>Total Bayar:</span>
                    <span x-text="formatCurrency(createdOrder?.total_price || 0)"></span>
                </p>
            </div>
        </div>

        <button @click="window.location.reload()"
            class="w-full bg-orange-500 text-white font-semibold py-3 rounded-lg hover:bg-orange-600">
            Selesai
        </button>
    </div>
</div>
