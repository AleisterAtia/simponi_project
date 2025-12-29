@extends('kasir.kasir') {{-- <-- Saya perbaiki ini ke layout yang benar --}}

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Kasir</h1>
            <p class="text-gray-500">Pantau pesanan dan kelola transaksi harian</p>
        </div>
        <a href="#"
            class="bg-orange-500 text-white font-semibold px-4 py-2 rounded-lg hover:bg-orange-600 transition flex items-center gap-2">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Pesanan Baru</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-orange-400 text-white p-6 rounded-xl shadow-lg">
            <p class="text-sm text-orange-100 font-medium">Pendapatan Harian</p>
            {{-- PERUBAHAN: Menampilkan data --}}
            <p class="text-3xl font-extrabold mt-2">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <p class="text-sm text-gray-500 font-medium">Pesanan Baru</p>
            {{-- PERUBAHAN: Menampilkan data --}}
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $newOrderCount }}</p>
            <p class="text-xs text-gray-500">Menunggu Konfirmasi</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <p class="text-sm text-gray-500 font-medium">Sedang Diproses</p>
            {{-- PERUBAHAN: Menampilkan data --}}
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $processingOrderCount }}</p>
            <p class="text-xs text-gray-500">Dalam Persiapan</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <p class="text-sm text-gray-500 font-medium">Selesai Hari Ini</p>
            {{-- PERUBAHAN: Menampilkan data --}}
            <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ $doneOrderCount }}</p>
            <p class="text-xs text-gray-500">Pesanan Selesai</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="flex flex-col gap-3">
                <a href="{{ route('kasir.orders.createManual') }}"
                    class="w-full text-center p-4 bg-orange-500 text-white font-semibold rounded-lg shadow hover:bg-orange-600 transition">
                    Input Manual Pesanan
                </a>
                <a href="{{ route('kasir.orders.online') }}"
                    class="w-full text-center p-4 bg-white border-2 border-orange-500 text-orange-500 font-semibold rounded-lg hover:bg-orange-50 transition">
                    Lihat Pesanan Online
                </a>
                <a href="#"
                    class="w-full text-center p-4 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    Proses Pembayaran
                </a>
            </div>
        </div>

        <div class="lg:col-span-2">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Pesanan Terbaru</h2>
    
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <ul class="divide-y divide-gray-200">
            @forelse($recentOrders as $order)
                <li class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                    <div>
                        {{-- Kode Order & Tipe --}}
                        <p class="font-semibold text-gray-800">
                            #{{ $order->order_code }}
                            <span class="text-xs font-medium px-2 py-0.5 rounded ml-2 
                                {{ $order->order_type == 'online' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($order->order_type ?? 'Manual') }}
                            </span>
                        </p>

                        {{-- Nama Pelanggan & Detail Item Singkat --}}
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-medium">{{ $order->customer_name }}</span>
                            <span class="text-gray-400 mx-1">|</span>
                            
                            {{-- Logika Menampilkan Menu Pertama + Sisa Item --}}
                            @php
                                $firstItem = $order->orderItems->first();
                                $otherItemsCount = $order->orderItems->count() - 1;
                            @endphp

                            @if($firstItem && $firstItem->menu)
                                {{ $firstItem->menu->name }} (x{{ $firstItem->quantity }})
                                @if($otherItemsCount > 0)
                                    <span class="text-xs font-bold text-gray-500">+{{ $otherItemsCount }} lainnya</span>
                                @endif
                            @else
                                <span>Item dihapus</span>
                            @endif
                        </p>
                    </div>

                    <div class="text-right">
                        {{-- Total Harga --}}
                        <p class="font-semibold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        
                        {{-- Badge Status Warna-Warni --}}
                        @php
                            $statusColor = match($order->status) {
                                'pending' => 'bg-blue-100 text-blue-800',
                                'processing' => 'bg-yellow-100 text-yellow-800',
                                'done', 'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            
                            $statusLabel = match($order->status) {
                                'pending' => 'Baru',
                                'processing' => 'Diproses',
                                'done', 'completed' => 'Selesai',
                                'cancelled' => 'Batal',
                                default => ucfirst($order->status)
                            };
                        @endphp

                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="p-8 text-center text-gray-500">
                    Belum ada pesanan hari ini.
                </li>
            @endforelse
        </ul>
    </div>
</div>

    </div>
@endsection
