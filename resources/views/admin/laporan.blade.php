@extends('admin.layout')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="p-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Laporan Penjualan</h1>

            {{-- Form Filter Tanggal --}}
            <form action="{{ route('admin.reports') }}" method="GET"
                class="flex items-center space-x-2 bg-white p-2 rounded-lg shadow-sm border border-gray-200">

                {{-- Input Tanggal Mulai --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-calendar text-gray-400"></i>
                    </div>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full pl-10 p-2.5"
                        placeholder="Dari Tanggal">
                </div>

                <span class="text-gray-500">-</span>

                {{-- Input Tanggal Akhir --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-calendar text-gray-400"></i>
                    </div>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full pl-10 p-2.5"
                        placeholder="Sampai Tanggal">
                </div>

                <button type="submit"
                    class="text-white bg-orange-500 hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-4 py-2.5">
                    <i class="bi bi-filter"></i> Filter
                </button>

                {{-- Reset Button --}}
                @if (request('start_date') || request('end_date'))
                    <a href="{{ route('admin.reports') }}"
                        class="text-gray-600 bg-gray-100 hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-4 py-2.5"
                        title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Tab Navigasi Cepat (Harian/Mingguan/Bulanan) --}}
        <div class="mb-8">
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <a href="{{ route('admin.reports', ['tab' => 'harian']) }}"
                    class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-l-lg {{ $tab == 'harian' ? 'bg-orange-500 text-white' : 'bg-white text-gray-900 hover:bg-gray-100 hover:text-orange-700' }}">
                    Hari Ini
                </a>
                <a href="{{ route('admin.reports', ['tab' => 'mingguan']) }}"
                    class="px-4 py-2 text-sm font-medium border-t border-b border-gray-200 {{ $tab == 'mingguan' ? 'bg-orange-500 text-white' : 'bg-white text-gray-900 hover:bg-gray-100 hover:text-orange-700' }}">
                    Minggu Ini
                </a>
                <a href="{{ route('admin.reports', ['tab' => 'bulanan']) }}"
                    class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-r-lg {{ $tab == 'bulanan' ? 'bg-orange-500 text-white' : 'bg-white text-gray-900 hover:bg-gray-100 hover:text-orange-700' }}">
                    Bulan Ini
                </a>
            </div>

            {{-- Info Periode Aktif --}}
            <div class="mt-2 text-sm text-gray-500">
                <i class="bi bi-info-circle me-1"></i> Menampilkan data: <span
                    class="font-semibold text-gray-700">{{ $tanggalLabel }}</span>
            </div>
        </div>

        {{-- Kartu Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">@rupiah($totalPendapatan)</p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-lg text-green-600">
                        <i class="bi bi-cash-stack text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTransaksi }}</p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                        <i class="bi bi-receipt text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Rata-rata Harian</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">@rupiah($rataRataHarian)</p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                        <i class="bi bi-graph-up-arrow text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Menu Terlaris --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center">
                    <i class="bi bi-star-fill text-yellow-400 mr-2"></i> Menu Terlaris
                </h3>

                @if ($menuTerlaris->isEmpty())
                    <div class="text-center py-10">
                        <i class="bi bi-inbox text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-500">Belum ada data penjualan untuk periode ini.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($menuTerlaris as $index => $menu)
                            <div
                                class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition border border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $menu->name }}</p>
                                        <p class="text-xs text-gray-500">Pendapatan: @rupiah($menu->pendapatan)</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="block text-lg font-bold text-gray-900">{{ $menu->total_terjual }}</span>
                                    <span class="text-xs text-gray-500">Terjual</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Distribusi Penjualan --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center">
                    <i class="bi bi-pie-chart-fill text-blue-500 mr-2"></i> Kategori
                </h3>

                @if ($distribusiPenjualan->isEmpty())
                    <p class="text-gray-500 text-center py-10">Belum ada data distribusi.</p>
                @else
                    <div class="w-full h-4 bg-gray-100 rounded-full overflow-hidden flex mb-6">
                        @foreach ($distribusiPenjualan as $item)
                            <div class="{{ $item['color'] }}" style="width: {{ $item['percentage'] }}%"
                                title="{{ $item['name'] }} ({{ round($item['percentage'], 1) }}%)"></div>
                        @endforeach
                    </div>

                    <div class="space-y-3">
                        @foreach ($distribusiPenjualan as $item)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full {{ $item['color'] }} mr-2"></span>
                                    <span class="text-gray-600">{{ $item['name'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium text-gray-900">{{ $item['total'] }} item</span>
                                    <span class="text-xs text-gray-400">({{ round($item['percentage'], 1) }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
