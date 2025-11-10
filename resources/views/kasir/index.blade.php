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
            {{--
                CATATAN:
                Bagian "Pesanan Terbaru" ini juga perlu data dari controller.
                Anda bisa menambahkannya di query web.php nanti.
            --}}
            <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
                <ul class="divide-y divide-gray-200">
                    <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                        <div>
                            <p class="font-semibold text-gray-800">#CSH001 <span
                                    class="text-xs font-medium text-gray-500 ml-2">Manual</span></p>
                            <p class="text-sm text-gray-600">Walk-in Customer (1x Wayouji Signature Matcha)</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">Rp 10.000</p>
                            <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Baru</span>
                        </div>
                    </li>
                    <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                        <div>
                            <p class="font-semibold text-gray-800">#CSH002 <span
                                    class="text-xs font-medium text-gray-500 ml-2">Online</span></p>
                            <p class="text-sm text-gray-600">Fadhil Dzaky (1x Wayouji Signature Matcha)</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800">Rp 10.000</p>
                            <span
                                class="text-xs font-medium bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Selesai</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </div>
@endsection
