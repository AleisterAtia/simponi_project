@extends('kasir.kasir') {{-- Ganti ini dengan layout kasir Anda --}}

@section('content')
    <div class="p-6 md:p-10">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Pesanan Online</h1>
                <p class="text-gray-500">Kelola pesanan pelanggan yang masuk melalui QR</p>
            </div>
            {{-- Kita bisa buat auto-refresh ini berfungsi nanti --}}
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                </span>
                <span>Auto Refresh</span>
            </div>
        </div>

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Kolom Kanban (Kanban Columns) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="p-4 border-b flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0l-8 5-8-5">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Menunggu Konfirmasi</h3>
                    <span
                        class="bg-blue-100 text-blue-800 text-sm font-bold px-2.5 py-0.5 rounded-full">{{ $newOrders->count() }}</span>
                </div>
                <div class="p-4 space-y-4 h-96 overflow-y-auto">
                    @forelse($newOrders as $order)
                        @include('kasir.partials._order_card', ['order' => $order, 'color' => 'blue'])
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada pesanan baru.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="p-4 border-b flex items-center space-x-3">
                    <div class="p-2 bg-orange-100 rounded-full">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 18.657A8 8 0 016.343 7.343m11.314 11.314a8 8 0 00-11.314-11.314m11.314 11.314L6.343 7.343">
                            </path>
                            <path d="M9 17a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Sedang Diproses</h3>
                    <span
                        class="bg-orange-100 text-orange-800 text-sm font-bold px-2.5 py-0.5 rounded-full">{{ $processingOrders->count() }}</span>
                </div>
                <div class="p-4 space-y-4 h-96 overflow-y-auto">
                    @forelse($processingOrders as $order)
                        @include('kasir.partials._order_card', ['order' => $order, 'color' => 'orange'])
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada pesanan diproses.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="p-4 border-b flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Siap Diambil</h3>
                    <span
                        class="bg-green-100 text-green-800 text-sm font-bold px-2.5 py-0.5 rounded-full">{{ $readyOrders->count() }}</span>
                </div>
                <div class="p-4 space-y-4 h-96 overflow-y-auto">
                    @forelse($readyOrders as $order)
                        @include('kasir.partials._order_card', ['order' => $order, 'color' => 'green'])
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada pesanan siap.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
