{{--
    PERHATIKAN: TIDAK ADA "x-data" DI FILE INI.
    Semua state (checkoutModal, errors, customerInfo)
    diambil dari x-data di tag <body> layout utama.
--}}

<div x-show="checkoutModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 bg-black bg-opacity-50" aria-hidden="true" x-cloak></div>

<div x-show="checkoutModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.outside="checkoutModal = false"
    class="fixed inset-0 z-50 m-auto h-fit max-w-lg overflow-y-auto rounded-lg bg-white p-6 shadow-xl"
    style="background-color: #FFFBF5; max-width: 450px;" x-cloak>
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Informasi Pesanan</h3>
            <p class="text-sm text-gray-600">Lengkapi data untuk menyelesaikan pesanan anda</p>
        </div>
        <button @click="checkoutModal = false" class="text-gray-400 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <form id="checkout-form" class="mt-4 space-y-4" x-on:submit.prevent="showConfirmation">
        <div>
            <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama <span
                    class="text-red-500">*</span></label>
            <input type="text" name="customer_name" id="customer_name" x-model="customerInfo.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500
    {{ Auth::check() ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                {{ Auth::check() ? 'readonly' : '' }} required>
            <template x-if="errors.customer_name">
                <p class="mt-1 text-sm text-red-600" x-text="errors.customer_name[0]"></p>
            </template>
        </div>

        <div>
            <label for="customer_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon <span
                    class="text-red-500">*</span></label>
            <input type="tel" name="customer_phone" id="customer_phone" x-model="customerInfo.phone"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500
    {{ Auth::check() ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                {{ Auth::check() ? 'readonly' : '' }} required>
            <template x-if="errors.customer_phone">
                <p class="mt-1 text-sm text-red-600" x-text="errors.customer_phone[0]"></p>
            </template>
        </div>

        <div>
            <label for="table_number" class="block text-sm font-medium text-gray-700">Nomor Meja (Opsional)</label>
            <input type="text" name="table_number" id="table_number" x-model="customerInfo.table_number"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
            <textarea name="notes" id="notes" rows="3" x-model="customerInfo.notes"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
        </div>

        <div class="flex items-center justify-between border-t pt-4">
            <span class="text-lg font-bold">Total Pembayaran</span>
            <span class="text-lg font-bold text-gray-900" x-text="formatCurrency(totalCartPrice)"></span>
        </div>

        <button type="submit"
            class="w-full rounded-md bg-orange-500 px-4 py-3 text-lg font-semibold text-white shadow-sm hover:bg-orange-600">
            Lanjut Ke Review
        </button>
    </form>
</div>
