<!-- Modal 4a: Sukses Tunai -->
<div x-show="cashSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
    <!-- Overlay -->
    <div @click="window.location.reload()" class="fixed inset-0 bg-black bg-opacity-70"></div>

    <!-- Panel -->
    <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-green-500 mx-auto" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="text-2xl font-bold mt-4">Pesanan Diterima!</h2>
        <p class="text-gray-600 mt-2">Silakan lakukan pembayaran di kasir.</p>

        <div class="my-6 p-4 bg-gray-50 border rounded-lg text-left">
            <p class="flex justify-between">
                <span class="text-gray-600">Order ID:</span>
                <span class="font-semibold" x-text="createdOrder?.order_code"></span>
            </p>
            <p class="flex justify-between mt-1">
                <span class="text-gray-600">Nama:</span>
                <span class="font-semibold" x-text="createdOrder?.customer_name"></span>
            </p>
            <div class="border-t my-3"></div>
            <p class="flex justify-between text-xl font-bold">
                <span>Total Bayar:</span>
                <span x-text="formatCurrency(createdOrder?.total_price || 0)"></span>
            </p>
        </div>

        <button @click="window.location.reload()"
            class="w-full bg-orange-500 text-white font-semibold py-3 rounded-lg hover:bg-orange-600">
            Selesai
        </button>
    </div>
</div>
