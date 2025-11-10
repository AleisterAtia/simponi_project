@extends('layouts.customer_layout') {{-- Sesuaikan dengan nama layout Anda --}}

@section('content')
    {{--
    Komponen Alpine.js untuk Halaman Tracking.
    Kita passing status awal & ID order dari Blade ke Alpine.
--}}
    <div class="max-w-md mx-auto my-8 p-4 md:p-6 rounded-lg" style="background-color: #F7F5F2;" x-data="orderTracker('{{ $order->status }}', {{ $order->id }})"
        x-init="listenForUpdates()">

        <!-- 1. Nomor Pesanan -->
        <div class="text-center bg-white p-5 rounded-lg shadow-sm border mb-5">
            <p class="text-sm text-gray-500">Nomor Pesanan</p>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wider">{{ $order->order_code }}</h1>
            <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('d/m/Y, H:i:s') }}</p>
        </div>

        <!-- 2. Status & Progress Bar -->
        <div class="bg-white p-5 rounded-lg shadow-sm border mb-5">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-full mr-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    {{-- Teks status akan real-time --}}
                    <h2 class="font-semibold text-lg" x-text="statusText"></h2>
                    <p class="text-sm text-gray-500" x-text="statusSubtitle"></p>
                </div>
            </div>
            <!-- Progress Bar Real-time -->
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-4">
                <div class="bg-orange-600 h-2.5 rounded-full transition-all duration-500"
                    :style="`width: ${progressPercentage}%`"></div>
            </div>
            <p class="text-xs text-right text-gray-500 mt-1" x-text="`${progressPercentage}%`"></p>
        </div>

        <!-- 3. Timeline Pesanan -->
        <div class="bg-white p-5 rounded-lg shadow-sm border mb-5">
            <h3 class="font-semibold mb-4 text-gray-700">Timeline Pesanan</h3>
            <ul class="space-y-4">

                <!-- Step 1: Pesanan Diterima (new) -->
                <li class="flex items-center" :class="{ 'opacity-50': !isStatusActive('new') }">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white"
                        :class="isStatusActive('new') ? 'bg-orange-600' : 'bg-gray-300'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <span class="font-medium" :class="isStatusActive('new') ? 'text-gray-800' : 'text-gray-500'">Pesanan
                            Diterima</span>
                        <p class="text-xs text-gray-500" x-show="isStatusActive('new')">Pesanan Anda telah dikonfirmasi.</p>
                    </div>
                </li>

                <!-- Step 2: Sedang Diproses (process) -->
                <li class="flex items-center" :class="{ 'opacity-50': !isStatusActive('process') }">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white"
                        :class="isStatusActive('process') ? 'bg-orange-600' : 'bg-gray-300'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <span class="font-medium"
                            :class="isStatusActive('process') ? 'text-gray-800' : 'text-gray-500'">Sedang Diproses</span>
                        <p class="text-xs text-gray-500" x-show="isStatusActive('process')">Tim kitchen sedang menyiapkan
                            pesanan Anda.</p>
                    </div>
                </li>

                <!-- Step 3: Siap Diambil (done) -->
                <li class="flex items-center" :class="{ 'opacity-50': !isStatusActive('done') }">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white"
                        :class="isStatusActive('done') ? 'bg-orange-600' : 'bg-gray-300'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <span class="font-medium" :class="isStatusActive('done') ? 'text-gray-800' : 'text-gray-500'">Siap
                            Diambil</span>
                        <p class="text-xs text-gray-500" x-show="isStatusActive('done')">Pesanan sudah siap! Silakan ambil
                            di counter.</p>
                    </div>
                </li>

                <!-- Step 4: Selesai (cancel - asumsi 'cancel' adalah 'selesai/diambil') -->
                <li class="flex items-center" :class="{ 'opacity-50': !isStatusActive('cancel') }">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white"
                        :class="isStatusActive('cancel') ? 'bg-orange-600' : 'bg-gray-300'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <span class="font-medium"
                            :class="isStatusActive('cancel') ? 'text-gray-800' : 'text-gray-500'">Selesai</span>
                        <p class="text-xs text-gray-500" x-show="isStatusActive('cancel')">Pesanan telah diselesaikan.
                            Terima kasih!</p>
                    </div>
                </li>
            </ul>
        </div>

        <!-- 4. Detail Pesanan (Statik) -->
        <div class="bg-white p-5 rounded-lg shadow-sm border">
            <h3 class="font-semibold mb-4 text-gray-700">Detail Pesanan</h3>
            <div class="space-y-1 text-sm text-gray-600 mb-3">
                <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
                <p><strong>Telepon:</strong> {{ $order->customer_phone }}</p>
            </div>
            <div class="border-t pt-3">
                @php $subtotal = 0; @endphp
                @foreach ($order->orderItems as $item)
                    @php $subtotal += $item->subtotal; @endphp
                    <div class="flex justify-between items-center text-sm mb-2">
                        <div>
                            <p class="font-medium">{{ $item->menu->name }}</p>
                            <p class="text-gray-500">1x @ Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
            <div class="border-t pt-3 mt-3 space-y-1 text-sm">
                @php
                    $pajak = $subtotal * 0.1; // Pajak 10%
                    $total = $order->total_price; // Ambil total dari DB
                @endphp
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pajak (10%):</span>
                    <span class="font-medium text-gray-800">Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t">
                <span>Total:</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
        <a href="{{ route('landingpage') }}"
            class="inline-block w-full bg-orange-600 text-white font-semibold text-center py-3 px-6 rounded-lg shadow hover:bg-orange-700 transition duration-200 mt-6">
            Kembali ke Halaman Menu
        </a>
    </div>

    {{-- SCRIPT UNTUK REAL-TIME TRACKING --}}
    <script>
        function orderTracker(initialStatus, orderId) {
            return {
                orderId: orderId,
                currentStatus: initialStatus,

                // Definisikan status dan urutannya
                // (Sesuaikan string 'new', 'process' dengan DB Anda)
                statusConfig: {
                    'new': {
                        text: 'Pesanan Diterima',
                        subtitle: 'Pesanan Anda telah dikonfirmasi.',
                        progress: 25,
                        order: 1
                    },
                    'process': {
                        text: 'Sedang Diproses',
                        subtitle: 'Tim kitchen sedang menyiapkan pesanan Anda.',
                        progress: 50,
                        order: 2
                    },
                    'done': {
                        text: 'Siap Diambil',
                        subtitle: 'Pesanan sudah siap! Silakan ambil di counter.',
                        progress: 75,
                        order: 3
                    },
                    'cancel': {
                        text: 'Selesai',
                        subtitle: 'Pesanan telah diselesaikan. Terima kasih!',
                        progress: 100,
                        order: 4
                    },
                },

                // Getter untuk UI yang reaktif
                get statusText() {
                    return this.statusConfig[this.currentStatus]?.text || 'Menunggu Update';
                },
                get statusSubtitle() {
                    return this.statusConfig[this.currentStatus]?.subtitle || '...';
                },
                get progressPercentage() {
                    return this.statusConfig[this.currentStatus]?.progress || 0;
                },

                // Fungsi untuk cek timeline
                isStatusActive(status) {
                    const currentOrder = this.statusConfig[this.currentStatus]?.order || 0;
                    const checkOrder = this.statusConfig[status]?.order || 0;
                    return currentOrder >= checkOrder;
                },

                // Fungsi untuk mendengarkan Broadcast
                listenForUpdates() {
                    // Pastikan resources/js/bootstrap.js sudah di-load
                    if (window.Echo) {
                        window.Echo.private(`order.${this.orderId}`)
                            .listen('OrderStatusUpdated', (event) => {
                                console.log('Status baru diterima:', event.order.status);
                                // Ini adalah intinya:
                                // Update status di Alpine, dan seluruh UI akan otomatis berubah
                                this.currentStatus = event.order.status;
                            });
                    } else {
                        console.error("Laravel Echo not configured.");
                    }
                }
            }
        }
    </script>

    @if (session('order_success'))
        <div {{-- Inisialisasi Alpine.js. 'showModal' true karena session ada --}} x-data="{ showModal: true }" x-show="showModal" style="display: none;" {{-- Sembunyikan awalnya agar Alpine bisa ambil alih --}}
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showModal = false" class="fixed inset-0 bg-black bg-opacity-60" x-show="showModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div class="relative w-full max-w-md p-8 bg-white rounded-lg shadow-xl" x-show="showModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center w-20 h-20 mb-5 bg-green-100 rounded-full">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Berhasil!</h2>

                    {{-- Menampilkan pesan dari controller --}}
                    <p class="text-gray-600 mb-6">
                        {{ session('order_success') }}
                        <br>
                        Status pesanan Anda akan diperbarui secara real-time.
                    </p>

                    {{-- Tombol untuk menutup modal --}}
                    <button @click="showModal = false"
                        class="w-full px-4 py-3 font-semibold text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition">
                        OK, Mengerti
                    </button>
                </div>
            </div>
        </div>
    @endif
@endsection
