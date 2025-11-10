@extends('admin.layout')

@section('title', 'Daftar Menu')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Manajemen Menu</h2>
        <a href="{{ route('admin.menu.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition duration-200 flex items-center space-x-2 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span>Tambah Menu</span>
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 border-l-4 border-green-500 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Daftar Menu</h3>

    <div class="grid grid-cols-10 text-sm font-semibold text-gray-500 border-b pb-2 mb-3">
        <div class="col-span-1">Gambar</div>
        <div class="col-span-3">Nama Produk</div>
        <div class="col-span-1">Kategori</div>
        <div class="col-span-1 text-right">Harga</div>
        <div class="col-span-1 text-center">Stok</div>
        <div class="col-span-1 text-center">Status</div>
        <div class="col-span-2 text-center">Aksi</div>
    </div>

    <div class="space-y-4">
        @foreach($menus as $menu)
        <div class="grid grid-cols-10 items-center border-b pb-4 pt-2 text-gray-800 hover:bg-gray-50 transition duration-100">
            
            <div class="col-span-1">
                <img src="{{ $menu->image_url ?? 'https://placehold.co/50x50/F8F8F8/B0B0B0?text=IMG' }}" alt="{{ $menu->name }}" class="w-12 h-12 object-cover rounded-md border">
            </div>

            <div class="col-span-3 pr-2">
                <p class="font-medium">{{ $menu->name }}</p>
            </div>

            <div class="col-span-1">
                <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-300">
                    {{ $menu->category->name ?? 'N/A' }}
                </span>
            </div>

            <div class="col-span-1 text-right font-semibold">
                Rp {{ number_format($menu->price, 0, ',', '.') }}
            </div>

            <div class="col-span-1 text-center text-sm font-medium">
                {{ $menu->stock }}
            </div>

            <div class="col-span-1 text-center">
                @php
                    // Logika Status: Jika stok > 0 DAN status adalah 'tersedia' -> Tersedia
                    $isAvailable = $menu->stock > 0 && strtolower($menu->status) == 'tersedia'; 
                    $badgeColor = $isAvailable ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                    $statusText = $isAvailable ? 'tersedia' : 'habis';
                @endphp
                <span id="status-badge-{{ $menu->id }}" class="text-xs font-semibold px-3 py-1 rounded-full {{ $badgeColor }}">
                    {{ $statusText }}
                </span>
            </div>

            <div class="col-span-2 flex justify-center space-x-3">
                
                {{-- Tombol Edit --}}
                <a href="{{ route('admin.menu.edit', $menu->id) }}" class="text-gray-500 hover:text-yellow-600 transition duration-150" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-1.75 2.121l-7.536 7.536L3 17l4.757-1.243 7.536-7.536-2.828-2.828z" />
                    </svg>
                </a>

                {{-- TOMBOL TOGGLE STATUS BARU (Menggantikan Tombol View / Ikon Mata) --}}
                <button 
                    id="toggle-btn-{{ $menu->id }}"
                    onclick="toggleStatus({{ $menu->id }}, {{ $isAvailable ? 'true' : 'false' }})" 
                    class="relative inline-flex items-center h-6 w-11 rounded-full focus:outline-none transition-colors duration-200 ease-in-out"
                    role="switch"
                    title="{{ $isAvailable ? 'Nonaktifkan' : 'Aktifkan' }}"
                    style="background-color: {{ $isAvailable ? '#10B981' : '#D1D5DB' }};" {{-- Warna Emerald/Green atau Gray --}}
                >
                    <span class="sr-only">Toggle Status</span>
                    <span 
                        aria-hidden="true" 
                        class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        style="transform: {{ $isAvailable ? 'translateX(20px)' : 'translateX(1px)' }}"
                    ></span>
                </button>
                
                {{-- Tombol Hapus --}}
                <form method="POST" action="{{ route('admin.menu.destroy', $menu->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-gray-500 hover:text-red-600 transition duration-150" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
            </div>

        </div>
        @endforeach

        @if($menus->isEmpty())
        <div class="text-center py-8 text-gray-500">Tidak ada data menu yang ditemukan.</div>
        @endif

    </div>

</div>

<script>
/**
 * Fungsi untuk mengubah status badge (tersedia/habis) pada UI dan mengirim permintaan ke server.
 * @param {number} menuId ID Menu yang akan diubah statusnya.
 * @param {boolean} currentStatus Status ketersediaan saat ini (true jika tersedia/on).
 */
function toggleStatus(menuId, currentStatus) {
    const newStatus = currentStatus ? 'habis' : 'tersedia'; // Toggle status
    
    fetch(`/admin/menu/${menuId}/toggle-status`, {
        method: 'POST',
        headers: {
            // Mengambil token CSRF dari meta tag atau input tersembunyi
            // CATATAN: Pastikan Anda memiliki tag <meta name="csrf-token" content="{{ csrf_token() }}"> di layout utama Anda agar ini berfungsi.
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '', 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Perbarui Badge Status
            const badge = document.getElementById(`status-badge-${menuId}`);
            badge.textContent = data.statusText;
            badge.className = `text-xs font-semibold px-3 py-1 rounded-full ${data.badgeColor}`;

            // Perbarui Tombol Toggle
            const button = document.getElementById(`toggle-btn-${menuId}`);
            const indicator = button.querySelector('span:last-child');
            
            // Perbarui atribut dan styling
            if (data.statusText === 'tersedia') {
                button.style.backgroundColor = '#10B981'; // Emerald/Green
                indicator.style.transform = 'translateX(20px)';
                button.setAttribute('onclick', `toggleStatus(${menuId}, true)`);
                button.setAttribute('title', 'Nonaktifkan');
            } else {
                button.style.backgroundColor = '#D1D5DB'; // Gray
                indicator.style.transform = 'translateX(1px)';
                button.setAttribute('onclick', `toggleStatus(${menuId}, false)`);
                button.setAttribute('title', 'Aktifkan');
            }
        } else {
            console.error('Gagal mengubah status:', data.message);
        }
    })
    .catch(err => {
        console.error('Terjadi kesalahan network atau server:', err);
        // Tampilkan pesan error jika request gagal
        alert('Gagal terhubung ke server. Pastikan rute dan controller sudah disiapkan!');
    });
}
</script>
@endsection
    <meta name="csrf-token" content="{{ csrf_token() }}">
