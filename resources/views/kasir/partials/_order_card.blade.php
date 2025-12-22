<div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition">

    <div class="p-4">
        {{-- HEADER --}}
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="font-bold text-gray-800">{{ $order->order_code }}</p>
                <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
            </div>

            {{-- STATUS BADGE --}}
            @php
                $statusStyle = [
                    'new' => 'bg-gray-100 text-gray-700',
                    'process' => 'bg-blue-100 text-blue-700',
                    'done' => 'bg-orange-100 text-orange-700',
                    'complete' => 'bg-green-100 text-green-700',
                    'cancel' => 'bg-red-100 text-red-700',
                ];
            @endphp

            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusStyle[$order->status] }}">
                {{ strtoupper($order->status) }}
            </span>
        </div>

        {{-- CUSTOMER --}}
        <div class="text-sm mb-3">
            <p class="font-medium text-gray-700">{{ $order->customer_name }}</p>
            <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
        </div>

        {{-- ITEMS --}}
        <div class="border-t border-b py-2 mb-3 space-y-1">
            @foreach ($order->orderItems as $item)
                <div class="flex justify-between text-sm">
                    <span>{{ $item->menu->name }}</span>
                    <span class="font-medium">x{{ $item->quantity }}</span>
                </div>
            @endforeach
        </div>

        {{-- FOOTER --}}
        <div class="space-y-3">
            <p class="text-lg font-bold text-gray-800">
                Rp {{ number_format($order->total_price, 0, ',', '.') }}
            </p>

            <form action="{{ route('kasir.orders.updateStatus', $order) }}"
                  method="POST"
                  class="flex flex-col gap-2 w-full">
                @csrf

                @if ($order->status == 'new')
                    <button type="submit"
                            name="status"
                            value="process"
                            class="w-full px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                        ✔ Konfirmasi Pembayaran
                    </button>

                    <button type="submit"
                            name="status"
                            value="cancel"
                            onclick="return confirm('Yakin ingin membatalkan pesanan ini?')"
                            class="w-full px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        ✖ Batalkan Pesanan
                    </button>

                @elseif ($order->status == 'process')
                    <button type="submit"
                            name="status"
                            value="done"
                            class="w-full px-4 py-2 text-sm font-semibold text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition">
                        🕒 Siap Diambil
                    </button>

                @elseif ($order->status == 'done')
                    <button type="submit"
                            name="status"
                            value="complete"
                            class="w-full px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                        ✔ Selesaikan
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>
