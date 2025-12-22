<div x-show="loginModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">

    <div x-show="loginModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="loginModalOpen" @click.away="loginModalOpen = false" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="loginModalOpen = false" type="button"
                    class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl font-bold leading-6 text-gray-900 mb-2" id="modal-title">Login Member</h3>
                        <p class="text-sm text-gray-500 mb-6">Masukkan nomor HP yang terdaftar sebagai member.</p>

                        <form action="{{ route('member.login.post') }}" method="POST">
                            @csrf
                            <div class="mb-4 text-left">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor
                                    HP</label>
                                <input type="text" name="phone" id="phone"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 bg-gray-50 focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                    placeholder="08xxxxxxxxxx" required>
                            </div>

                            <button type="submit"
                                class="w-full justify-center rounded-md bg-orange-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:w-full transition">
                                Login Sekarang
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
