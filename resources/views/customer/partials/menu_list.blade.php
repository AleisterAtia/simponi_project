<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 pb-12">
    @foreach($menus as $menu)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition duration-300">
        <div class="relative">
            <img src="{{ $menu->image_url ?? 'https://placehold.co/600x400/fff3e0/ff5722?text=' . urlencode($menu->name) }}" 
                 alt="{{ $menu->name }}" class="w-full h-56 object-cover bg-gray-100">
        </div>
        <div class="p-4">
            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $menu->name }}</h3>
            <p class="text-sm text-gray-600 mb-3">{{ $menu->description }}</p>
            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                <p class="text-2xl font-extrabold text-red-600">Rp {{ number_format($menu->price,0,',','.') }}</p>
                <button class="btn-add add-to-cart" data-menu-id="{{ $menu->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah</span>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
