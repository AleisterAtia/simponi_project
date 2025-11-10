{{--
    $order -> data pesanan
    $color -> warna tema (blue, orange, green)
--}}
<div class="border-l-4 rounded-lg bg-gray-50 border border-gray-200 shadow-sm"
    style="border-left-color: {{ $color == 'blue' ? '#3B82F6' : ($color == 'orange' ? '#F97316' : '#16A34A') }}">
    <div class="p-4">
        <div class="flex justify-between items-center mb-2">
            <span class="font-bold text-gray-800">{{ $order->order_code }}</span>
            <span class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</span>
        </div>
        <p class="text-sm font-medium text-gray-700">{{ $order->customer_name }}</p>
        <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>

        <div class="border-t my-3 py-2 space-y-1">
            @foreach ($order->orderItems as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700">{{ $item->menu->name }}</span>
                    <span class="text-gray-600 font-medium">x{{ $item->quantity }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex justify-between items-center mt-2">
            <span class="text-lg font-bold text-gray-800">Rp
                {{ number_format($order->total_price, 0, ',', '.') }}</span>

            <div>
                <form action="{{ route('kasir.orders.updateStatus', $order) }}" method="POST">
                    @csrf

                    @if ($order->status == 'new')
                        {{-- Jika status BARU, tombolnya "Konfirmasi" -> ubah ke "process" --}}
                        <input type="hidden" name="status" value="process">
                        <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            Konfirmasi
                        </button>
                    @elseif($order->status == 'process')
                        {{-- Jika status DIPROSES, tombolnya "Siap" -> ubah ke "done" --}}
                        <input type="hidden" name="status" value="done">
                        <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600">
                            Siap Diambil
                        </button>
                    @elseif($order->status == 'done')
                        {{-- Jika status SIAP, tombolnya "Selesai" -> ubah ke "cancel" --}}
                        <input type="hidden" name="status" value="done">
                        <button type="submit"
                            class="px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Selesai
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
