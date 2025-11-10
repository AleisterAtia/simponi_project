@extends('kasir.kasir')

@section('content')
    <div class="p-6 md:p-10">

        <div>
            <h2 class="text-3xl font-bold text-gray-800">Riwayat Transaksi</h2>
            <p class="text-gray-600">Lihat dan kelola riwayat transaksi harian</p>
        </div>

        <div class="grid grid-cols-1 gap-5 mt-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="p-5 bg-orange-500 rounded-lg shadow text-white">
                <p class="text-sm font-medium text-orange-100">Total Pendapatan (Filtered)</p>
                <p class="text-3xl font-bold">@rupiah($totalPendapatan)</p>
            </div>

            <div class="p-5 bg-white rounded-lg shadow">
                <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalTransaksi }}</p>
            </div>

            <div class="p-5 bg-white rounded-lg shadow">
                <p class="text-sm font-medium text-gray-500">Total Tunai</p>
                <p class="text-3xl font-bold text-gray-800">@rupiah($totalTunai)</p>
            </div>

            <div class="p-5 bg-white rounded-lg shadow">
                <p class="text-sm font-medium text-gray-500">Total QRIS</p>
                <p class="text-3xl font-bold text-gray-800">@rupiah($totalQris)</p>
            </div>
        </div>

        <div class="p-6 mt-8 bg-white rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Filter dan Pencarian</h3>

            <form action="{{ route('kasir.riwayat') }}" method="GET">
                <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-600">Cari</label>
                        <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="ID Transaksi / Nama Customer"
                            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-600">Metode
                            Pembayaran</label>
                        <select name="payment_method" id="payment_method"
                            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Semua Metode</option>
                            <option value="cash" @if ($filters['payment_method'] ?? '' == 'cash') selected @endif>Tunai</option>
                            <option value="qris" @if ($filters['payment_method'] ?? '' == 'qris') selected @endif>QRIS</option>
                        </select>
                    </div>

                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-600">Periode</label>
                        <input type="date" name="period" id="period"
                            value="{{ $filters['period'] ?? today()->format('Y-m-d') }}"
                            class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit"
                            class="w-full px-4 py-2 font-semibold text-white bg-orange-500 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                            Filter
                        </button>
                        <a href="{{ route('kasir.riwayat') }}"
                            class="w-full px-4 py-2 font-semibold text-center text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="mt-8 overflow-hidden bg-white rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700">Daftar Transaksi</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">ID
                                Transaksi</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Waktu
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Item
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Metode Bayar</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Kasir
                            </th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900">{{ $order->order_code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->orderItems->count() }} Item(s)</div>
                                    <div class="text-sm text-gray-500">
                                        {{-- Tampilkan nama item pertama --}}
                                        {{ $order->orderItems->first()->product->name ?? 'Item tidak diketahui' }}
                                        @if ($order->orderItems->count() > 1)
                                            <span class="text-xs">(dan lainnya)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900">@rupiah($order->total_price)</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($order->payment_method == 'cash')
                                        <span
                                            class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                            Tunai
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">
                                            QRIS
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $order->user->name ?? 'N/A' }} {{-- Mengambil nama kasir dari relasi --}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="#" class="font-medium text-orange-600 hover:text-orange-900">Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada transaksi untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
