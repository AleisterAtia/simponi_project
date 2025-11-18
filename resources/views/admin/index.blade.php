<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Memuat Alpine.js untuk fitur interaktif --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom scrollbar untuk notifikasi stok */
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

    <!-- Sidebar -->@include('admin.sidebar')

    <!-- Main Content -->
    <main class="flex-1 p-8 bg-gray-100">
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    {{-- Logo Wayouji --}}
                    <img src="{{ asset('images/logop.png') }}" alt="Wayouji Logo" class="h-10 w-10">
                    {{-- Pastikan path logo benar --}}
                    <h1 class="text-2xl font-bold text-gray-800">Mr.Wayojiai Admin - Point of Sale</h1>
                </div>
                <div class="text-right">
                    <p class="text-gray-600 text-sm">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    <p class="text-gray-800 font-semibold text-lg">{{ \Carbon\Carbon::now()->format('H:i') }} WIB <span
                            class="text-green-500 text-sm ml-2">Online</span></p>
                </div>
            </div>
            <hr class="my-4">

            <h2 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h2>

            {{-- Produk Habis Stok Notifikasi --}}
            @if (count($outOfStockProducts) > 0)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6"
                    role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Ada {{ count($outOfStockProducts) }} produk yang habis stok:
                        @foreach ($outOfStockProducts as $product)
                            {{ $product->name }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </span>
                    <a href="{{ route('admin.menu.index') }}" class="text-red-800 underline ml-2">Kelola Stok</a>
                </div>
            @endif

            {{-- Ringkasan Pendapatan Hari Ini --}}
            <div
                class="bg-gradient-to-r from-orange-500 to-yellow-500 text-white p-6 rounded-xl shadow-md flex justify-between items-center mb-8">
                <div>
                    <p class="text-sm font-light">Pendapatan Hari Ini</p>
                    <p class="text-4xl font-extrabold mt-1">@rupiah($totalRevenueToday)</p>
                    <p class="text-sm mt-2">
                        @if ($revenueChangePercentage >= 0)
                            <span class="bg-green-600 px-2 py-0.5 rounded-full text-xs flex items-center w-fit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                +{{ abs($revenueChangePercentage) }}%
                            </span>
                        @else
                            <span class="bg-red-600 px-2 py-0.5 rounded-full text-xs flex items-center w-fit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                {{ $revenueChangePercentage }}%
                            </span>
                        @endif
                        dari kemarin
                    </p>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-75" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V3m0 9v3m0 3.01V21M6.758 4.242L.416 10.584m13.485-13.485L23.584 10.584M0 10.5h16m-6.002 0c1.11 0 2.08.402 2.599 1M12 10.5h12" />
                    </svg>
                </div>
            </div>

            {{-- Ringkasan Status Pesanan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 p-5 rounded-xl shadow-sm border border-blue-200 text-center">
                    <p class="text-sm text-blue-600 mb-2">Pesanan Baru</p>
                    <p class="text-4xl font-bold text-blue-800">{{ $newOrdersCount }}</p>
                    <p class="text-sm text-blue-500 mt-2">Menunggu Konfirmasi</p>
                </div>
                <div class="bg-yellow-50 p-5 rounded-xl shadow-sm border border-yellow-200 text-center">
                    <p class="text-sm text-yellow-600 mb-2">Sedang Diproses</p>
                    <p class="text-4xl font-bold text-yellow-800">{{ $processingOrdersCount }}</p>
                    <p class="text-sm text-yellow-500 mt-2">Hari ini</p>
                </div>
                <div class="bg-green-50 p-5 rounded-xl shadow-sm border border-green-200 text-center">
                    <p class="text-sm text-green-600 mb-2">Pesanan Selesai</p>
                    <p class="text-4xl font-bold text-green-800">{{ $completedOrdersCount }}</p>
                    <p class="text-sm text-green-500 mt-2">Hari ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Pesanan Terbaru --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Pesanan Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($latestOrders as $order)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-700">#{{ $order->order_code }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->customer_name ?? 'Pelanggan' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">@rupiah($order->total_price)</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <span
                                    class="px-3 py-1 text-sm font-semibold rounded-full
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status === 'done' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status === 'cancel' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">Tidak ada pesanan terbaru.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Notifikasi Stok (Produk Habis) --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Notifikasi Stok</h3>
                    <div class="space-y-4 max-h-60 overflow-y-auto custom-scrollbar">
                        @forelse($outOfStockProducts as $product)
                            <div class="flex items-center p-3 bg-red-50 rounded-lg">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                    class="h-12 w-12 object-cover rounded-lg mr-4">
                                <div>
                                    <p class="font-medium text-red-800">{{ $product->name }}</p>
                                    <p class="text-sm text-red-600">Stok habis!</p>
                                </div>
                                <a href="{{ route('admin.menu.edit', $product->id) }}"
                                    class="ml-auto text-orange-600 hover:underline text-sm">Perbarui</a>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">Semua stok aman!</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>

</html>
