{{-- Menggunakan layout admin Anda yang sudah ada --}}
@extends('admin.layout')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="p-6"> {{-- Konten utama akan memiliki padding dari layout, tapi kita tambahkan di sini untuk konsistensi --}}

        <h1 class="text-3xl font-bold text-gray-900 mb-6">Laporan</h1>

        <div class="mb-6">
            <div class="flex space-x-2 bg-orange-50 p-1.5 rounded-lg max-w-md">
                <a href="{{ route('admin.reports', ['tab' => 'harian']) }}"
                    class="flex-1 text-center px-4 py-2 rounded-md font-semibold text-sm transition
                      {{ $tab == 'harian' ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-orange-100' }}">
                    Harian
                </a>
                <a href="{{ route('admin.reports', ['tab' => 'mingguan']) }}"
                    class="flex-1 text-center px-4 py-2 rounded-md font-semibold text-sm transition
                      {{ $tab == 'mingguan' ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-orange-100' }}">
                    Mingguan
                </a>
                <a href="{{ route('admin.reports', ['tab' => 'bulanan']) }}"
                    class="flex-1 text-center px-4 py-2 rounded-md font-semibold text-sm transition
                      {{ $tab == 'bulanan' ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-orange-100' }}">
                    Bulanan
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">@rupiah($totalPendapatan)</p>
                <p class="text-sm text-gray-400 mt-1">{{ $tanggalLabel }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTransaksi }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ $tanggalLabel }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <p class="text-sm font-medium text-gray-500">Rata-rata Harian</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">@rupiah($rataRataHarian)</p>
                <p class="text-sm text-gray-400 mt-1">Per hari (selama periode)</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-5">
                    Menu Terlaris ({{ ucfirst($tab) }})
                </h3>

                @if ($menuTerlaris->isEmpty())
                    <p class="text-gray-500">Belum ada data penjualan untuk periode ini.</p>
                @else
                    <ol class="space-y-4">
                        @foreach ($menuTerlaris as $index => $menu)
                            <li class="flex items-center justify-between space-x-4 p-3 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center space-x-4">
                                    <span
                                        class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-medium text-gray-700">{{ $menu->name }}</span>
                                </div>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold">
                                    {{ $menu->total_terjual }} terjual
                                </span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-5">Distribusi Penjualan</h3>

                @if ($distribusiPenjualan->isEmpty())
                    <p class="text-gray-500">Belum ada data distribusi.</p>
                @else
                    <div class="w-full h-10 flex rounded-lg overflow-hidden my-4">
                        @foreach ($distribusiPenjualan as $item)
                            <div class="{{ $item['color'] }}" style="width: {{ $item['percentage'] }}%"
                                title="{{ $item['name'] }} ({{ round($item['percentage'], 1) }}%)">
                            </div>
                        @endforeach
                    </div>

                    <ul class="space-y-2 mt-4">
                        @foreach ($distribusiPenjualan as $item)
                            <li class="flex items-center space-x-2 text-sm">
                                <span class="w-3 h-3 rounded-full {{ $item['color'] }}"></span>
                                <span class="text-gray-600">{{ $item['name'] }}</span>
                                <span class="font-medium text-gray-800">({{ $item['total'] }} item)</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Jika Anda ingin menggunakan chart.js di masa depan, tambahkan script di sini --}}
@endpush
