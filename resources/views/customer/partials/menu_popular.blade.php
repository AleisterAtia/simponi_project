<h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2 mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9.049 2.927c.3-.921 1.62-.921 1.92 0l1.24 3.84a1 1 0 00.95.69h4.04c.97 0 1.37 1.24.58 1.81l-3.27 2.37a1 1 0 00-.36 1.11l1.24 3.84c.3.92-1.62.92-1.92 0l-3.27-2.37a1 1 0 00-1.11 0l-3.27 2.37c-.3.22-1.2.22-1.5 0l1.24-3.84a1 1 0 00-.36-1.11l-3.27-2.37c-.79-.57-.39-1.81.58-1.81h4.04a1 1 0 00.95-.69l1.24-3.84z"/>
    </svg>
    <span>Menu Populer</span>
</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-12">
    @forelse($popularMenus as $item)
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition duration-200">
        <div class="flex items-center p-3">
            <img src="{{ $item->image_url ?? 'https://placehold.co/80x80/ffe4e6/ff5722?text=IMG' }}" alt="{{ $item->name }}" class="w-16 h-16 rounded-lg object-cover mr-4">
            <div class="flex-grow">
                <h3 class="font-semibold text-gray-800">{{ $item->name }}</h3>
                <p class="text-md text-red-600 font-bold">Rp {{ number_format($item->price,0,',','.') }}</p>
            </div>
            <button class="btn-add add-to-cart" data-menu-id="{{ $item->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>
    @empty
        <p class="text-gray-500 col-span-3">Belum ada menu populer.</p>
    @endforelse
</div>
