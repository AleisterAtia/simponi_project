<div x-show="toppingModalOpen" class="fixed inset-0 z-40 flex items-center justify-center" x-cloak>

    <div @click="toppingModalOpen = false" x-show="toppingModalOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50">
    </div>

    <div x-show="toppingModalOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative z-50 w-full max-w-lg bg-white rounded-lg shadow-xl flex flex-col max-h-[80vh]">

        <div class="flex justify-between items-start p-4 border-b">
            <div>
                <h2 class="text-xl font-bold text-gray-800" x-text="currentMenu?.name">Nama Menu</h2>
                <p class="text-sm text-gray-500">Pilih topping tambahan untuk minuman Anda</p>
            </div>
            <button @click="toppingModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-3">
            <h3 class="text-lg font-semibold text-gray-700">Tambah Topping</h3>

            {{-- Loop daftar topping dari 'allToppings' di Alpine --}}
            <template x-for="topping in allToppings" :key="topping.id">
                <label :for="'topping-' + topping.id"
                    class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all"
                    :class="{ 'bg-orange-50 border-orange-500 ring-2 ring-orange-200': selectedToppings.includes(topping.id) }">
                    <div class="flex items-center">
                        <input type="checkbox" :id="'topping-' + topping.id" :value="topping.id"
                            x-model="selectedToppings" @change="calculateToppingTotal()"
                            class="h-5 w-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <span class="ml-3 font-medium text-gray-800" x-text="topping.name"></span>
                    </div>
                    <span class="text-sm font-semibold text-gray-600"
                        x-text="'+' + formatCurrency(topping.price)"></span>
                </label>
            </template>
        </div>

        <div class="p-6 border-t bg-gray-50 rounded-b-lg">
            <div class="flex justify-between items-center mb-3 text-sm">
                <span class="text-gray-600">Harga Dasar:</span>
                <span class="font-semibold" x-text="formatCurrency(currentMenu?.price || 0)"></span>
            </div>
            <div class="flex justify-between items-center font-bold text-xl">
                <span class="text-gray-800">Total:</span>
                <span class="text-orange-600" x-text="formatCurrency(toppingTotal)"></span>
            </div>

            <button @click="addItemToCart()"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition mt-4 flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <span>Tambah ke Keranjang</span>
            </button>
        </div>
    </div>
</div>
