{{-- FILE: resources/views/kasir/partials/_online_orders_grid.blade.php --}}

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    {{-- KOLOM 1: MENUNGGU KONFIRMASI --}}
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-4 border-b flex items-center space-x-3 bg-blue-50 rounded-t-lg">
            <div class="p-2 bg-blue-100 rounded-full">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0l-8 5-8-5">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">Menunggu Konfirmasi</h3>
            <span class="bg-blue-200 text-blue-800 text-sm font-bold px-2.5 py-0.5 rounded-full">
                {{ $newOrders->count() }}
            </span>
        </div>
        <div class="p-4 space-y-4 h-[calc(100vh-300px)] overflow-y-auto">
            @forelse($newOrders as $order)
                @include('kasir.partials._order_card', ['order' => $order, 'color' => 'blue'])
            @empty
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                    <p>Belum ada pesanan baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- KOLOM 2: SEDANG DIPROSES --}}
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-4 border-b flex items-center space-x-3 bg-orange-50 rounded-t-lg">
            <div class="p-2 bg-orange-100 rounded-full">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 18.657A8 8 0 016.343 7.343m11.314 11.314a8 8 0 00-11.314-11.314m11.314 11.314L6.343 7.343">
                    </path>
                    <path d="M9 17a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">Sedang Diproses</h3>
            <span class="bg-orange-200 text-orange-800 text-sm font-bold px-2.5 py-0.5 rounded-full">
                {{ $processingOrders->count() }}
            </span>
        </div>
        <div class="p-4 space-y-4 h-[calc(100vh-300px)] overflow-y-auto">
            @forelse($processingOrders as $order)
                @include('kasir.partials._order_card', ['order' => $order, 'color' => 'orange'])
            @empty
                <p class="text-gray-400 text-center py-10">Tidak ada pesanan diproses.</p>
            @endforelse
        </div>
    </div>

    {{-- KOLOM 3: SIAP DIAMBIL --}}
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="p-4 border-b flex items-center space-x-3 bg-green-50 rounded-t-lg">
            <div class="p-2 bg-green-100 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">Siap Diambil</h3>
            <span class="bg-green-200 text-green-800 text-sm font-bold px-2.5 py-0.5 rounded-full">
                {{ $readyOrders->count() }}
            </span>
        </div>
        <div class="p-4 space-y-4 h-[calc(100vh-300px)] overflow-y-auto">
            @forelse($readyOrders as $order)
                @include('kasir.partials._order_card', ['order' => $order, 'color' => 'green'])
            @empty
                <p class="text-gray-400 text-center py-10">Tidak ada pesanan siap diambil.</p>
            @endforelse
        </div>
    </div>

</div>
