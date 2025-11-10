<aside class="w-64 min-h-screen bg-orange-500 text-white flex flex-col p-4 shadow-lg">

    <div class="flex items-center gap-3 p-3 mb-4">
        <span class="text-4xl">☕</span>
        <div>
            <h1 class="font-bold text-lg">Wayouji Kasir</h1>
            <p class="text-xs text-orange-100">Point of Sale</p>
        </div>
    </div>

    <nav class="flex-1 flex flex-col gap-2">
        <p class="text-xs font-semibold text-orange-200 uppercase mt-2 mb-1 px-3">Menu Kasir</p>

        {{-- Link Dashboard --}}
        <a href="{{ route('kasir.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                  {{ request()->routeIs('kasir.dashboard') ? 'bg-white text-orange-600 font-bold shadow' : 'hover:bg-orange-600' }}">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
            </svg>
            <span>Dashboard</span>
        </a>

        {{-- Link Input Pesanan --}}
        <a href="{{ route('kasir.orders.createManual') }}" {{-- Ganti # dengan route Anda --}}
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition hover:bg-orange-600">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            <span>Input Pesanan</span>
        </a>

        {{-- Link Pesanan Online --}}
        <a href="{{ route('kasir.orders.online') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                  {{ request()->routeIs('kasir.orders.online')
                      ? 'bg-white text-orange-600 font-bold shadow'
                      : 'hover:bg-orange-600' }}">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
            </svg>
            <span>Pesanan Online</span>
        </a>

        {{-- Link Pembayaran --}}
        <a href="#" {{-- Ganti # dengan route Anda --}}
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition hover:bg-orange-600">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h6m3-3.75l-3 3m0 0l-3-3m3 3V1.5m6 5.25h5.25m-5.25 2.25h5.25m-5.25 2.25h5.25m-5.25 2.25h5.25M6 16.5v3m3-3v3m3-3v3" />
            </svg>
            <span>Pembayaran</span>
        </a>

        {{-- Link Riwayat Transaksi --}}
        <a href="{{ route('kasir.riwayat') }}" {{-- Ganti # dengan route Anda --}}
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition hover:bg-orange-600">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Riwayat Transaksi</span>
        </a>
    </nav>

    <div class="mt-auto pt-4 border-t border-orange-400">
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2 bg-orange-100 rounded-full">
                <span class="text-2xl">🧑‍🍳</span> {{-- Ganti dengan Ikon Kasir --}}
            </div>
            <div>
                <p class="font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-orange-200">Shift 10:00 - 15:00</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-3 bg-white text-orange-600 font-bold px-4 py-2.5 rounded-lg hover:bg-gray-100 shadow">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
