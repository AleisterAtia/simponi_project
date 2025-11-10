<div x-cloak>

    <button @click="openCart = true"
        class="relative p-2 text-gray-600 rounded-lg hover:bg-gray-100 hover:text-orange-500 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
        </svg>

        <template x-if="cartCount > 0">
            <span
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
                x-text="cartCount">
            </span>
        </template>
    </button>

    <div x-show="openCart" @click="openCart = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-40 z-40">
    </div>

    <div x-show="openCart" x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 w-full max-w-md h-full bg-white shadow-2xl z-50 flex flex-col">

        <div class="p-4 border-b flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Keranjang Belanja</h2>
                <p class="text-sm text-gray-500">
                    <span x-text="cartCount">0</span> item dalam keranjang
                </p>
            </div>
            <button @click="openCart = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4">

            <template x-if="cartItems.length === 0">
                <div class="text-center text-gray-500 mt-10">
                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <p class="mt-2 font-semibold">Keranjang masih kosong</p>
                    <p class="text-sm">Yuk, tambahkan menu favoritmu!</p>
                </div>
            </template>

            <template x-for="item in cartItems" :key="item.id">
                <div class="flex gap-4 border-b pb-4">
                    <img :src="item.image || 'https://placehold.co/80x80'" :alt="item.name"
                        class="w-20 h-20 object-cover rounded-lg border">

                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="font-semibold text-gray-800" x-text="item.name"></h3>
                                <button @click.prevent="removeCartItem(item.id)"
                                    class="text-gray-400 hover:text-red-500 ml-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500" x-text="formatCurrency(item.price)"></p>
                            {{-- <p class="text-sm text-gray-400">Topping: ...</p> --}} {{-- (Bisa ditambahkan nanti) --}}
                        </div>

                        <div class="flex justify-between items-center mt-2">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button @click.prevent="updateCartQuantity(item.id, item.quantity - 1)"
                                    class="px-2 py-1 text-gray-600 hover:text-black disabled:opacity-50"
                                    :disabled="item.quantity <= 1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4"></path>
                                    </svg>
                                </button>

                                <span class="px-3 text-sm font-semibold" x-text="item.quantity"></span>

                                <button @click.prevent="updateCartQuantity(item.id, item.quantity + 1)"
                                    class="px-2 py-1 text-gray-600 hover:text-black">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>

                            <span class="text-md font-bold text-gray-800" x-text="formatCurrency(item.subtotal)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-4 border-t-2">
            <div class="flex justify-between mb-3 font-bold text-xl text-gray-800">
                <span>Total:</span>
                <span x-text="formatCurrency(cartTotal)"></span>
            </div>

            <button type="button"
                @click="
                    openCart = false;
                    checkoutModal = true;
                    errors = {};
                    // 'totalCartPrice' sudah di-set oleh fetchCart()
                "
                :disabled="cartItems.length === 0"
                :class="cartItems.length === 0 ? 'bg-gray-300 cursor-not-allowed' :
                    'bg-orange-500 hover:bg-orange-600 text-white'"
                class="w-full py-3 px-4 rounded-lg font-bold text-lg transition">
                Checkout
            </button>
        </div>
    </div>
</div>
