<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Bootstrap Icons (Wajib untuk ikon Lonceng & Info) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body class="bg-gray-100 flex min-h-screen">

    @include('admin.sidebar')

    <main class="flex-1 p-8 bg-gray-100">
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-200">

            {{-- HEADER SECTION --}}
            <div class="flex justify-between items-start mb-4">

                {{-- Kiri: Logo & Judul --}}
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/logop.png') }}" alt="Wayouji Logo" class="h-10 w-10">
                    <h1 class="text-2xl font-bold text-gray-800">Mr.Wayojiai Admin</h1>
                </div>

                {{-- Kanan: Notifikasi & Waktu --}}
                <div class="flex items-center space-x-6">

                    {{-- ========================================== --}}
                    {{-- KOMPONEN NOTIFIKASI LONCENG --}}
                    {{-- ========================================== --}}
                    <div class="relative" x-data="{ open: false }">

                        {{-- Tombol Lonceng --}}
                        <button @click="open = !open"
                            class="relative p-2 text-gray-500 hover:text-orange-600 focus:outline-none transition">
                            <i class="bi bi-bell text-2xl"></i>

                            {{-- Badge Merah (Jumlah Belum Dibaca) --}}
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="open" @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 w-80 mt-2 origin-top-right bg-white rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50 border border-gray-100"
                            style="display: none;">

                            <div class="py-2">
                                {{-- Header Dropdown --}}
                                <div
                                    class="px-4 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                                    <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <a href="{{ route('notifications.readAll') }}"
                                            class="text-xs text-orange-600 hover:text-orange-800 font-medium">Tandai
                                            dibaca</a>
                                    @endif
                                </div>

                                {{-- List Notifikasi --}}
                                <div class="max-h-64 overflow-y-auto custom-scrollbar">
                                    @forelse(auth()->user()->notifications as $notification)
                                        <a href="{{ $notification->data['url'] ?? '#' }}"
                                            class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 {{ $notification->read_at ? '' : 'bg-orange-50' }}">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 mt-1">
                                                    {{-- Icon Dinamis dari Database --}}
                                                    <i
                                                        class="bi {{ $notification->data['icon'] ?? 'bi-info-circle' }} {{ $notification->data['color'] ?? 'text-gray-500' }} text-lg"></i>
                                                </div>
                                                <div class="ml-3 w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-800">
                                                        {{ $notification->data['title'] }}</p>
                                                    <p class="text-xs text-gray-600 mt-0.5">
                                                        {{ $notification->data['message'] }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-1 flex items-center">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-gray-400">
                                            <i class="bi bi-bell-slash text-2xl mb-2 block"></i>
                                            <span class="text-sm">Tidak ada notifikasi baru.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END NOTIFIKASI --}}

                    {{-- Jam & Tanggal --}}
                    <div class="text-right border-l pl-6 border-gray-300">
                        <p class="text-gray-600 text-sm" id="live-date">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                        <p class="text-gray-800 font-semibold text-lg">
                            <span id="live-time">{{ \Carbon\Carbon::now()->format('H:i:s') }}</span> WIB
                            <span class="text-green-500 text-xs ml-1 font-bold px-2 py-0.5 bg-green-100 rounded-full">●
                                Online</span>
                        </p>
                    </div>

                </div>
            </div>
            <hr class="my-4">

            {{-- KONTEN DASHBOARD --}}
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h2>

            {{-- ALERT STATIC (Opsional: Bisa dihapus jika sudah pakai notifikasi lonceng, tapi bagus untuk tetap ada) --}}
            @if (count($outOfStockProducts) > 0)
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r shadow-sm mb-6 flex items-start"
                    role="alert">
                    <i class="bi bi-exclamation-triangle-fill text-xl mr-3"></i>
                    <div>
                        <strong class="font-bold">Perhatian!</strong>
                        <span class="block sm:inline text-sm">Ada {{ count($outOfStockProducts) }} produk yang habis
                            stok.</span>
                        <div class="mt-2">
                            <a href="{{ route('admin.menu.index') }}"
                                class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-xs font-semibold transition">Kelola
                                Stok</a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Ringkasan Pendapatan --}}
            <div
                class="bg-gradient-to-r from-orange-500 to-yellow-500 text-white p-6 rounded-xl shadow-md flex justify-between items-center mb-8">
                <div>
                    <p class="text-sm font-light">Pendapatan Hari Ini</p>
                    <p class="text-4xl font-extrabold mt-1">@rupiah($totalRevenueToday)</p>
                    <p class="text-sm mt-2 flex items-center">
                        @if ($revenueChangePercentage >= 0)
                            <span
                                class="bg-white/20 px-2 py-0.5 rounded-full text-xs flex items-center w-fit backdrop-blur-sm">
                                <i class="bi bi-arrow-up-short text-lg mr-1"></i> +{{ abs($revenueChangePercentage) }}%
                            </span>
                        @else
                            <span
                                class="bg-white/20 px-2 py-0.5 rounded-full text-xs flex items-center w-fit backdrop-blur-sm">
                                <i class="bi bi-arrow-down-short text-lg mr-1"></i> {{ $revenueChangePercentage }}%
                            </span>
                        @endif
                        <span class="ml-2 opacity-80">dari kemarin</span>
                    </p>
                </div>
                <div>
                    <i class="bi bi-wallet2 text-6xl opacity-25"></i>
                </div>
            </div>

            {{-- Ringkasan Status Pesanan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div
                    class="bg-blue-50 p-5 rounded-xl shadow-sm border border-blue-200 text-center hover:shadow-md transition">
                    <p class="text-sm text-blue-600 mb-2 font-semibold">Pesanan Baru</p>
                    <p class="text-4xl font-bold text-blue-800">{{ $newOrdersCount }}</p>
                    <p class="text-xs text-blue-500 mt-2">Menunggu Konfirmasi</p>
                </div>
                <div
                    class="bg-yellow-50 p-5 rounded-xl shadow-sm border border-yellow-200 text-center hover:shadow-md transition">
                    <p class="text-sm text-yellow-600 mb-2 font-semibold">Sedang Diproses</p>
                    <p class="text-4xl font-bold text-yellow-800">{{ $processingOrdersCount }}</p>
                    <p class="text-xs text-yellow-500 mt-2">Hari ini</p>
                </div>
                <div
                    class="bg-green-50 p-5 rounded-xl shadow-sm border border-green-200 text-center hover:shadow-md transition">
                    <p class="text-sm text-green-600 mb-2 font-semibold">Pesanan Selesai</p>
                    <p class="text-4xl font-bold text-green-800">{{ $completedOrdersCount }}</p>
                    <p class="text-xs text-green-500 mt-2">Hari ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Pesanan Terbaru --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4 flex items-center">
                        <i class="bi bi-clock-history mr-2 text-orange-500"></i> Pesanan Terbaru
                    </h3>
                    <div class="space-y-3">
                        @forelse($latestOrders as $order)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-orange-50 transition">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">#{{ $order->order_code }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->customer_name ?? 'Pelanggan' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-orange-600 text-sm">@rupiah($order->total_price)</p>
                                    <p class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <div>
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-full
                                        {{ $order->status === 'new' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $order->status === 'process' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $order->status === 'done' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $order->status === 'cancel' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">Tidak ada pesanan terbaru.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Notifikasi Stok --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4 flex items-center">
                        <i class="bi bi-box-seam mr-2 text-red-500"></i> Stok Menipis
                    </h3>
                    <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar pr-2">
                        @forelse($outOfStockProducts as $product)
                            <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-100">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="h-10 w-10 object-cover rounded-md mr-3 border border-red-200">
                                <div class="flex-1">
                                    <p class="font-bold text-red-800 text-sm">{{ $product->name }}</p>
                                    <p class="text-xs text-red-600 font-semibold">Stok: 0</p>
                                </div>
                                <a href="{{ route('admin.menu.edit', $product->id) }}"
                                    class="text-xs bg-white border border-red-200 text-red-600 px-3 py-1 rounded hover:bg-red-600 hover:text-white transition">
                                    Restock
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="bi bi-check-circle-fill text-green-500 text-4xl mb-2 block"></i>
                                <p class="text-gray-500 text-sm">Semua stok aman!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        function updateClock() {
            const now = new Date();
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateString = now.toLocaleDateString('id-ID', dateOptions);
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(/\./g, ':');

            document.getElementById('live-date').innerText = dateString;
            document.getElementById('live-time').innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock(); // Run immediately
    </script>
</body>

</html>
