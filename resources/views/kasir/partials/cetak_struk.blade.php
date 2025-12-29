{{-- Halaman ini TIDAK menggunakan @extends agar saat dicetak, layout utama tidak ikut terbawa --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $order->order_code ?? 'TRX-123456' }}</title>
    {{-- Memuat Tailwind CSS Anda --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Mengatur lebar container agar mirip kertas thermal (misal 80mm) */
        #receipt-container {
            width: 320px;
            /* Lebar yang cukup sempit untuk simulasi */
            margin: 20px auto;
            /* Memberi jarak di tengah untuk pratinjau */
        }

        /* CSS untuk garis putus-putus */
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        /* Media query khusus untuk mode cetak */
        @media print {
            body * {
                visibility: hidden;
            }

            #receipt-container,
            #receipt-container * {
                visibility: visible;
            }

            #receipt-container {
                width: 100%;
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }

            #receipt {
                font-size: 10px;
                /* Ukuran font lebih kecil saat dicetak */
                line-height: 1.2;
                padding: 0;
            }

            @page {
                margin: 0;
            }
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col items-center p-4">

    <div class="flex flex-col items-center mb-6 print:hidden">
        <h1 class="text-2xl font-bold mb-2">Pratinjau Struk Penjualan</h1>
        <p class="text-gray-600">Simulasi tampilan cetak printer thermal.</p>
    </div>

    {{-- Container Struk (Simulasi Kertas Thermal) --}}
    <div id="receipt-container" class="bg-white p-4 shadow-xl border border-gray-300">

        <div id="receipt" class="font-mono text-sm text-black">

            {{-- HEADER TOKO --}}
            <div class="text-center mb-3">
                <h2 class="font-bold text-lg uppercase leading-tight">MR Wayojiai</h2>
                <p class="text-xs">Jl. Pasar Baru</p>
                <p class="text-xs">Telp: 0812-3456-7890</p>
            </div>

            <div class="divider"></div>

            {{-- INFORMASI TRANSAKSI --}}
            <div class="text-xs mb-3 space-y-0.5">
                <p>No. Transaksi: **{{ $order->order_code ?? 'TRX-123456' }}**</p>
                <p>Tanggal: {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
                <p>Kasir: {{ auth()->user()->name ?? 'Kasir Offline' }}</p>
                <p>Pelanggan: {{ $order->customer_name ?? 'Pelanggan Umum' }}</p>
                @if ($order->customer_id)
                    <p class="font-bold">Member: {{ $order->customer_name }}</p>
                @endif
            </div>

            <div class="divider"></div>

            {{-- DAFTAR ITEM --}}
            <table class="w-full text-xs">
                <thead>
                    <tr>
                        <th class="text-left">Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                {{-- GANTI BAGIAN TABLE BODY DENGAN INI --}}
                <tbody>
                    {{-- Loop melalui Order Items --}}
                    @foreach ($order->orderItems as $item)
                        <tr>
                            {{-- Baris untuk Nama Item --}}
                            <td class="py-1 text-left" colspan="4">
                                <span class="font-bold">{{ $item->menu->name ?? 'Menu Dihapus' }}</span>

                                {{-- PERBAIKAN: TAMPILKAN TOPPING DI SINI --}}
                                @if ($item->toppings && $item->toppings->count() > 0)
                                    <div class="text-[10px] text-gray-500 ml-2">
                                        + {{ $item->toppings->pluck('name')->join(', ') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            {{-- Baris Detail Qty & Harga --}}
                            <td></td>
                            <td class="text-center">{{ $item->quantity }}</td>

                            {{-- Harga Satuan (Termasuk harga topping jika dihitung per item) --}}
                            {{-- Atau tampilkan harga base menu saja, tergantung logika bisnis Yang Mulia --}}
                            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>

                            {{-- Subtotal (Quantity * Harga per item + topping) --}}
                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="divider"></div>

            {{-- TOTAL DAN DISKON --}}
            <div class="text-xs space-y-0.5">

                {{-- 1. Subtotal Awal (Dari kolom subtotal Order) --}}
                <div class="flex justify-between">
                    <span>Subtotal Awal</span>
                    <span class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>

                {{-- 2. BARIS DISKON (Kondisional) --}}
                @if ($order->discount_amount > 0)
                    <div class="flex justify-between font-bold text-red-700">
                        <span>Diskon Member ({{ number_format($order->discount_percentage) }}%)</span>
                        <span class="text-right">— Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between font-bold text-sm mt-1 border-t border-dashed pt-1">
                    <span>TOTAL BAYAR</span>
                    {{-- 3. TOTAL AKHIR (Dari kolom total_price Order) --}}
                    <span class="text-right text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="divider"></div>

            {{-- DETAIL BAYAR --}}
            <div class="text-xs space-y-0.5">
                <div class="flex justify-between">
                    <span>Metode Bayar</span>
                    <span class="text-right font-bold">{{ $order->payment_method ?? 'Tunai' }}</span>
                </div>

                {{-- Detail uang diterima (Hanya untuk Cash) --}}
                @if (($order->payment_method ?? 'cash') === 'cash')
                    <div class="flex justify-between">
                        <span>Bayar</span>
                        {{-- Mengambil langsung dari database --}}
                        <span class="text-right">Rp {{ number_format($order->uang_diterima, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kembalian</span>
                        {{-- Mengambil langsung dari database --}}
                        <span class="text-right">Rp {{ number_format($order->kembalian, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <div class="divider"></div>

            {{-- FOOTER --}}
            <div class="text-center text-xs space-y-0.5 mt-3">
                <p class="font-bold">TERIMA KASIH ATAS KUNJUNGAN ANDA</p>
                @if ($order->customer_id)
                    {{-- Tambahkan info poin yang didapat jika ada --}}
                    <p>Poin bertambah: +{{ floor($order->total_price / 1000) }}</p>
                @endif
                <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
            </div>
        </div>
    </div>

    {{-- Tombol Cetak (hanya tampil saat pratinjau, sembunyi saat cetak) --}}
    <button onclick="printReceipt()"
        class="mt-6 bg-orange-500 text-white font-semibold py-2 px-4 rounded-lg shadow hover:bg-orange-600 transition duration-200 print:hidden">
        Simulasikan Cetak Struk
    </button>

    <script>
        function printReceipt() {
            const originalTitle = document.title;
            document.title = "Struk Pembayaran";

            window.print();

            setTimeout(() => {
                document.title = originalTitle;
            }, 100);
        }
    </script>
</body>

</html>
