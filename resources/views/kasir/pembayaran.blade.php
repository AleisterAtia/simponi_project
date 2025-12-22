@extends('kasir.kasir') {{-- Sesuaikan dengan layout kasir oranye Anda --}}

@section('content')
    {{-- 1. PERBAIKAN UTAMA: Hitung Subtotal di awal untuk digunakan Alpine --}}
    @php
        // $items, $subtotal, $total, $discount_amount, $discount_percentage, $customer, $customer_id 
        // sudah tersedia dari Controller
        $totalTanpaPajak = $subtotal; // Subtotal Awal

        // Gunakan fungsi untuk formatCurrency di JS
        $jsSubtotal = $subtotal; 
        $jsTotalAkhir = $total;
    @endphp

    <div class="p-6 md:p-10" x-data="{
        // Menggunakan nilai total akhir yang sudah didiskon
        total: {{ $jsTotalAkhir }}, 
        paymentMethod: 'cash',
        uangDiterima: '',
        kembalian: 0,
        
        // Fungsi ini akan menghitung kembalian secara real-time
        calculateChange() {
            let diterima = parseFloat(this.uangDiterima) || 0;
            let kembali = diterima - this.total; // 'this.total' kini adalah Total Akhir (setelah diskon)
            this.kembalian = kembali >= 0 ? kembali : 0; 
        },
        
        // Fungsi untuk tombol uang pas
        setUang(amount) {
            this.uangDiterima = amount;
            this.calculateChange();
        }
    }">

        <form action="{{ route('kasir.pembayaran.process') }}" method="POST">
            @csrf

            {{-- ========================================================= --}}
            {{-- ➡️ INPUT TERSEMBUNYI WAJIB (Nilai yang Dihitung di Backend) ⬅️ --}}
            {{-- ========================================================= --}}
            <input type="hidden" name="subtotal" value="{{ $subtotal }}">
            <input type="hidden" name="discount_percentage" value="{{ $discount_percentage }}">
            <input type="hidden" name="discount_amount" value="{{ $discount_amount }}">
            <input type="hidden" name="customer_id" value="{{ $customer_id ?? '' }}">
            
            {{-- Input tersembunyi untuk mengirim data ITEMS ke controller --}}
            @foreach ($items as $index => $item)
                <input type="hidden" name="items[{{ $index }}][menu_id]" value="{{ $item['menu_id'] }}">
                <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                <input type="hidden" name="items[{{ $index }}][price]" value="{{ $item['price'] }}">
            @endforeach
            
            {{-- Mengirim Total Akhir (Nilai yang harus dibayar) --}}
            <input type="hidden" name="total_price" :value="total"> 
            
            {{-- Mengirim Uang Diterima dan Kembalian (Dari Alpine) --}}
            <input type="hidden" name="uang_diterima" x-model="uangDiterima">
            <input type="hidden" name="kembalian" x-model="kembalian">
            

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('kasir.orders.createManual') }}" class="text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Pembayaran</h1>
                    <p class="text-gray-500">Proses Pembayaran Pesanan Pelanggan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 h-fit">
                    <h2 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3 mb-4">
                        {{-- ULANGI LOOP ITEM --}}
                        @foreach ($items as $item)
                            @php
                                $itemSubtotal = $item['price'] * $item['quantity'];
                                // Ambil nama menu dari Model, karena $items hanya array data
                                $menuName = \App\Models\Menu::find($item['menu_id'])->name ?? 'Nama Menu';
                            @endphp
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-semibold">{{ $menuName }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['quantity'] }} x @rupiah($item['price'])</p>
                                </div>
                                <p class="font-semibold">@rupiah($itemSubtotal)</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Awal:</span>
                            <span class="font-medium">@rupiah($subtotal)</span>
                        </div>
                        
                        {{-- TAMPILAN DISKON --}}
                        @if ($discount_amount > 0)
                            <div class="flex justify-between text-red-600 font-semibold">
                                <span>Diskon Member ({{ $discount_percentage }}%):</span>
                                <span>— @rupiah($discount_amount)</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-2xl font-bold text-gray-900 mt-2 pt-2 border-t">
                            <span>TOTAL BAYAR:</span>
                            <span class="text-orange-600" x-text="formatCurrency(total)"></span> 
                        </div>
                    </div>
                </div>


                <div class="space-y-8">

                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Informasi Pelanggan</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700">Nama
                                    Pelanggan</label>
                                <input type="text" name="customer_name" id="customer_name" required
                                    placeholder="Nama Pelanggan" 
                                    value="{{ old('customer_name', $customer->name ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 @error('customer_name') border-red-500 @enderror">
                                <small class="text-xs text-green-600 mt-1">
                                    @if ($customer) Member: {{ $customer->member_code }} @endif
                                </small>
                                @error('customer_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700">Nomor
                                    Telepon</label>
                                <input type="tel" name="customer_phone" id="customer_phone" required
                                    placeholder="0812..." 
                                    value="{{ old('customer_phone', $customer->phone ?? '') }}"
                                    class="w-full mt-1 rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 @error('customer_phone') border-red-500 @enderror">
                                @error('customer_phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 border-b pb-3 mb-4">Metode Pembayaran</h2>

                        <div class="space-y-4">
                            <label class="block p-4 border rounded-lg cursor-pointer transition"
                                :class="paymentMethod === 'cash' ? 'border-orange-500 bg-orange-50' : 'border-gray-300'">
                                <input type="radio" name="payment_method" value="cash" x-model="paymentMethod"
                                    class="sr-only">
                                <div class="flex items-center">
                                    <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center mr-3"
                                        :class="paymentMethod === 'cash' ? 'border-orange-500' : 'border-gray-400'">
                                        <span x-show="paymentMethod === 'cash'"
                                            class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-lg text-gray-800">Tunai</d>
                                        <p class="text-sm text-gray-500">Pembayaran dengan uang cash</p>
                                    </div>
                                </div>
                            </label>

                            <label class="block p-4 border rounded-lg cursor-pointer transition"
                                :class="paymentMethod === 'qris' ? 'border-orange-500 bg-orange-50' : 'border-gray-300'">
                                <input type="radio" name="payment_method" value="qris" x-model="paymentMethod"
                                    class="sr-only">
                                <div class="flex items-center">
                                    <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center mr-3"
                                        :class="paymentMethod === 'qris' ? 'border-orange-500' : 'border-gray-400'">
                                        <span x-show="paymentMethod === 'qris'"
                                            class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-lg text-gray-800">QRIS</p>
                                        <p class="text-sm text-gray-500">Pembayaran digital via QR code</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div x-show="paymentMethod === 'cash'" x-transition class="mt-6 pt-6 border-t">
                            <label for="uang_diterima" class="block text-sm font-medium text-gray-700 mb-2">Uang
                                Diterima</label>

                            {{-- MENGHITUNG LIVE DENGAN @input --}}
                            <input type="number" id="uang_diterima" x-model.number="uangDiterima"
                                @input="calculateChange()"
                                class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-lg"
                                placeholder="Masukkan jumlah uang">

                            <div class="flex flex-wrap gap-2 mt-3">
                                <button type="button" @click="setUang(total)" class="btn-uang">Uang Pas</button>
                                <button type="button" @click="setUang(50000)" class="btn-uang">Rp 50.000</button>
                                <button type="button" @click="setUang(100000)" class="btn-uang">Rp 100.000</button>
                            </div>

                            <div class="flex justify-between text-lg font-semibold text-gray-800 mt-4 pt-4 border-t">
                                <span>Kembalian:</span>
                                <span class="text-green-600" x-text="formatCurrency(kembalian)">Rp 0</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" {{-- Tombol nonaktif jika bayar tunai tapi uangnya kurang --}}
                                :disabled="paymentMethod === 'cash' && (uangDiterima === '' || parseFloat(
                                        uangDiterima) < total)"
                                class="w-full bg-orange-500 text-white font-bold py-3 px-4 rounded-lg transition hover:bg-orange-600
                                             disabled:bg-gray-300 disabled:cursor-not-allowed">
                                Proses Pembayaran
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Helper CSS untuk tombol uang --}}
    @push('styles')
        <style>
            .btn-uang {
                padding: 6px 12px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                background-color: #f9f9f9;
                transition: all 0.2s ease;
            }

            .btn-uang:hover {
                background-color: #f0f0f0;
                border-color: #ccc;
            }
        </style>
    @endpush

    {{-- Helper JS untuk format mata uang --}}
    @push('scripts')
        <script>
            // Tambahkan fungsi formatCurrency ke window agar bisa diakses Alpine
            function formatCurrency(value) {
                if (isNaN(value)) {
                    value = 0;
                }
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }

            // Pasang di Alpine
            document.addEventListener('alpine:init', () => {
                // Daftarkan 'formatCurrency' sebagai fungsi global di Alpine
                Alpine.magic('formatCurrency', () => {
                    return (value) => formatCurrency(value);
                });

                // Anda juga bisa mendaftarkannya di data komponen jika perlu
                // (tapi cara di atas lebih bersih)
            })
        </script>
    @endpush
@endsection